import { __, _x, sprintf } from '@wordpress/i18n';
import { AjaxComboboxControl, AjaxFormTokenField, AjaxSearchControl, AjaxToggleGroupControl, ColorControl, NoYesControl, UnitControl } from '@site-reviews/components';
import { BaseControl, Notice, RangeControl, TextControl } from '@wordpress/components';
import { getCSSValueFromRawStyle } from '@wordpress/style-engine';
import { JustifyContentControl, withColors } from "@wordpress/block-editor";
import { useSelect } from '@wordpress/data';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, setAttributes } = props;
    const {
        style_bar_color,
        style_bar_color_custom,
        style_rating_color,
        style_rating_color_custom,
    } = attributes;
    const {
        style_max_width_default,
    } = useSelect(select => {
        const blockType = select('core/blocks').getBlockType(props.name);
        return {
            style_max_width_default: blockType?.attributes?.style_max_width?.default || '',
        }
    }, []);
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
        author: <AjaxSearchControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=author'
            key='author'
            label={ _x('Limit Reviews by Review Author', 'admin-text', 'site-reviews') }
            onChange={ (author) => setAttributes({ author }) }
            placeholder={ _x('Search Users...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.author }
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
            placeholder={[
                __('Excellent', 'site-reviews'),
                __('Very good', 'site-reviews'),
                __('Average', 'site-reviews'),
                __('Poor', 'site-reviews'),
                __('Terrible', 'site-reviews'),
            ].join(', ')}
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
        style_align: <JustifyContentControl
            allowedControls={['left', 'center', 'right']}
            onChange={ (style_align) => setAttributes({ style_align }) }
            value={ attributes.style_align }
        />,
        style_bar_color: <ColorControl
            attributeName='style_bar_color'
            label={ _x('Percent Bar', 'admin-text', 'site-reviews') }
            props={ props }
        />,
        style_max_width: <UnitControl
            attributeName='style_max_width'
            defaultValue={ style_max_width_default }
            props={ props }
            label={ _x('Max Width', 'admin-text', 'site-reviews-themes') }
            units={[
                { value: 'ch',  label: 'ch',  default: '48' },
                { value: 'px',  label: 'px',  default: '640' },
                { value: 'em',  label: 'em',  default: '40' },
                { value: 'rem', label: 'rem', default: '40' },
                { value: '%', label: '%', default: '100' },
                { value: 'vw', label: 'vw', default: '100' },
            ]}
        />,
        style_rating_color: <ColorControl
            attributeName='style_rating_color'
            label={ _x('Rating', 'admin-text', 'site-reviews') }
            props={ props }
        />,
        terms: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=terms'
            key='terms'
            label={ _x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews') }
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
        notice_text: <BaseControl __nextHasNoMarginBottom>
            <Notice status="warning" politeness="polite" isDismissible={ false }>
                { _x('The recommended way to change these values is to use the Site Reviews → Settings → Strings page.', 'admin-text', 'site-reviews') }
            </Notice>
        </BaseControl>,
        type: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=type'
            hideIfEmpty={ true }
            key='type'
            label={ _x('Limit Reviews by Type', 'admin-text', 'site-reviews') }
            onChange={ (type) => setAttributes({ type }) }
            placeholder={ _x('Select a Review Type...', 'admin-text', 'site-reviews') }
            value={ attributes.type }
        />,
        verified: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=verified'
            key='verified'
            label={ _x('Limit Reviews by Verified Status', 'admin-text', 'site-reviews') }
            onChange={ (verified) => setAttributes({ verified }) }
            placeholder={ _x('Select Verified Status...', 'admin-text', 'site-reviews') }
            value={ attributes.verified }
        />,
    };
    const panels = { // order is intentional
        block: {
            controls: [
                'style_align',
            ],
        },
        settings: {
            controls: [
                'assigned_posts',
                'assigned_terms',
                'assigned_users',
                'author',
                'terms',
                'type',
                'verified',
                'schema',
            ],
        },
        display: {
            controls: [
                'rating',
                'rating_field',
            ],
            initialOpen: false,
        },
        hide: {
            controls: [
                'hide',
            ],
            initialOpen: false,
        },
        text: {
            controls: [
                'notice_text',
                'text',
                'labels',
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
                'style_bar_color',
                'style_rating_color',
            ],
        },
        sizes: {
            controls: [
                'style_max_width',
            ],
            group: 'styles',
            title: _x('Sizes', 'admin-text', 'site-reviews'),
            resetAll: () => {
                setAttributes({
                    style_max_width: style_max_width_default,
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
                '--glsr-bar-bg': style_bar_color
                  ? getCSSValueFromRawStyle(`var:preset|color|${style_bar_color}`)
                  : style_bar_color_custom,
                '--glsr-max-w': attributes.style_max_width || 'none',
                '--glsr-summary-align': ({ left: 'start', right: 'end' }[attributes.style_align || 'left']) || 'center',
                '--glsr-summary-star-bg': style_rating_color
                    ? getCSSValueFromRawStyle(`var:preset|color|${style_rating_color}`)
                    : style_rating_color_custom,
            }}
            styleClassNames={[
                (attributes.style_align) ? `items-justified-${attributes.style_align}` : '',
                (style_rating_color || style_rating_color_custom) ? 'has-rating-color' : '',
            ]}
        />
    )
}

export default withColors('style_bar_color', 'style_rating_color')(Edit)
