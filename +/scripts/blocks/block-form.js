import { CheckboxControlList } from './checkbox-control-list';
import { FormIcon } from './icons';
import categories from './categories';
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, ServerSideRender, TextControl } = wp.components;
const attributes = {
    assign_to: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    hide: { default: '', type: 'string' },
    id: { default: '', type: 'string' },
};
const blockName = GLSR.nameprefix + '/form';

const edit = props => {
    const { attributes: { assign_to, category, hide, id }, className, setAttributes } = props;
    return [
        <InspectorControls>
            <PanelBody title={ __('Settings', 'site-reviews')}>
                <TextControl
                    help={ __('Assign reviews to a post ID. You can also enter "post_id" to use the ID of the current page, or "parent_id" to use the ID of the parent page.', 'site-reviews') }
                    label={ __('Assign To', 'site-reviews') }
                    onChange={ assign_to => setAttributes({ assign_to }) }
                    value={ assign_to }
                />
                <SelectControl
                    help={ __('Assign reviews to a category.', 'site-reviews') }
                    label={ __('Category', 'site-reviews') }
                    onChange={ category => setAttributes({ category }) }
                    options={ categories }
                    value={ category }
                />
                { CheckboxControlList(GLSR.hideoptions.site_reviews_form, hide, setAttributes) }
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

export default registerBlockType(
    blockName, {
        attributes: attributes,
        category: GLSR.nameprefix,
        description: __('Display a review submission form.', 'site-reviews'),
        edit: edit,
        example: {},
        icon: {src: FormIcon},
        keywords: ['reviews', 'form'],
        save: () => null,
        title: __('Submit a Review', 'site-reviews'),
    }
);
