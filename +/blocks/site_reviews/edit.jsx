import { _x } from '@wordpress/i18n';
import { AjaxComboboxControl, AjaxFormTokenField, AjaxSearchControl, AjaxToggleGroupControl, ColorControl, NoYesControl } from '@site-reviews/components';
import { getCSSValueFromRawStyle } from '@wordpress/style-engine';
import { JustifyContentControl, withColors } from "@wordpress/block-editor";
import { RangeControl, TextControl } from '@wordpress/components';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, setAttributes } = props;
    const { style_rating_color, style_rating_color_custom } = attributes;
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
        author: <AjaxSearchControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=author'
            key='author'
            label={ _x('Limit Reviews by Review Author', 'admin-text', 'site-reviews') }
            onChange={ (author) => setAttributes({ author }) }
            placeholder={ _x('Search Users...', 'admin-text', 'site-reviews') }
            prefetch={ true }
            value={ attributes.author }
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
        style_align: <JustifyContentControl
            allowedControls={['left', 'center', 'right']}
            onChange={ (style_align) => setAttributes({ style_align }) }
            value={ attributes.style_align }
        />,
        style_rating_color: <ColorControl
            attributeName='style_rating_color'
            label={ _x('Rating', 'admin-text', 'site-reviews') }
            props={ props }
        />,
        terms: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=terms'
            key='terms'
            label={ _x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews') }
            onChange={ (terms) => setAttributes({ terms }) }
            placeholder={ _x('Select Review Terms...', 'admin-text', 'site-reviews') }
            value={ attributes.terms }
        />,
        type: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=type'
            hideIfEmpty={ true }
            key='type'
            label={ _x('Limit Reviews by Type', 'admin-text', 'site-reviews') }
            onChange={ (type) => setAttributes({ type }) }
            placeholder={ _x('Select a Review Type...', 'admin-text', 'site-reviews') }
            value={ attributes.type }
        />,
        verified: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=verified'
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
                'pagination',
                'display',
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
        advanced: {
            controls: [
                'id',
            ],
        },
        color: {
            controls: [
                'style_rating_color',
            ],
        },
    };
    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            style={{
                '--glsr-review-star-bg': style_rating_color
                    ? getCSSValueFromRawStyle(`var:preset|color|${style_rating_color}`)
                    : style_rating_color_custom,
            }}
            styleClassNames={[
                (attributes?.style_align) ? `items-justified-${attributes.style_align}` : '',
                (style_rating_color || style_rating_color_custom) ? 'has-custom-color' : '',
            ]}
        />
    )
}

export default withColors('style_rating_color')(Edit);
