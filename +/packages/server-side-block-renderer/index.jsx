import ServerSideRender from '@wordpress/server-side-render';
import { _x } from '@wordpress/i18n';
import { InspectorControls, InspectorAdvancedControls, useBlockProps } from '@wordpress/block-editor';
import { Disabled, PanelBody, Spinner } from '@wordpress/components';
import { useCallback, useEffect, useMemo, useRef } from '@wordpress/element';
import { BaseControl, Notice } from '@wordpress/components';

const CustomLoadingPlaceholder = ({ children, showLoader }) => {
    return (
        children ? (
            <div style={{ position: 'relative' }}>
                { showLoader && (
                    <div style={{
                        position: 'absolute',
                        top: '50%',
                        left: '50%',
                        marginTop: '-9px',
                        marginLeft: '-9px',
                    }}>
                        <Spinner />
                    </div>
                ) }
                <div style={{ opacity: showLoader ? '0.3' : 1 }}>
                    { children }
                </div>
            </div>
        ) : (
            <div className="block-editor-warning">
                <Spinner style={{ marginBlockStart: 0, marginInlineStart: 0 }} />
                <p className="block-editor-warning__message">
                    { _x('Loading block...', 'admin-text', 'site-reviews') }
                </p>
            </div>
        )
    )
};

const defaultPanelTitles = {
    hide: _x('Hide Options', 'admin-text', 'site-reviews'),
    settings: _x('Settings', 'admin-text', 'site-reviews'),
};

const allowedInspectorGroups = [
    'advanced',
    'background',
    'bindings',
    'border',
    'color',
    'default',
    'dimensions',
    'effects',
    'filter',
    'list',
    'position',
    'styles',
    'typography',
];

const ServerSideBlockRenderer = ({
    className = 'ssr',
    controls = {},
    panels = {},
    props,
    renderCallback,
    style = {},
}) => {
    const { attributes, name: blockName } = props;
    const hookPrefix = blockName.replace('/', '.');
    const ref = useRef(null);

    const blockProps = useBlockProps({
        ref,
        style,
    });

    // Use useCallback to memoize the observer callback
    const observerCallback = useCallback((mutations) => {
        for (const mutation of mutations) {
            if ('childList' !== mutation.type) continue
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement) || !node.classList.contains(className)) continue;
                const block = node.firstElementChild;
                block.classList.add('glsr-' + window.getComputedStyle(block, null).getPropertyValue('direction'))
                if (GLSR?.Event) {
                    GLSR.Event.trigger(blockName, block, attributes)
                }
                if ('function' === typeof renderCallback) {
                    renderCallback(node, ref.current)
                }
                return
            }
        }
    }, [renderCallback, className]);

    useEffect(() => {
        if (!ref.current) return
        const observer = new MutationObserver(observerCallback);
        observer.observe(ref.current, {
            childList: true,
            subtree: true,
        })
        return () => observer.disconnect()
    }, [observerCallback])

    const filteredControls = useMemo(
        () => wp.hooks.applyFilters(`${hookPrefix}.InspectorControls`, controls, props),
        [controls, props, hookPrefix]
    );
    const filteredPanels = useMemo(
        () => wp.hooks.applyFilters(`${hookPrefix}.InspectorPanels`, panels, props),
        [panels, props, hookPrefix]
    );

    const normalizedPanels = Object.entries(filteredPanels).reduce((acc, [panelKey, panel]) => {
        const group = allowedInspectorGroups.includes(panel.group || panelKey)
            ? (panel.group || panelKey)
            : 'default';
        const normalizedPanel = {
            ...panel,
            controls: Array.isArray(panel.controls) ? panel.controls : [],
            title: panel.title || defaultPanelTitles[panelKey] || null,
        };
        if (0 === normalizedPanel.controls.length) {
            return acc;
        }
        acc[group] = acc[group] || {};
        acc[group][panelKey] = normalizedPanel;
        return acc;
    }, {});

    const renderControls = (controlsArray, context = 'unknown') => {
        return controlsArray
            .filter((controlKey) => controlKey in filteredControls)
            .map((controlKey) => filteredControls[controlKey]);
    }

    return (
        <>
            {Object.entries(normalizedPanels).map(([group, panels]) => {
                return (
                    <InspectorControls group={group}>
                        {Object.entries(panels).map(([panelKey, panel]) => {
                            const { controls, ...panelProps } = panel; // Exclude controls
                            if (group === panelKey) {
                                return renderControls(controls, panelKey);
                            }
                            return (
                                <PanelBody {...panelProps}>
                                    {renderControls(controls, panelKey)}
                                </PanelBody>
                            )
                        })}
                    </InspectorControls>
                )
            })}
            <div {...blockProps}>
                <Disabled isDisabled>
                    <ServerSideRender
                        attributes={ attributes }
                        block={ blockName }
                        className={ className }
                        LoadingResponsePlaceholder={ CustomLoadingPlaceholder }
                        skipBlockSupportAttributes
                    />
                </Disabled>
            </div>
        </>
    );
};

export default ServerSideBlockRenderer;
