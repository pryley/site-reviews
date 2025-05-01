import './editor.scss';
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
import { JustifyContentControl } from '@wordpress/block-editor';
import { useEffect, useRef } from '@wordpress/element';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

export default function Edit (props) {
    const { attributes, setAttributes } = props;
    const { className } = attributes;
    const prevClassNameRef = useRef('');

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
            setAttributes({ summary_bar_size: '3em' })
        } else if (verticalBars.includes(prevStyle) && horizontalBars.includes(currentStyle)) {
            setAttributes({ summary_bar_size: '1em' })
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
        summary_align: <JustifyContentControl
            allowedControls={['left', 'center', 'right']}
            onChange={ (summary_align) => setAttributes({ summary_align}) }
            value={ attributes.summary_align }
        />,
        summary_bar_size: <ToolsPanelItem
            hasValue={ () => '1em' !== attributes.summary_bar_size }
            isShownByDefault
            label={ _x('Percent Bar Size', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ summary_bar_size: '1em' }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                isResetValueOnUnitChange
                label={ _x('Percent Bar Size', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (summary_bar_size) => setAttributes({ summary_bar_size }) }
                units={ useCustomUnits({
                    availableUnits: ['px', 'em', 'rem'],
                    defaultValues: { px: '16', em: '1', rem: '1' },
                }) }
                value={ attributes.summary_bar_size }
            />
        </ToolsPanelItem>,
        summary_bar_spacing: <ToolsPanelItem
            hasValue={ () => !['.5em','0.5em'].includes(attributes.summary_bar_spacing) }
            isShownByDefault
            label={ _x('Percent Bar Gap', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ summary_bar_spacing: '.5em' }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                isResetValueOnUnitChange
                label={ _x('Percent Bar Gap', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (summary_bar_spacing) => setAttributes({ summary_bar_spacing }) }
                units={ useCustomUnits({
                    availableUnits: ['px', 'em', 'rem'],
                    defaultValues: { px: '8', em: '.5', rem: '.5' },
                }) }
                value={ attributes.summary_bar_spacing }
            />
        </ToolsPanelItem>,
        summary_max_width: <ToolsPanelItem
            hasValue={ () => '48ch' !== attributes.summary_max_width }
            isShownByDefault
            label={ _x('Max Width', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ summary_max_width: '48ch' }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                allowReset
                isResetValueOnUnitChange
                label={ _x('Max Width', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (summary_max_width) => setAttributes({ summary_max_width }) }
                units={ useCustomUnits({
                    availableUnits: ['%', 'ch'],
                    defaultValues: { '%': '100', ch: '48' },
                }) }
                value={ attributes.summary_max_width }
            />
        </ToolsPanelItem>,
        summary_star_size: <ToolsPanelItem
            hasValue={ () => '1.5em' !== attributes.summary_star_size }
            isShownByDefault
            label={ _x('Star Size', 'admin-text', 'site-reviews') }
            onDeselect={ () => setAttributes({ summary_star_size: '1.5em' }) }
            style={{ 'grid-column': 'span 1' }}
        >
            <UnitControl
                __next40pxDefaultSize
                allowReset
                isResetValueOnUnitChange
                label={ _x('Star Size', 'admin-text', 'site-reviews') }
                min={0}
                onChange={ (summary_star_size) => setAttributes({ summary_star_size }) }
                units={ useCustomUnits({
                    availableUnits: ['px', 'em', 'rem'],
                    defaultValues: { px: '24', em: '1.5', rem: '1.5' },
                }) }
                value={ attributes.summary_star_size }
            />
        </ToolsPanelItem>,
    };

    const panels = { // order is intentional
        block: {
            controls: [
                'summary_align',
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
        sizes: {
            controls: [
                'summary_max_width',
                'summary_star_size',
                'summary_bar_size',
                'summary_bar_spacing',
            ],
            group: 'styles',
            title: _x('Sizes', 'admin-text', 'site-reviews'),
            resetAll: () => {
                setAttributes({
                    summary_bar_size: '1em',
                    summary_bar_spacing: '.5em',
                    summary_max_width: '48ch',
                    summary_star_size: '1.5em',
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
                '--glsr-bar-size': attributes.summary_bar_size,
                '--glsr-bar-spacing': attributes.summary_bar_spacing,
                '--glsr-max-w': attributes.summary_max_width || 'none',
                '--glsr-summary-star': attributes.summary_star_size,
            }}
            styleClassNames={[
                `items-justified-${attributes.summary_align || 'left'}`,
            ]}
        />
    )
}
