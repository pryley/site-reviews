import AjaxFormTokenField from '../AjaxFormTokenField.jsx';
import AjaxToggleGroupControl from '../AjaxToggleGroupControl.jsx';
import RenderedBlock from '../RenderedBlock.jsx';
import { _x } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';

export default function Edit (props) {
    const { attributes, setAttributes } = props;
    const inspectorControls = {
        assigned_posts: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=assigned_posts'
            key='assigned_posts'
            label={ _x('Assign Reviews to Pages', 'admin-text', 'site-reviews') }
            onChange={ (assigned_posts) => setAttributes({ assigned_posts }) }
            placeholder={ _x('Select a Page...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_posts }
        />,
        assigned_terms: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=assigned_terms'
            key='assigned_terms'
            label={ _x('Assign Reviews to Categories', 'admin-text', 'site-reviews') }
            onChange={ (assigned_terms) => setAttributes({ assigned_terms }) }
            placeholder={ _x('Select a Category...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_terms }
        />,
        assigned_users: <AjaxFormTokenField
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=assigned_users'
            key='assigned_users'
            label={ _x('Assign Reviews to Users', 'admin-text', 'site-reviews') }
            onChange={ (assigned_users) => setAttributes({ assigned_users }) }
            placeholder={ _x('Select a User...', 'admin-text', 'site-reviews') }
            value={ attributes.assigned_users }
        />,
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_reviews_form?option=hide'
            key='hide'
            label={ _x('Hide Options', 'admin-text', 'site-reviews') }
            onChange={ (hide) => setAttributes({ hide }) }
            value={ attributes.hide }
        />,
    };
    const inspectorAdvancedControls = {
        reviews_id: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Enter the Custom ID of a reviews block, shortcode, or widget where the review should be displayed after submission.', 'admin-text', 'site-reviews') }
            key='reviews_id'
            label={ _x('Reviews ID', 'admin-text', 'site-reviews') }
            onChange={ reviews_id => setAttributes({ reviews_id }) }
            value={ attributes.reviews_id }
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
    };
    const onRenderComplete = () => {
        if (GLSR?.stars) {
            GLSR.stars.destroy();
            GLSR.stars.init('.glsr-field-rating select', { clearable: true });
        }
    };
    return (
        <RenderedBlock
            inspectorControls={inspectorControls}
            inspectorAdvancedControls={inspectorAdvancedControls}
            name='form'
            props={props}
            renderCallback={onRenderComplete}
        />
    )
}
