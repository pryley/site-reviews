import ServerSideRender from '@wordpress/server-side-render';
import { __experimentalToolsPanel as ToolsPanel, Disabled, PanelBody, Spinner } from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import { BaseControl, Notice } from '@wordpress/components';
import { BlockControls, InspectorControls, InspectorAdvancedControls, useBlockProps } from '@wordpress/block-editor';
import { useMemo } from '@wordpress/element';
import { useRefEffect } from '@wordpress/compose';

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
    display: _x('Display Options', 'admin-text', 'site-reviews'),
    hide: _x('Hide Options', 'admin-text', 'site-reviews'),
    settings: _x('Settings', 'admin-text', 'site-reviews'),
};

const allowedInspectorGroups = [
    'advanced',
    'background',
    'bindings',
    'block', // special group for block toolbar controls
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
    styleClassNames = [],
}) => {
    const { attributes, name: blockName } = props;
    const hookPrefix = blockName.replace('/', '.');

    const ref = useRefEffect((block) => {
        const observer = new MutationObserver((mutations, observer) => {
            for (let mutation of mutations) {
                for (let node of mutation.addedNodes) {
                    if (node.tagName == 'DIV' && node.classList.contains(className)) {
                        const el = node.firstElementChild;
                        const iframe = block?.ownerDocument?.defaultView;
                        el.classList.add('glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction'))
                        if (iframe?.GLSR_init) {
                            iframe.GLSR_init(blockName, el, attributes)
                        }
                        if ('function' === typeof renderCallback) {
                            renderCallback(block, iframe)
                        }
                    }
                }
            }
        });
        observer.observe(block, {
            childList: true,
            subtree: true,
        });
        return () => {
            observer.disconnect();
        }
    }, []);

    const memoizedSSR = useMemo(() => {
        return (
            <ServerSideRender
                attributes={attributes}
                block={blockName}
                className={className}
                LoadingResponsePlaceholder={CustomLoadingPlaceholder}
                skipBlockSupportAttributes
            />
        )
    }, [attributes]);

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

    const renderControls = (controlsArray) => {
        return controlsArray
            .filter((controlKey) => controlKey in filteredControls)
            .map((controlKey) => filteredControls[controlKey]);
    }

    const blockProps = useBlockProps({
        className: styleClassNames.join(' '),
        ref,
        style,
    });

    return (
        <>
            {Object.entries(normalizedPanels).map(([group, panels]) => {
                if ('block' === group) {
                    return (
                        <BlockControls group={group}>
                            {Object.entries(panels).map(([panelKey, panel]) => renderControls(panel.controls))}
                        </BlockControls>
                    )
                }
                return (
                    <InspectorControls group={group}>
                        {Object.entries(panels).map(([panelKey, panel]) => {
                            const { controls, ...panelProps } = panel; // Exclude controls
                            if (group === panelKey) {
                                return renderControls(controls);
                            }
                            if (panelProps.resetAll) {
                                const { title: label, ...toolsPanelProps } = panelProps;
                                return (
                                    <ToolsPanel label={label} {...toolsPanelProps}>
                                        {renderControls(controls)}
                                    </ToolsPanel>
                                )
                            }
                            return (
                                <PanelBody {...panelProps}>
                                    {renderControls(controls)}
                                </PanelBody>
                            )
                        })}
                    </InspectorControls>
                )
            })}
            <div {...blockProps}>
                <Disabled isDisabled>
                    {memoizedSSR}
                </Disabled>
            </div>
        </>
    );
};

export default ServerSideBlockRenderer;
