import { CheckboxControlList } from './checkbox-control-list';
import { ReviewsIcon } from './icons';
import categories from './categories';
import types from './types';
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.editor;
const { PanelBody, RangeControl, SelectControl, ServerSideRender, TextControl, ToggleControl } = wp.components;
const attributes = {
    assigned_to: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    display: { default: 5, type: 'number' },
    hide: { default: '', type: 'string' },
    id: { default: '', type: 'string' },
    pagination: { default: '', type: 'string' },
    post_id: { default: '', type: 'string' },
    rating: { default: 0, type: 'number' },
    schema: { default: false, type: 'boolean' },
    type: { default: 'local', type: 'string' },
};
const blockName = GLSR.nameprefix + '/reviews';

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
                    help={ __('Pagination should only be enabled once per page.', 'site-reviews') }
                    label={ __('Pagination', 'site-reviews') }
                    onChange={ pagination => setAttributes({ pagination }) }
                    options={[
                        { label: '- ' + __('Select', 'site-reviews') + ' -', value: '' },
                        { label: __( 'Enabled', 'site-reviews' ), value: 'true' },
                        { label: __( 'Enabled (using ajax)', 'site-reviews' ), value: 'ajax' },
                    ]}
                    value={ pagination }
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
                <RangeControl
                    help={ __('The number of reviews to display.', 'site-reviews') }
                    label={ __('Display', 'site-reviews') }
                    min={ 1 }
                    max={ 50 }
                    onChange={ display => setAttributes({ display }) }
                    value={ display }
                />
                <ToggleControl
                    checked={ schema }
                    help={ __('The schema should only be enabled once per page.', 'site-reviews') }
                    label={ __('Enable the schema?', 'site-reviews') }
                    onChange={ schema => setAttributes({ schema }) }
                />
                { CheckboxControlList(GLSR.hideoptions.site_reviews, hide, setAttributes) }
            </PanelBody>
        </InspectorControls>,
        <InspectorAdvancedControls>
            <TextControl
                label={ __('Custom ID', 'site-reviews') }
                onChange={ id => setAttributes({ id }) }
                value={ id }
            />
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes }>
        </ServerSideRender>
    ];
};

wp.hooks.addFilter('blocks.getBlockAttributes', blockName, (attributes, block, unknown, saved) => {
    if (saved && saved.count) { // @deprecated since 4.1.0
        attributes.display = saved.count;
    }
    return attributes;
});

export default registerBlockType(
    blockName, {
        attributes: attributes,
        category: GLSR.nameprefix,
        description: __('Display your most recent reviews.', 'site-reviews'),
        edit: edit,
        example: {
            attributes: { 
                display: 2,
                pagination: 'ajax',
                rating: 0,
            },
        },
        icon: {src: ReviewsIcon},
        keywords: ['reviews'],
        save: () => null,
        title: __('Latest Reviews', 'site-reviews'),
    }
);
