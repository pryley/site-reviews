import ServerSideRender from '@wordpress/server-side-render';
import { _x } from '@wordpress/i18n';
import { InspectorControls, InspectorAdvancedControls, useBlockProps } from '@wordpress/block-editor';
import { Disabled, PanelBody, Spinner } from '@wordpress/components';
import { useCallback, useEffect, useRef } from '@wordpress/element';

const RenderedBlock = ({
    className = 'ssr',
    inspectorAdvancedControls = {},
    inspectorControls = {},
    props,
    renderCallback,
}) => {
    const { attributes, name: blockName } = props;
    const hookPrefix = blockName.replace('/', '.');
    const ref = useRef(null);

    // Use useCallback to memoize the observer callback
    const observerCallback = useCallback((mutations) => {
        for (const mutation of mutations) {
            if ('childList' !== mutation.type) continue
            for (const node of mutation.addedNodes) {
                if ('DIV' !== node.tagName || !node.classList.contains(className)) continue
                if ('function' === typeof renderCallback) {
                    const block = node.firstElementChild;
                    block.classList.add('glsr-' + window.getComputedStyle(block, null).getPropertyValue('direction'))
                    renderCallback(node, ref.current)
                }
                return
            }
        }
    }, [renderCallback]);

    useEffect(() => {
        if (!ref.current) return
        const observer = new MutationObserver(observerCallback);
        observer.observe(ref.current, {
            childList: true,
            subtree: true,
        })
        return () => observer.disconnect()
    }, [observerCallback])

    const inspectorPanels = {
        panel_settings: (
            <PanelBody title={_x('Settings', 'admin-text', 'site-reviews')}>
                {Object.values(wp.hooks.applyFilters(`${hookPrefix}.InspectorControls`, inspectorControls, props))}
            </PanelBody>
        ),
    };

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

    return (
        <>
            <InspectorControls>
                {Object.values(wp.hooks.applyFilters(`${hookPrefix}.InspectorPanels`, inspectorPanels, props))}
            </InspectorControls>
            <InspectorAdvancedControls>
                {Object.values(wp.hooks.applyFilters(`${hookPrefix}.InspectorAdvancedControls`, inspectorAdvancedControls, props))}
            </InspectorAdvancedControls>
            <div {...useBlockProps({ ref })}>
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

export default RenderedBlock;
