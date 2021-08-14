import { CheckboxControlList } from './checkbox-control-list';
import { FormIcon } from './icons';
import assign_to_options from './assign_to-options';
import category_options from './category-options';
import user_options from './user-options';
import transformWidgetAttributes from './transform-widget';
import ConditionalSelectControl from './ConditionalSelectControl';

const { _x } = wp.i18n;
const { createBlock, registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

const blockName = GLSR.nameprefix + '/form';

const attributes = {
    assign_to: { default: '', type: 'string' },
    assigned_posts: { default: '', type: 'string' },
    assigned_terms: { default: '', type: 'string' },
    assigned_users: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    hide: { default: '', type: 'string' },
    id: { default: '', type: 'string' },
    user: { default: '', type: 'string' },
};

const edit = props => {
    const { attributes: { assign_to, assigned_posts, assigned_terms, assigned_users, category, hide, id, user }, className, setAttributes } = props;
    const inspectorControls = {
        assign_to: <ConditionalSelectControl
            key={ 'assigned_posts' }
            label={ _x('Assign Reviews to a Page', 'admin-text', 'site-reviews') }
            onChange={ assign_to => setAttributes({
                assign_to: assign_to,
                assigned_posts: ('custom' === assign_to ? assigned_posts : ''),
            })}
            options={ assign_to_options }
            value={ assign_to }
        >
            <TextControl
                className="glsr-base-conditional-control"
                help={ _x('Separate with commas.', 'admin-text', 'site-reviews') }
                onChange={ assigned_posts => setAttributes({ assigned_posts }) }
                placeholder={ _x('Enter the Post IDs', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_posts }
            />
        </ConditionalSelectControl>,
        category: <ConditionalSelectControl
            key={ 'assigned_terms' }
            custom_value={ 'glsr_custom' }
            label={ _x('Assign Reviews to a Category', 'admin-text', 'site-reviews') }
            onChange={ category => setAttributes({
                category: category,
                assigned_terms: ('glsr_custom' === category ? assigned_terms : ''),
            })}
            options={ category_options }
            value={ category }
        >
            <TextControl
                className="glsr-base-conditional-control"
                help={ _x('Separate with commas.', 'admin-text', 'site-reviews') }
                onChange={ assigned_terms => setAttributes({ assigned_terms }) }
                placeholder={ _x('Enter the Category IDs or slugs', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_terms }
            />
        </ConditionalSelectControl>,
        user: <ConditionalSelectControl
            key={ 'assigned_users' }
            custom_value={ 'glsr_custom' }
            label={ _x('Assign Reviews to a User', 'admin-text', 'site-reviews') }
            onChange={ user => setAttributes({
                user: user,
                assigned_users: ('glsr_custom' === user ? assigned_users : ''),
            })}
            options={ user_options }
            value={ user }
        >
            <TextControl
                className="glsr-base-conditional-control"
                help={ _x('Separate with commas.', 'admin-text', 'site-reviews') }
                onChange={ assigned_users => setAttributes({ assigned_users }) }
                placeholder={ _x('Enter the User IDs or usernames', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_users }
            />
        </ConditionalSelectControl>,
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

export default registerBlockType(blockName, {
    attributes: attributes,
    category: GLSR.nameprefix,
    description: _x('Display a review form.', 'admin-text', 'site-reviews'),
    edit: edit,
    example: {},
    icon: {src: FormIcon},
    keywords: ['reviews', 'form'],
    save: () => null,
    title: _x('Submit a Review', 'admin-text', 'site-reviews'),
    transforms: {
        from: [{
            type: 'block',
            blocks: ['core/legacy-widget'],
            isMatch: ({ idBase, instance }) => idBase === 'glsr_site-reviews-form' && !! instance?.raw,
            transform: ({ instance }) => createBlock(blockName, transformWidgetAttributes(instance, attributes)),
        }],
    },
});
