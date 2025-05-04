import {
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
    JustifyContentControl,
    withColors,
} from '@wordpress/block-editor';
import {
    __experimentalToolsPanelItem as ToolsPanelItem,
    __experimentalUnitControl as UnitControl,
    __experimentalUseCustomUnits as useCustomUnits,
    BaseControl,
    Notice,
    NumberControl,
    RangeControl,
    TextControl,
} from '@wordpress/components';
import { _x, sprintf } from '@wordpress/i18n';
import { AjaxComboboxControl, AjaxFormTokenField, AjaxToggleGroupControl, NoYesControl } from '@site-reviews/components';
import { getCSSValueFromRawStyle } from '@wordpress/style-engine';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, clientId, setAttributes, setStyleRatingColor, styleRatingColor } = props;
    const { className } = attributes;
    const colorGradientSettings = useMultipleOriginColorsAndGradients();
    const prevClassNameRef = useRef('');

    const {
        defaultBarSize,
        defaultBarSpacing,
        defaultMaxWidth,
        defaultStarSize,
    } = useSelect((select) => {
        const blockType = select('core/blocks').getBlockType(props.name);
        return {
            defaultBarSize: blockType?.attributes?.styleBarSize?.default || '',
            defaultBarSpacing: blockType?.attributes?.styleBarSpacing?.default || '',
            defaultMaxWidth: blockType?.attributes?.styleMaxWidth?.default || '',
            defaultStarSize: blockType?.attributes?.styleStarSize?.default || '',
        }
    }, []);

    setAttributes({ post_id: jQuery('#post_ID').val() }) // used to get the "post_id" assigned_posts value

    useEffect(() => {
        // Get current style (if any)
        const currentStyleMatch = className?.match(/is-style-(\w+)/);
        const currentStyle = currentStyleMatch ? currentStyleMatch[1] : 'default';
        // Get previous style (if any)
        const prevClassName = prevClassNameRef.current;
        const prevStyleMatch = prevClassName?.match(/is-style-(\w+)/);
        const prevStyle = prevStyleMatch ? prevStyleMatch[1] : 'default';
        // Define style groups
        const horizontalBars = ['default', '1'];
        const verticalBars = ['2', '3'];
        // Check for style transitions and update attributes
        if (horizontalBars.includes(prevStyle) && verticalBars.includes(currentStyle)) {
            setAttributes({ styleBarSize: '3em' })
        } else if (verticalBars.includes(prevStyle) && horizontalBars.includes(currentStyle)) {
            setAttributes({ styleBarSize: '1em' })
        }
        // Update previous className for next render
        prevClassNameRef.current = className || '';
    }, [className, setAttributes]);

    const controls = {
        assigned_posts: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_posts'
            key='assigned_posts'
            label={ _x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews') }
            onChange={ (assigned_posts) => setAttributes({ assigned_posts }) }
            placeholder={ _x('Search Pages...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.assigned_posts }
        />,
        assigned_terms: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_terms'
            key='assigned_terms'
            label={ _x('Limit Reviews by Categories', 'admin-text', 'site-reviews') }
            onChange={ (assigned_terms) => setAttributes({ assigned_terms }) }
            placeholder={ _x('Search Categories...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_terms }
        />,
        assigned_users: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_users'
            key='assigned_users'
            label={ _x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews') }
            onChange={ (assigned_users) => setAttributes({ assigned_users }) }
            placeholder={ _x('Search Users...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.assigned_users }
        />,
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=hide'
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
        labels: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Enter custom labels for the percentage bar levels (from high to low) and separate them with a comma.', 'admin-text', 'site-reviews') }
            key='labels'
            label={ _x('Summary Labels', 'admin-text', 'site-reviews') }
            onChange={ (labels) => setAttributes({ labels }) }
            placeholder={ _x('Excellent, Very good, Average, Poor, Terrible', 'admin-text', 'site-reviews') }
            value={ attributes.labels }
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
        rating_field: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Use the Review Forms addon to add custom rating fields.', 'admin-text', 'site-reviews') }
            key='rating_field'
            label={ _x('Custom Rating Field Name', 'admin-text', 'site-reviews') }
            onChange={ (rating_field) => setAttributes({ rating_field }) }
            value={ attributes.rating_field }
        />,
        schema: <NoYesControl
            help={ _x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews') }
            key='schema'
            onChange={ (schema) => setAttributes({ schema }) }
            label={ _x('Enable the Schema?', 'admin-text', 'site-reviews') }
            value={ attributes.schema }
        />,
        styleAlign: <JustifyContentControl
            allowedControls={['left', 'center', 'right']}
            onChange={ (styleAlign) => setAttributes({ styleAlign }) }
            value={ attributes.styleAlign }
        />,
        styleBarSize: <ToolsPanelItem
            hasValue={ () => attributes.styleBarSize !== defaultBarSize }
            isShownByDefault
            label={ _x('Percent Bar Size', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleBarSize: defaultBarSize }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                isResetValueOnUnitChange
                label={ _x('Percent Bar Size', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (styleBarSize) => setAttributes({ styleBarSize }) }
                units={ useCustomUnits({
                    availableUnits: ['px', 'em', 'rem'],
                    defaultValues: { px: '16', em: '1', rem: '1' },
                }) }
                value={ attributes.styleBarSize }
            />
        </ToolsPanelItem>,
        styleBarSpacing: <ToolsPanelItem
            hasValue={ () => attributes.styleBarSpacing !== defaultBarSpacing }
            isShownByDefault
            label={ _x('Percent Bar Gap', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleBarSpacing: defaultBarSpacing }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                isResetValueOnUnitChange
                label={ _x('Percent Bar Gap', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (styleBarSpacing) => setAttributes({ styleBarSpacing }) }
                units={ useCustomUnits({
                    availableUnits: ['px', 'em', 'rem'],
                    defaultValues: { px: '8', em: '0.5', rem: '0.5' },
                }) }
                value={ attributes.styleBarSpacing }
            />
        </ToolsPanelItem>,
        styleMaxWidth: <ToolsPanelItem
            hasValue={ () => attributes.styleMaxWidth !== defaultMaxWidth }
            isShownByDefault
            label={ _x('Max Width', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ styleMaxWidth: defaultMaxWidth }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                allowReset
                isResetValueOnUnitChange
                label={ _x('Max Width', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (styleMaxWidth) => setAttributes({ styleMaxWidth }) }
                units={ useCustomUnits({
                    availableUnits: ['%', 'ch'],
                    defaultValues: { '%': '100', ch: '48' },
                }) }
                value={ attributes.styleMaxWidth }
            />
        </ToolsPanelItem>,
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
                    defaultValues: { px: '24', em: '1.5', rem: '1.5' },
                }) }
                value={ attributes.styleStarSize }
            />
        </ToolsPanelItem>,
        terms: <AjaxComboboxControl
            __experimentalRenderItem={false}
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=terms'
            key='terms'
            label={ _x('Limit Reviews by terms accepted', 'admin-text', 'site-reviews') }
            onChange={ (terms) => setAttributes({ terms }) }
            placeholder={ _x('Select Review Terms...', 'admin-text', 'site-reviews') }
            value={ attributes.terms }
        />,
        text: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Use {num} to display the number of reviews, {rating} to display the average rating, and {max} to display the maximum rating value.', 'admin-text', 'site-reviews') }
            key='text'
            label={ _x('Summary Text', 'admin-text', 'site-reviews') }
            onChange={ (text) => setAttributes({ text }) }
            placeholder={ _x('{rating} out of {max} stars (based on {num} reviews)', 'admin-text', 'site-reviews') }
            value={ attributes.text }
        />,
        text_options_notice: <BaseControl __nextHasNoMarginBottom>
            <Notice status="warning" politeness="polite" isDismissible={ false }>
                { _x('The recommended way to change these values is to use the Site Reviews → Settings → Strings page.', 'admin-text', 'site-reviews') }
            </Notice>
        </BaseControl>,
        type: <AjaxComboboxControl
            __experimentalRenderItem={false}
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=type'
            hideIfEmpty={ true }
            key='type'
            label={ _x('Limit Reviews by Type', 'admin-text', 'site-reviews') }
            onChange={ (type) => setAttributes({ type }) }
            placeholder={ _x('Select a Review Type...', 'admin-text', 'site-reviews') }
            value={ attributes.type }
        />,
    };

    const panels = { // order is intentional
        block: {
            controls: [
                'styleAlign',
            ],
        },
        settings: {
            controls: [
                'assigned_posts',
                'assigned_terms',
                'assigned_users',
                'terms',
                'type',
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
        text: {
            controls: [
                'text_options_notice',
                'text',
                'labels',
            ],
            initialOpen: false,
            title: _x('Text Options', 'admin-text', 'site-reviews'),
        },
        advanced: {
            controls: [
                'id',
                'rating_field',
            ],
        },
        color: {
            controls: [
                'styleRatingColor',
            ],
        },
        sizes: {
            controls: [
                'styleMaxWidth',
                'styleStarSize',
                'styleBarSize',
                'styleBarSpacing',
            ],
            group: 'styles',
            title: _x('Sizes', 'admin-text', 'site-reviews'),
            resetAll: () => {
                setAttributes({
                    styleBarSize: defaultBarSize,
                    styleBarSpacing: defaultBarSpacing,
                    styleMaxWidth: defaultMaxWidth,
                    styleStarSize: defaultStarSize,
                })
            },
        },
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            style={{
                '--glsr-bar-bg': styleRatingColor.slug
                    ? getCSSValueFromRawStyle(`var:preset|color|${styleRatingColor.slug}`)
                    : attributes.styleRatingColorCustom,
                '--glsr-bar-size': attributes.styleBarSize,
                '--glsr-bar-spacing': attributes.styleBarSpacing,
                '--glsr-max-w': attributes.styleMaxWidth || 'none',
                '--glsr-summary-star': attributes.styleStarSize,
                '--glsr-summary-star-bg': 'var(--glsr-bar-bg)',
            }}
            styleClassNames={[
                (attributes.styleAlign) ? `items-justified-${attributes.styleAlign}` : '',
                (attributes.styleRatingColorCustom || styleRatingColor.slug) ? 'has-custom-rating-color' : '',
            ]}
        />
    )
}

export default withColors('styleRatingColor')(Edit)
