import apiFetch from '@wordpress/api-fetch';
import {
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
    InspectorControls,
    useBlockProps,
    withColors,
} from '@wordpress/block-editor';
import { _x } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { RawHTML, useEffect, useMemo, useState } from '@wordpress/element';
import { safeHTML } from '@wordpress/dom';

const Edit = (props) => {
    const { attributes, clientId, context, name, setAttributes, setStyleRatingColor, styleRatingColor } = props;
    const { postId } = context;
    const colorGradientSettings = useMultipleOriginColorsAndGradients();
    const [ratings, setRatings] = useState({});
    const defaultText = _x('{num} customer reviews', 'admin-text', 'site-reviews');
    const defaultLinkUrl = '#product-reviews';

    const blockProps = useBlockProps({
        className: (attributes.styleRatingColorCustom || styleRatingColor.slug) ? 'has-custom-color' : '',
        style: {
            '--glsr-rating-star-bg': styleRatingColor.slug ? `var(--wp--preset--color--${styleRatingColor.slug})` : attributes.styleRatingColorCustom,
        }
    });

    const endpoint = useMemo(
        () => addQueryArgs('/site-reviews/v1/summary/stars', {
            _block: name,
            assigned_posts: postId,
        }),
        [postId]
    );

    const text = useMemo(
        () => (attributes.text || defaultText).replace('{num}', ratings.reviews || '0'),
        [attributes.text, ratings]
    );

    useEffect(() => {
        (async () => {
            try {
                const response = await apiFetch({ path: endpoint });
                setRatings(response);
            } catch (error) {
                console.error('Error fetching product rating:', error);
            }
        })();
    }, [endpoint]);

    return (
        <>
            <InspectorControls group="color">
                <ColorGradientSettingsDropdown
                    __experimentalIsRenderedInSidebar
                    panelId={clientId}
                    settings={ [
                        {
                            clearable: true,
                            colorValue: styleRatingColor.color || attributes.styleRatingColorCustom,
                            label: _x('Rating', 'admin-text', 'site-reviews'),
                            onColorChange: (color) => {
                                setAttributes({ styleRatingColorCustom: color })
                                setStyleRatingColor(color)
                            },
                            isShownByDefault: true,
                            resetAllFilter: () => ({
                                styleRatingColor: '',
                                styleRatingColorCustom: '',
                            }),
                        }
                    ] }
                    {...colorGradientSettings}
                />
            </InspectorControls>
            <InspectorControls>
                <PanelBody>
                    <ToggleControl
                        __nextHasNoMarginBottom
                        label={_x('Display text after the rating', 'admin-text', 'site-reviews')}
                        checked={attributes.has_text}
                        onChange={(has_text) => setAttributes({ has_text })}
                    />
                    {attributes.has_text && (
                        <>
                            <ToggleControl
                                __nextHasNoMarginBottom
                                checked={attributes.is_link}
                                label={_x('Make text a link', 'admin-text', 'site-reviews')}
                                onChange={(is_link) => setAttributes({ is_link })}
                            />
                            <TextControl
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                                help={_x('Use the {num} placeholder to display the number of reviews.', 'admin-text', 'site-reviews')}
                                label={_x('Text', 'admin-text', 'site-reviews')}
                                onChange={(text) => setAttributes({ text: text || defaultText })}
                                placeholder={defaultText}
                                value={attributes.text || defaultText}
                            />
                            {attributes.is_link && (
                                <TextControl
                                    __next40pxDefaultSize
                                    __nextHasNoMarginBottom
                                    help={_x('#product-reviews is the default HTML anchor used in the Product Reviews block.', 'admin-text', 'site-reviews')}
                                    label={_x('URL', 'admin-text', 'site-reviews')}
                                    onChange={(link_url) => setAttributes({ link_url: link_url || defaultLinkUrl })}
                                    placeholder={defaultLinkUrl}
                                    value={attributes.link_url || defaultLinkUrl}
                                />
                            )}
                        </>
                    )}
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                {ratings?.rendered && (
                    <RawHTML data-style={ratings?.args?.theme}>
                        {safeHTML(ratings.rendered)}
                    </RawHTML>
                )}
                {attributes.has_text && (
                    attributes.is_link ? (
                        <a href={attributes.link_url}>{text}</a>
                    ) : (
                        <span>{text}</span>
                    )
                )}
            </div>
        </>
    );
}

export default withColors('styleRatingColor')(Edit)
