import { CheckboxControlList } from './checkbox-control-list';
import { FormIcon } from './icons';
import assign_to_options from './assign_to-options';
import category_options from './category-options';
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
            label={ _x('Assign Reviews to a Post ID', 'admin-text', 'site-reviews') }
            onChange={ assign_to => setAttributes({
                assign_to: assign_to,
                assign_to_custom: ('custom' === assign_to ? assign_to_custom : ''),
            })}
            options={ assign_to_options }
            value={ assign_to }
        >
            <TextControl
                className="glsr-base-conditional-control"
                help={ _x('Separate multiple IDs with commas.', 'admin-text', 'site-reviews') }
                onChange={ assign_to_custom => setAttributes({ assign_to_custom }) }
                placeholder={ _x('Enter the Post IDs', 'admin-text', 'site-reviews') }
                type="text"
                value={ assign_to_custom }
            />
        </ConditionalSelectControl>,
        category: <SelectControl
            label={ _x('Assign Reviews to a Category', 'admin-text', 'site-reviews') }
            onChange={ category => setAttributes({ category }) }
            options={ category_options }
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
