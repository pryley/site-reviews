import AjaxFormTokenField from '../AjaxFormTokenField.jsx';
// import AjaxSelectControl from '../AjaxSelectControl.jsx';
import AjaxToggleGroupControl from '../AjaxToggleGroupControl.jsx';
import RenderedBlock from '../RenderedBlock.jsx';
import { _x } from '@wordpress/i18n';
import {
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
    RangeControl,
    SelectControl,
    TextControl,
} from '@wordpress/components';

export default function Edit (props) {
    const { attributes, setAttributes } = props;
    setAttributes({ post_id: jQuery('#post_ID').val() }) // used to get the "post_id" assigned_posts value
    const inspectorControls = {
        assigned_posts: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_posts'
            key='assigned_posts'
            label={ _x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews') }
            onChange={ (assigned_posts) => setAttributes({ assigned_posts }) }
            placeholder={ _x('Select a Page...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_posts }
        />,
        assigned_terms: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_terms'
            key='assigned_terms'
            label={ _x('Limit Reviews by Categories', 'admin-text', 'site-reviews') }
            onChange={ (assigned_terms) => setAttributes({ assigned_terms }) }
            placeholder={ _x('Select a Category...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_terms }
        />,
        assigned_users: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_users'
            key='assigned_users'
            label={ _x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews') }
            onChange={ (assigned_users) => setAttributes({ assigned_users }) }
            placeholder={ _x('Select a User...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_users }
        />,
        terms: <SelectControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            key='terms'
            label={ _x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews') }
            onChange={ (terms) => setAttributes({ terms }) }
            options={[
                {
                    label: _x('Select Review Terms...', 'admin-text', 'site-reviews'),
                    value: '',
                },
                {
                    label: _x('Terms were accepted', 'admin-text', 'site-reviews'),
                    value: 'true',
                },
                {
                    label: _x('Terms were not accepted', 'admin-text', 'site-reviews'),
                    value: 'false',
                },
            ]}
            value={ attributes.terms }
        />,
        // type: <AjaxSelectControl
        //     endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=type'
        //     key='type'
        //     label={ _x('Limit Reviews by Type', 'admin-text', 'site-reviews') }
        //     onChange={ (type) => setAttributes({ type }) }
        //     placeholder={ _x('Select a Review Type...', 'admin-text', 'site-reviews') }
        //     value={ attributes.type }
        // />,
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
        schema: <ToggleGroupControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews') }
            key='schema'
            onChange={ (schema) => setAttributes({ schema }) }
            label={ _x('Enable the Schema?', 'admin-text', 'site-reviews') }
            value={ attributes.schema }
        >
            <ToggleGroupControlOption value={ false } label={ _x('No', 'admin-text', 'site-reviews') } />
            <ToggleGroupControlOption value={ true } label={ _x('Yes', 'admin-text', 'site-reviews') } />
        </ToggleGroupControl>,
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_summary?option=hide'
            key='hide'
            label={ _x('Hide Options', 'admin-text', 'site-reviews') }
            onChange={ (hide) => setAttributes({ hide }) }
            value={ attributes.hide }
        />,
    };
    const inspectorAdvancedControls = {
        rating_field: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Use the Review Forms addon to add custom rating fields.', 'admin-text', 'site-reviews') }
            key='rating_field'
            label={ _x('Custom Rating Field Name', 'admin-text', 'site-reviews') }
            onChange={ (rating_field) => setAttributes({ rating_field }) }
            value={ attributes.rating_field }
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
    };
    return (
        <RenderedBlock
            inspectorControls={inspectorControls}
            inspectorAdvancedControls={inspectorAdvancedControls}
            name='summary'
            props={props}
        />
    )
}
