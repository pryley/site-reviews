import { _x } from '@wordpress/i18n';
import { AjaxSearchControl, AjaxToggleGroupControl } from '@site-reviews/components';
import { JustifyContentControl } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, setAttributes } = props;

    const controls = {
        hide: <AjaxToggleGroupControl
            endpoint='/site-reviews/v1/shortcode/site_review?option=hide'
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
        post_id: <AjaxSearchControl
            endpoint='/site-reviews/v1/shortcode/site_review?option=post_id'
            help={ _x('Search for a review to display.', 'admin-text', 'site-reviews') }
            key='post_id'
            label={ _x('Review Post ID', 'admin-text', 'site-reviews') }
            onChange={ (post_id) => setAttributes({ post_id }) }
            placeholder={ _x('Search Reviews...', 'admin-text', 'site-reviews') }
            value={ attributes.post_id }
        />,
        style_align: <JustifyContentControl
            allowedControls={['left', 'center', 'right']}
            onChange={ (style_align) => setAttributes({ style_align }) }
            value={ attributes.style_align }
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
                'post_id',
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
            ],
        },
    };

    return (
        <ServerSideBlockRenderer
            controls={controls}
            panels={panels}
            props={props}
            styleClassNames={[
                (attributes.style_align) ? `items-justified-${attributes.style_align}` : '',
            ]}
        />
    )
}

export default Edit;
