import AjaxComboboxControl from '../AjaxComboboxControl';
import AjaxFormTokenField from '../AjaxFormTokenField';
import AjaxToggleGroupControl from '../AjaxToggleGroupControl';
import NoYesControl from '../NoYesControl';
import RenderedBlock from '../RenderedBlock';
import { _x } from '@wordpress/i18n';
import { RangeControl, TextControl } from '@wordpress/components';

export default function Edit (props) {
    const { attributes, setAttributes } = props;
    setAttributes({ post_id: jQuery('#post_ID').val() }) // used to get the "post_id" assigned_posts value
    const inspectorControls = {
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
        pagination: <AjaxComboboxControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=pagination'
            key='pagination'
            label={ _x('Pagination Type', 'admin-text', 'site-reviews') }
            onChange={ (pagination) => setAttributes({ pagination }) }
            placeholder={ _x('Select Pagination...', 'admin-text', 'site-reviews') }
            value={ attributes.pagination }
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
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_reviews?option=hide'
            key='hide'
            label={ _x('Hide Options', 'admin-text', 'site-reviews') }
            onChange={ (hide) => setAttributes({ hide }) }
            value={ attributes.hide }
        />,
    };
    const inspectorAdvancedControls = {
        id: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('This should be a unique value.', 'admin-text', 'site-reviews') }
            key='id'
            label={ _x('Custom ID', 'admin-text', 'site-reviews') }
            onChange={ (id) => setAttributes({ id }) }
            value={ attributes.id }
        />,
    };
    return (
        <RenderedBlock
            inspectorControls={inspectorControls}
            inspectorAdvancedControls={inspectorAdvancedControls}
            name='reviews'
            props={props}
        />
    )
}
