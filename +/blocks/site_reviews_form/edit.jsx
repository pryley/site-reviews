import { _x } from '@wordpress/i18n';
import { AjaxFormTokenField, AjaxToggleGroupControl } from '@site-reviews/components';
import { TextControl } from '@wordpress/components';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, setAttributes } = props;

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
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
        />
    )
}

export default Edit;
