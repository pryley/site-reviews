import ServerSideRender from '@wordpress/server-side-render';
import { _x } from '@wordpress/i18n';
import { InspectorControls, InspectorAdvancedControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, Disabled } from '@wordpress/components';
import { useCallback, useEffect, useRef } from '@wordpress/element';

const RenderedBlock = ({
    className = 'ssr',
    inspectorAdvancedControls = {},
    inspectorControls = {},
    name,
    namespace = 'site-reviews',
    props,
    renderCallback,
}) => {
    const { attributes } = props;
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
                {Object.values(wp.hooks.applyFilters(`${namespace}.${name}.InspectorControls`, inspectorControls, props))}
            </PanelBody>
        ),
    };

    return (
        <>
            <InspectorControls>
                {Object.values(wp.hooks.applyFilters(`${namespace}.${name}.InspectorPanels`, inspectorPanels, props))}
            </InspectorControls>
            <InspectorAdvancedControls>
                {Object.values(wp.hooks.applyFilters(`${namespace}.${name}.InspectorAdvancedControls`, inspectorAdvancedControls, props))}
            </InspectorAdvancedControls>
            <div {...useBlockProps({ ref })}>
                <Disabled isDisabled>
                    <ServerSideRender
                        attributes={attributes}
                        block={`${namespace}/${name}`}
                        className={className}
                        skipBlockSupportAttributes
                    />
                </Disabled>
            </div>
        </>
    );
};

export default RenderedBlock;
