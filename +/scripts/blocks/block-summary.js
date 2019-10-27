import { SummaryIcon } from './icons';
import { CheckboxControlList } from './checkbox-control-list';
import categories from './categories';
import types from './types';
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { PanelBody, RangeControl, SelectControl, ServerSideRender, TextControl, ToggleControl } = wp.components;
const attributes = {
    assigned_to: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    hide: { default: '', type: 'string' },
    post_id: { default: '', type: 'string' },
    rating: { default: 0, type: 'number' },
    schema: { default: false, type: 'boolean' },
    type: { default: 'local', type: 'string' },
};
const blockName = GLSR.nameprefix + '/summary';

const edit = props => {
    props.attributes.post_id = jQuery('#post_ID').val();
    const { attributes: { assigned_to, category, display, hide, id, pagination, rating, schema, type }, className, setAttributes } = props;
    return [
        <InspectorControls>
            <PanelBody title={ __('Settings', 'site-reviews')}>
                <TextControl
                    help={ __('Limit reviews to those assigned to this post ID. You can also enter "post_id" to use the ID of the current page, or "parent_id" to use the ID of the parent page.', 'site-reviews') }
                    label={ __('Assigned To', 'site-reviews') }
                    onChange={ assigned_to => setAttributes({ assigned_to }) }
                    value={ assigned_to }
                />
                <SelectControl
                    help={ __('Limit reviews to a category.', 'site-reviews') }
                    label={ __('Category', 'site-reviews') }
                    onChange={ category => setAttributes({ category }) }
                    options={ categories }
                    value={ category }
                />
                <SelectControl
                    help={ __('Limit type of reviews.', 'site-reviews') }
                    label={ __('Type', 'site-reviews') }
                    onChange={ type => setAttributes({ type }) }
                    options={ types }
                    value={ type }
                />
                <RangeControl
                    help={ __('Limit reviews to a minimum rating.', 'site-reviews') }
                    label={ __('Minimum Rating', 'site-reviews') }
                    min={ 0 }
                    max={ 5 }
                    onChange={ rating => setAttributes({ rating }) }
                    value={ rating }
                />
                <ToggleControl
                    checked={ schema }
                    help={ __('The schema should only be enabled once per page.', 'site-reviews') }
                    label={ __('Enable the schema?', 'site-reviews') }
                    onChange={ schema => setAttributes({ schema }) }
                />
                { CheckboxControlList(GLSR.hideoptions.site_reviews_summary, hide, setAttributes) }
            </PanelBody>
        </InspectorControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes }>
        </ServerSideRender>
    ];
};

export default registerBlockType(
    blockName, {
        attributes: attributes,
        category: GLSR.nameprefix,
        description: __('Display a summary of your reviews.', 'site-reviews'),
        edit: edit,
        example: {},
        icon: {src: SummaryIcon},
        keywords: ['reviews', 'summary'],
        save: () => null,
        title: __('Summary', 'site-reviews'),
    }
);
