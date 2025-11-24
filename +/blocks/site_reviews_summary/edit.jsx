import { __, _x, sprintf } from '@wordpress/i18n';
import { AjaxComboboxControl, AjaxFormTokenField, AjaxSearchControl, AjaxToggleGroupControl, NoYesControl } from '@site-reviews/components';
import { BaseControl, Notice, RangeControl, TextControl } from '@wordpress/components';
import { JustifyContentControl } from '@wordpress/block-editor';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, setAttributes } = props;

    setAttributes({ post_id: jQuery('#post_ID').val() }) // used to get the "post_id" assigned_posts value

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
        styleAlign: <JustifyContentControl
            allowedControls={['left', 'center', 'right']}
            onChange={ (styleAlign) => setAttributes({ styleAlign }) }
            value={ attributes.styleAlign }
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
        text_options_notice: <BaseControl __nextHasNoMarginBottom>
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
                'author',
                'terms',
                'type',
                'schema',
            ],
        },
        display: {
            controls: [
                'rating',
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
                'text_options_notice',
                'text',
                'labels',
            ],
            initialOpen: false,
        },
        advanced: {
            controls: [
                'rating_field',
                'id',
            ],
        },
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            style={{
                '--glsr-summary-align': ({ left: 'start', right: 'end' }[attributes.styleAlign || 'left']) || 'center',
            }}
            styleClassNames={[
                (attributes.styleAlign) ? `items-justified-${attributes.styleAlign}` : '',
            ]}
        />
    )
}

export default Edit;
