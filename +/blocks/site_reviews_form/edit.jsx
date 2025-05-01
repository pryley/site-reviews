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
import { AjaxFormTokenField, AjaxToggleGroupControl } from '@site-reviews/components';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, clientId, setAttributes, setStyleRatingColor, styleRatingColor } = props;
    const colorGradientSettings = useMultipleOriginColorsAndGradients();

    const controls = {
        assigned_posts: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=assigned_posts'
            key='assigned_posts'
            label={ _x('Assign Reviews to Pages', 'admin-text', 'site-reviews') }
            onChange={ (assigned_posts) => setAttributes({ assigned_posts }) }
            placeholder={ _x('Search Pages...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.assigned_posts }
        />,
        assigned_terms: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=assigned_terms'
            key='assigned_terms'
            label={ _x('Assign Reviews to Categories', 'admin-text', 'site-reviews') }
            onChange={ (assigned_terms) => setAttributes({ assigned_terms }) }
            placeholder={ _x('Search Categories...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_terms }
        />,
        assigned_users: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=assigned_users'
            key='assigned_users'
            label={ _x('Assign Reviews to Users', 'admin-text', 'site-reviews') }
            onChange={ (assigned_users) => setAttributes({ assigned_users }) }
            placeholder={ _x('Search Users...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.assigned_users }
        />,
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=hide'
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
            onChange={ id => setAttributes({ id }) }
            value={ attributes.id }
        />,
        reviews_id: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Enter the Custom ID of a Latest Reviews block where the review should be displayed after submission.', 'admin-text', 'site-reviews') }
            key='reviews_id'
            label={ _x('Latest Reviews ID', 'admin-text', 'site-reviews') }
            onChange={ reviews_id => setAttributes({ reviews_id }) }
            value={ attributes.reviews_id }
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
            hasValue={ () => '2em' !== attributes.styleStarSize }
            isShownByDefault
            label={ _x('Star Size', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleStarSize: '2em' }) }
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
                    defaultValues: { px: '32', em: '2', rem: '2' },
                }) }
                value={ attributes.styleStarSize }
            />
        </ToolsPanelItem>,
        summary_id: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Enter the Custom ID of a Rating Summary block where the rating values should be updated after submission.', 'admin-text', 'site-reviews') }
            key='summary_id'
            label={ _x('Rating Summary ID', 'admin-text', 'site-reviews') }
            onChange={ summary_id => setAttributes({ summary_id }) }
            value={ attributes.summary_id }
        />,
    };

    const panels = { // order is intentional
        settings: {
            controls: [
                'assigned_posts',
                'assigned_terms',
                'assigned_users',
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
                'reviews_id',
                'summary_id',
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
                    styleStarSize: '2em',
                })
            },
        },
    };

    const onRenderComplete = () => { // @todo render the stars server-side!
        if (GLSR?.stars) {
            GLSR.stars.destroy();
            GLSR.stars.init('.glsr-field-rating select', { clearable: true });
        }
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            renderCallback={onRenderComplete}
            style={{
                '--glsr-form-star': attributes.styleStarSize,
                '--glsr-form-star-bg': styleRatingColor.slug ? `var(--wp--preset--color--${styleRatingColor.slug})` : attributes.styleRatingColorCustom,
            }}
            styleClassNames={[
                (attributes.styleRatingColorCustom || styleRatingColor.slug) ? 'has-custom-rating-color' : '',
            ]}
        />
    )
}

export default withColors('styleRatingColor')(Edit)
