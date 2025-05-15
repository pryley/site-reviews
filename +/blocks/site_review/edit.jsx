import {
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
    withColors,
} from '@wordpress/block-editor';
import {
    __experimentalToolsPanelItem as ToolsPanelItem,
    __experimentalUnitControl as UnitControl,
    __experimentalUseCustomUnits as useCustomUnits,
    TextControl,
} from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import { AjaxSearchControl, AjaxToggleGroupControl } from '@site-reviews/components';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, clientId, setAttributes, setStyleRatingColor, styleRatingColor } = props;
    const colorGradientSettings = useMultipleOriginColorsAndGradients();

    const controls = {
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_review?option=hide'
            key='hide'
            onChange={ (hide) => setAttributes({ hide }) }
            value={ attributes.hide }
        />,
        id: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('This should be a unique value.', 'admin-text', 'site-reviews') }
            key='id'
            label={ _x('Custom ID', 'admin-text', 'site-reviews') }
            onChange={ (id) => setAttributes({ id }) }
            value={ attributes.id }
        />,
        post_id: <AjaxSearchControl
            endpoint='/site-reviews/v1/shortcode/site_review?option=post_id'
            help={ _x('Search for a review to display.', 'admin-text', 'site-reviews') }
            key='post_id'
            label={ _x('Review Post ID', 'admin-text', 'site-reviews') }
            onChange={ (post_id) => setAttributes({ post_id }) }
            placeholder={ _x('Search Reviews...', 'admin-text', 'site-reviews') }
            value={ attributes.post_id }
        />,
        styleRatingColor: <ColorGradientSettingsDropdown
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
        />,
        styleStarSize: <ToolsPanelItem
            hasValue={ () => '1.25em' !== attributes.styleStarSize }
            isShownByDefault
            label={ _x('Star Size', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleStarSize: '1.25em' }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                allowReset
                isResetValueOnUnitChange
                label={ _x('Star Size', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (styleStarSize) => setAttributes({ styleStarSize }) }
                units={ useCustomUnits({
                    availableUnits: ['px', 'em', 'rem'],
                    defaultValues: { px: '20', em: '1.25', rem: '1.25' },
                }) }
                value={ attributes.styleStarSize }
            />
        </ToolsPanelItem>,
    };

    const panels = { // order is intentional
        settings: {
            controls: [
                'post_id',
            ],
        },
        display: {
            controls: [],
            initialOpen: false,
        },
        hide: {
            controls: [
                'hide',
            ],
            initialOpen: false,
        },
        advanced: {
            controls: [
                'id',
            ],
        },
        color: {
            controls: [
                'styleRatingColor',
            ],
        },
        sizes: {
            controls: [
                'styleStarSize',
            ],
            group: 'styles',
            title: _x('Sizes', 'admin-text', 'site-reviews'),
            resetAll: () => {
                setAttributes({
                    styleStarSize: '1.25em',
                })
            },
        },
    };

    const onRenderComplete = (block, iframe) => {
        if (iframe?.GLSR_init) {
            iframe.GLSR_init('site-reviews/excerpts/init');
        }
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            renderCallback={onRenderComplete}
            style={{
                '--glsr-review-star': attributes.styleStarSize,
                '--glsr-review-star-bg': styleRatingColor.slug ? `var(--wp--preset--color--${styleRatingColor.slug})` : attributes.styleRatingColorCustom,
            }}
            styleClassNames={[
                (attributes.styleRatingColorCustom || styleRatingColor.slug) ? 'has-custom-rating-color' : '',
            ]}
        />
    )
}

export default withColors('styleRatingColor')(Edit)
