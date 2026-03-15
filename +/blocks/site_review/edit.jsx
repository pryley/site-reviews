import { _x } from '@wordpress/i18n';
import { AjaxSearchControl, AjaxToggleGroupControl, ColorControl } from '@site-reviews/components';
import { getCSSValueFromRawStyle } from '@wordpress/style-engine';
import { TextControl } from '@wordpress/components';
import { withColors } from "@wordpress/block-editor";
import ServerSideBlockRenderer from '@site-reviews/server-side-block-renderer';

const Edit = (props) => {
    const { attributes, setAttributes } = props;
    const { style_rating_color, style_rating_color_custom } = attributes;
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
        style_rating_color: <ColorControl
            attributeName='style_rating_color'
            label={ _x('Rating', 'admin-text', 'site-reviews') }
            props={ props }
        />,
    };
    const panels = { // order is intentional
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
                (style_rating_color || style_rating_color_custom) ? 'has-rating-color' : '',
            ]}
        />
    )
}

export default withColors('style_rating_color')(Edit);
