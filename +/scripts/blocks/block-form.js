import { CheckboxControlList } from './checkbox-control-list';
import { FormIcon } from './icons';
import categories from './categories';
import ConditionalSelectControl from './ConditionalSelectControl';

const { _x } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

const blockName = GLSR.nameprefix + '/form';

const attributes = {
    assign_to: { default: '', type: 'string' },
    assign_to_custom: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    hide: { default: '', type: 'string' },
    id: { default: '', type: 'string' },
};

const edit = props => {
    const { attributes: { assign_to, assign_to_custom, category, hide, id }, className, setAttributes } = props;
    const inspectorControls = {
        assign_to: <ConditionalSelectControl
            help={ _x('Assign reviews to a post.', 'admin-text', 'site-reviews') }
            label={ _x('Assign To', 'admin-text', 'site-reviews') }
            onChange={ assign_to => setAttributes({
                assign_to: assign_to,
                assign_to_custom: ('custom' === assign_to ? assign_to_custom : ''),
            })}
            options={[
                { label: '-' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
                { label: _x('Assign to the current page', 'admin-text', 'site-reviews'), value: 'post_id' },
                { label: _x('Assign to the parent page', 'admin-text', 'site-reviews'), value: 'parent_id' },
                { label: _x('Assign to a custom post ID', 'admin-text', 'site-reviews'), value: 'custom' },
            ]}
            value={ assign_to }
        >
            <TextControl
                className="glsr-base-conditional-control"
                onChange={ assign_to_custom => setAttributes({ assign_to_custom }) }
                placeholder={ _x('Enter the post ID.', 'admin-text', 'site-reviews') }
                type="number"
                value={ assign_to_custom }
            />
        </ConditionalSelectControl>,
        category: <SelectControl
            help={ _x('Assign reviews to a category.', 'admin-text', 'site-reviews') }
            label={ _x('Category', 'admin-text', 'site-reviews') }
            onChange={ category => setAttributes({ category }) }
            options={ categories }
            value={ category }
        />,
        hide: CheckboxControlList(GLSR.hideoptions.site_reviews_form, hide, setAttributes),
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
                { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.form.InspectorControls', inspectorControls, props)) }
            </PanelBody>
        </InspectorControls>,
        <InspectorAdvancedControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.form.InspectorAdvancedControls', inspectorAdvancedControls, props)) }
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes }>
        </ServerSideRender>
    ];
};

export default registerBlockType(
    blockName, {
        attributes: attributes,
        category: GLSR.nameprefix,
        description: _x('Display a review submission form.', 'admin-text', 'site-reviews'),
        edit: edit,
        example: {},
        icon: {src: FormIcon},
        keywords: ['reviews', 'form'],
        save: () => null,
        title: _x('Submit a Review', 'admin-text', 'site-reviews'),
    }
);
