import { CheckboxControlList } from './checkbox-control-list';
import { ReviewsIcon } from './icons';
import ConditionalSelectControl from './ConditionalSelectControl';
import assigned_to_options from './assigned_to-options';
import category_options from './category-options';
import type_options from './type-options';
import user_options from './user-options';

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
    user: { default: '', type: 'string' },
};

const edit = props => {
    props.attributes.post_id = jQuery('#post_ID').val();
    const { attributes: { assigned_to, assigned_to_custom, category, display, hide, id, pagination, rating, schema, type, user }, className, setAttributes } = props;
    const inspectorControls = {
        assigned_to: <ConditionalSelectControl
            label={ _x('Limit Reviews to an Assigned Post ID', 'admin-text', 'site-reviews') }
            onChange={ assigned_to => setAttributes({
                assigned_to: assigned_to,
                assigned_to_custom: ('custom' === assigned_to ? assigned_to_custom : ''),
            })}
            options={ assigned_to_options }
            value={ assigned_to }
        >
            <TextControl
                className="glsr-base-conditional-control"
                help={ _x('Separate multiple IDs with commas.', 'admin-text', 'site-reviews') }
                onChange={ assigned_to_custom => setAttributes({ assigned_to_custom }) }
                placeholder={ _x('Enter the Post IDs', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_to_custom }
            />
        </ConditionalSelectControl>,
        category: <SelectControl
            label={ _x('Limit Reviews to an Assigned Category', 'admin-text', 'site-reviews') }
            onChange={ category => setAttributes({ category }) }
            options={ category_options }
            value={ category }
        />,
        user: <SelectControl
            label={ _x('Limit Reviews to an Assigned User', 'admin-text', 'site-reviews') }
            onChange={ user => setAttributes({ user }) }
            options={ user_options }
            value={ user }
        />,
        pagination: <SelectControl
            label={ _x('Enable Pagination', 'admin-text', 'site-reviews') }
            onChange={ pagination => setAttributes({ pagination }) }
            options={[
                { label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
                { label: _x('Enabled', 'admin-text', 'site-reviews'), value: 'true' },
                { label: _x('Enabled (using ajax)', 'admin-text', 'site-reviews'), value: 'ajax' },
            ]}
            value={ pagination }
        />,
        type: <SelectControl
            label={ _x('Limit the Type of Reviews', 'admin-text', 'site-reviews') }
            onChange={ type => setAttributes({ type }) }
            options={ type_options }
            value={ type }
        />,
        display: <RangeControl
            label={ _x('Reviews Per Page', 'admin-text', 'site-reviews') }
            min={ 1 }
            max={ 50 }
            onChange={ display => setAttributes({ display }) }
            value={ display }
        />,
        rating: <RangeControl
            label={ _x('Minimum Rating', 'admin-text', 'site-reviews') }
            min={ 0 }
            max={ GLSR.maxrating }
            onChange={ rating => setAttributes({ rating }) }
            value={ rating }
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
