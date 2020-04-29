import { CheckboxControlList } from './checkbox-control-list';
import { ReviewsIcon } from './icons';
import categories from './categories';
import ConditionalSelectControl from './ConditionalSelectControl';
import types from './types';

const { _x } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.blockEditor;
const { PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

const blockName = GLSR.nameprefix + '/reviews';

const attributes = {
    assigned_to: { default: '', type: 'string' },
    assigned_to_custom: { default: '', type: 'string' },
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

const edit = props => {
    props.attributes.post_id = jQuery('#post_ID').val();
    const { attributes: { assigned_to, assigned_to_custom, category, display, hide, id, pagination, rating, schema, type }, className, setAttributes } = props;
    const inspectorControls = {
        assigned_to: <ConditionalSelectControl
            help={ _x('Limit reviews to an assigned post.', 'admin-text', 'site-reviews') }
            label={ _x('Assigned To', 'admin-text', 'site-reviews') }
            onChange={ assigned_to => setAttributes({
                assigned_to: assigned_to,
                assigned_to_custom: ('custom' === assigned_to ? assigned_to_custom : ''),
            })}
            options={[
                { label: '-' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
                { label: _x('Assigned to the current page', 'admin-text', 'site-reviews'), value: 'post_id' },
                { label: _x('Assigned to the parent page', 'admin-text', 'site-reviews'), value: 'parent_id' },
                { label: _x('Assigned to a custom post ID', 'admin-text', 'site-reviews'), value: 'custom' },
            ]}
            value={ assigned_to }
        >
            <TextControl
                className="glsr-base-conditional-control"
                onChange={ assigned_to_custom => setAttributes({ assigned_to_custom }) }
                placeholder={ _x('Enter the post ID.', 'admin-text', 'site-reviews') }
                type="number"
                value={ assigned_to_custom }
            />
        </ConditionalSelectControl>,
        category: <SelectControl
            help={ _x('Limit reviews to a category.', 'admin-text', 'site-reviews') }
            label={ _x('Category', 'admin-text', 'site-reviews') }
            onChange={ category => setAttributes({ category }) }
            options={ categories }
            value={ category }
        />,
        pagination: <SelectControl
            help={ _x('Pagination should only be enabled once per page.', 'admin-text', 'site-reviews') }
            label={ _x('Pagination', 'admin-text', 'site-reviews') }
            onChange={ pagination => setAttributes({ pagination }) }
            options={[
                { label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
                { label: _x('Enabled', 'admin-text', 'site-reviews'), value: 'true' },
                { label: _x('Enabled (using ajax)', 'admin-text', 'site-reviews'), value: 'ajax' },
            ]}
            value={ pagination }
        />,
        type: <SelectControl
            help={ _x('Limit type of reviews.', 'admin-text', 'site-reviews') }
            label={ _x('Type', 'admin-text', 'site-reviews') }
            onChange={ type => setAttributes({ type }) }
            options={ types }
            value={ type }
        />,
        rating: <RangeControl
            help={ _x('Limit reviews to a minimum rating.', 'admin-text', 'site-reviews') }
            label={ _x('Minimum Rating', 'admin-text', 'site-reviews') }
            min={ 0 }
            max={ 5 }
            onChange={ rating => setAttributes({ rating }) }
            value={ rating }
        />,
        display: <RangeControl
            help={ _x('The number of reviews to display.', 'admin-text', 'site-reviews') }
            label={ _x('Display', 'admin-text', 'site-reviews') }
            min={ 1 }
            max={ 50 }
            onChange={ display => setAttributes({ display }) }
            value={ display }
        />,
        schema: <ToggleControl
            checked={ schema }
            help={ _x('The schema should only be enabled once per page.', 'admin-text', 'site-reviews') }
            label={ _x('Enable the schema?', 'admin-text', 'site-reviews') }
            onChange={ schema => setAttributes({ schema }) }
        />,
        hide: CheckboxControlList(GLSR.hideoptions.site_reviews, hide, setAttributes),
    };
    const inspectorAdvancedControls = {
        id: <TextControl
            label={ _x('Custom ID', 'admin-text', 'site-reviews') }
            onChange={ id => setAttributes({ id }) }
            value={ id }
        />,
    };
    return [
        <InspectorControls>
            <PanelBody title={ _x('Settings', 'admin-text', 'site-reviews')}>
                { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.reviews.InspectorControls', inspectorControls, props)) }
            </PanelBody>
        </InspectorControls>,
        <InspectorAdvancedControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.reviews.InspectorAdvancedControls', inspectorAdvancedControls, props)) }
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes }>
        </ServerSideRender>
    ];
};

wp.hooks.addFilter('blocks.getBlockAttributes', blockName, (attributes, block, unknown, saved) => {
    if (saved && saved.count) { // @deprecated since Site Reviews 4.1.0
        attributes.display = saved.count;
    }
    return attributes;
});

export default registerBlockType(
    blockName, {
        attributes: attributes,
        category: GLSR.nameprefix,
        description: _x('Display your most recent reviews.', 'admin-text', 'site-reviews'),
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
        title: _x('Latest Reviews', 'admin-text', 'site-reviews'),
    }
);
