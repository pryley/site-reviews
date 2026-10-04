import { default as ServerSideRender } from '@wordpress/server-side-render';
import { Disabled, Spinner } from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import { applyFilters, doAction } from '@wordpress/hooks';
import { useBlockProps } from '@wordpress/block-editor';
import { useMemo } from '@wordpress/element';
import { useRefEffect } from '@wordpress/compose';
import BlockPanels from './block-panels.jsx';

export { BlockPanels };

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

const ServerSideBlockRenderer = ({
    className = 'ssr',
    controls = {},
    panels = {},
    props,
    style = {},
    styleClassNames = [],
}) => {
    const { attributes, name: blockName } = props;

    doAction('site-reviews.blocks.edit', props);

    const ref = useRefEffect((block) => {
        const observer = new MutationObserver((mutations, observer) => {
            for (let mutation of mutations) {
                for (let node of mutation.addedNodes) {
                    if (node.tagName == 'DIV' && node.classList.contains(className)) {
                        const el = node.firstElementChild;
                        const iframe = block?.ownerDocument?.defaultView;
                        el.classList.add('glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction'))
                        if (iframe?.GLSR_init) {
                            iframe.GLSR_init(el)
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
                // skipBlockSupportAttributes
            />
        )
    }, [attributes]);

    const filteredStyle = useMemo(
        () => applyFilters('site-reviews.blocks.style', style, props),
        [style, props]
    );
    const filteredStyleClassNames = useMemo(
        () => applyFilters('site-reviews.blocks.style_classnames', styleClassNames, props),
        [styleClassNames, props]
    );

    const blockProps = useBlockProps({
        className: filteredStyleClassNames.join(' '),
        ref,
        style: filteredStyle,
    });

    const classNamePrefixesToRemove = [
        'is-custom-',
        'is-style-',
        'items-justified-',
    ];

    const memoizedClassName = useMemo(() => {
        return (attributes.className || '')
            .split(' ')
            .filter(className => !classNamePrefixesToRemove.some(prefix => className.startsWith(prefix)))
            .join(' ')
            .trim();
    }, [attributes.className]);

    if (attributes.className) {
        // don't add custom classNames to root
        blockProps.className = blockProps.className.replace(memoizedClassName, '')
    }

    return (
        <>
            <BlockPanels controls={controls} panels={panels} props={props} />
            <div {...blockProps}>
                <Disabled isDisabled>
                    {memoizedSSR}
                </Disabled>
            </div>
        </>
    );
};

export default ServerSideBlockRenderer;
