import {
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalSpacingSizesControl as SpacingSizesControl,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
    withColors,
} from '@wordpress/block-editor';
import {
    __experimentalToolsPanelItem as ToolsPanelItem,
    __experimentalUnitControl as UnitControl,
    __experimentalUseCustomUnits as useCustomUnits,
    RangeControl,
    TextControl,
} from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import { AjaxComboboxControl, AjaxFormTokenField, AjaxToggleGroupControl, NoYesControl } from '@site-reviews/components';
import { getCSSValueFromRawStyle } from '@wordpress/style-engine';
import { useSelect } from '@wordpress/data';
import isShallowEqual from '@wordpress/is-shallow-equal';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, clientId, setAttributes, setStyleRatingColor, styleRatingColor } = props;
    const colorGradientSettings = useMultipleOriginColorsAndGradients();

    const {
        defaultReviewSpacing,
        defaultStarSize,
    } = useSelect((select) => {
        const blockType = select('core/blocks').getBlockType(props.name);
        return {
            defaultReviewSpacing: blockType?.attributes?.styleReviewSpacing?.default || {},
            defaultStarSize: blockType?.attributes?.styleStarSize?.default || '',
        };
    }, []);

    setAttributes({ post_id: jQuery('#post_ID').val() }) // used to get the "post_id" assigned_posts value

    const controls = {
        assigned_posts: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=assigned_posts'
            key='assigned_posts'
            label={ _x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews') }
            onChange={ (assigned_posts) => setAttributes({ assigned_posts }) }
            placeholder={ _x('Search Pages...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.assigned_posts }
        />,
        assigned_terms: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=assigned_terms'
            key='assigned_terms'
            label={ _x('Limit Reviews by Categories', 'admin-text', 'site-reviews') }
            onChange={ (assigned_terms) => setAttributes({ assigned_terms }) }
            placeholder={ _x('Search Categories...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_terms }
        />,
        assigned_users: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=assigned_users'
            key='assigned_users'
            label={ _x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews') }
            onChange={ (assigned_users) => setAttributes({ assigned_users }) }
            placeholder={ _x('Search Users...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.assigned_users }
        />,
        display: <RangeControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            key='display'
            label={ _x('Reviews Per Page', 'admin-text', 'site-reviews') }
            min='1'
            max='50'
            onChange={ (display) => setAttributes({ display }) }
            value={ attributes.display }
        />,
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=hide'
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
        pagination: <AjaxComboboxControl
            __experimentalRenderItem={false}
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=pagination'
            key='pagination'
            label={ _x('Pagination Type', 'admin-text', 'site-reviews') }
            onChange={ (pagination) => setAttributes({ pagination }) }
            placeholder={ _x('Select Pagination...', 'admin-text', 'site-reviews') }
            value={ attributes.pagination }
        />,
        rating: <RangeControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            key='rating'
            label={ _x('Minimum Rating', 'admin-text', 'site-reviews') }
            min={ GLSR.minrating }
            max={ GLSR.maxrating }
            onChange={ (rating) => setAttributes({ rating }) }
            value={ attributes.rating }
        />,
        schema: <NoYesControl
            help={ _x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews') }
            key='schema'
            onChange={ (schema) => setAttributes({ schema }) }
            label={ _x('Enable the Schema?', 'admin-text', 'site-reviews') }
            value={ attributes.schema }
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
        styleReviewSpacing: <ToolsPanelItem
            hasValue={ () => !isShallowEqual(attributes.styleReviewSpacing, defaultReviewSpacing) }
            isShownByDefault
            label={ _x('Review Spacing', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleReviewSpacing: defaultReviewSpacing }) }
        >
            <SpacingSizesControl
                label={ _x('Review Spacing', 'admin-text', 'site-reviews') }
                onChange={ (styleReviewSpacing) => setAttributes({ styleReviewSpacing }) }
                panelId={clientId}
                sides={ ['vertical'] }
                values={ attributes.styleReviewSpacing }
            />
        </ToolsPanelItem>,
        styleStarSize: <ToolsPanelItem
            hasValue={ () => attributes.styleStarSize !== defaultStarSize }
            isShownByDefault
            label={ _x('Star Size', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleStarSize: defaultStarSize }) }
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
        terms: <AjaxComboboxControl
            __experimentalRenderItem={false}
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=terms'
            key='terms'
            label={ _x('Limit Reviews by terms accepted', 'admin-text', 'site-reviews') }
            onChange={ (terms) => setAttributes({ terms }) }
            placeholder={ _x('Select Review Terms...', 'admin-text', 'site-reviews') }
            value={ attributes.terms }
        />,
        type: <AjaxComboboxControl
            __experimentalRenderItem={false}
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=type'
            hideIfEmpty={ true }
            key='type'
            label={ _x('Limit Reviews by Type', 'admin-text', 'site-reviews') }
            onChange={ (type) => setAttributes({ type }) }
            placeholder={ _x('Select a Review Type...', 'admin-text', 'site-reviews') }
            value={ attributes.type }
        />,
    };

    const panels = { // order is intentional
        settings: {
            controls: [
                'assigned_posts',
                'assigned_terms',
                'assigned_users',
                'terms',
                'type',
                'pagination',
                'display',
                'rating',
                'schema',
            ],
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
                'styleReviewSpacing',
                'styleStarSize',
            ],
            group: 'styles',
            title: _x('Sizes', 'admin-text', 'site-reviews'),
            resetAll: () => {
                setAttributes({
                    styleReviewSpacing: defaultReviewSpacing,
                    styleStarSize: defaultStarSize,
                })
            },
        },
    };

    const onRenderComplete = () => {
        if (window.GLSR_init) {
            GLSR_init('site-reviews/excerpts/init');
        }
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            renderCallback={onRenderComplete}
            style={{
                '--glsr-review-row-gap': getCSSValueFromRawStyle(attributes.styleReviewSpacing.top),
                '--glsr-review-star': attributes.styleStarSize,
                '--glsr-review-star-bg': styleRatingColor.slug
                    ? getCSSValueFromRawStyle(`var:preset|color|${styleRatingColor.slug}`)
                    : attributes.styleRatingColorCustom,
            }}
            styleClassNames={[
                (attributes.styleRatingColorCustom || styleRatingColor.slug) ? 'has-custom-rating-color' : '',
            ]}
        />
    )
}

export default withColors('styleRatingColor')(Edit)

