import AjaxToggleGroupControl from '../AjaxToggleGroupControl.jsx';
import RenderedBlock from '../RenderedBlock.jsx';
import { _x } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';

export default function Edit (props) {
    const { attributes, setAttributes } = props;
    const inspectorControls = {
        post_id: <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={ _x('Enter the Post ID of the review to display.', 'admin-text', 'site-reviews') }
            key='post_id'
            label={ _x('Review Post ID', 'admin-text', 'site-reviews') }
            onChange={ (post_id) => setAttributes({ post_id }) }
            placeholder={ _x('Enter a Review Post ID', 'admin-text', 'site-reviews') }
            value={ attributes.post_id }
        />,
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_review?option=hide'
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
            name='review'
            props={props}
        />
    )
}