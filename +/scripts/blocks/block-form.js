const { _x } = wp.i18n;
const { createBlock, registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.blockEditor;
const { Icon, PanelBody, SelectControl, TextControl } = wp.components;
const {
    AssignedPostsOptions,
    AssignedTermsOptions,
    AssignedUsersOptions,
    CheckboxControlList,
    ConditionalSelectControl,
    ServerSideRender,
    onRender,
    transformWidgetAttributes,
} = GLSR.blocks;

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
    reviews_id: { default: '', type: 'string' },
    user: { default: '', type: 'string' },
};

const edit = props => {
    const { attributes: { assign_to, assigned_posts, assigned_terms, assigned_users, category, hide, id, reviews_id, user }, className, setAttributes } = props;
    const inspectorControls = {
        assign_to: <ConditionalSelectControl
            key={ 'assigned_posts' }
            label={ _x('Assign Reviews to a Page', 'admin-text', 'site-reviews') }
            onChange={ assign_to => setAttributes({
                assign_to: assign_to,
                assigned_posts: ('custom' === assign_to ? assigned_posts : ''),
            })}
            options={ AssignedPostsOptions }
            value={ assign_to }
        >
            <TextControl
                key={ 'custom_assigned_posts' }
                className="glsr-base-conditional-control"
                help={ _x('Separate values with a comma.', 'admin-text', 'site-reviews') }
                onChange={ assigned_posts => setAttributes({ assigned_posts }) }
                placeholder={ _x('Enter the Post IDs', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_posts }
                __next40pxDefaultSize
                __nextHasNoMarginBottom
            />
        </ConditionalSelectControl>,
        category: <ConditionalSelectControl
            key={ 'assigned_terms' }
            label={ _x('Assign Reviews to a Category', 'admin-text', 'site-reviews') }
            onChange={ category => setAttributes({
                category: category,
                assigned_terms: ('custom' === category ? assigned_terms : ''),
            })}
            options={ AssignedTermsOptions }
            value={ category }
        >
            <TextControl
                key={ 'custom_assigned_terms' }
                className="glsr-base-conditional-control"
                help={ _x('Separate values with a comma.', 'admin-text', 'site-reviews') }
                onChange={ assigned_terms => setAttributes({ assigned_terms }) }
                placeholder={ _x('Enter the Category IDs or slugs', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_terms }
                __next40pxDefaultSize
                __nextHasNoMarginBottom
            />
        </ConditionalSelectControl>,
        user: <ConditionalSelectControl
            key={ 'assigned_users' }
            label={ _x('Assign Reviews to a User', 'admin-text', 'site-reviews') }
            onChange={ user => setAttributes({
                user: user,
                assigned_users: ('custom' === user ? assigned_users : ''),
            })}
            options={ AssignedUsersOptions }
            value={ user }
        >
            <TextControl
                key={ 'custom_assigned_users' }
                className="glsr-base-conditional-control"
                help={ _x('Separate values with a comma.', 'admin-text', 'site-reviews') }
                onChange={ assigned_users => setAttributes({ assigned_users }) }
                placeholder={ _x('Enter the User IDs or usernames', 'admin-text', 'site-reviews') }
                type="text"
                value={ assigned_users }
                __next40pxDefaultSize
                __nextHasNoMarginBottom
            />
        </ConditionalSelectControl>,
        hide: CheckboxControlList(GLSR.hideoptions.site_reviews_form, hide, setAttributes),
    };
    const inspectorPanels = {
        panel_settings: <PanelBody title={ _x('Settings', 'admin-text', 'site-reviews')}>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.form.InspectorControls', inspectorControls, props)) }
        </PanelBody>
    };
    const inspectorAdvancedControls = {
        id: <TextControl
            label={ _x('Custom ID', 'admin-text', 'site-reviews') }
            onChange={ id => setAttributes({ id }) }
            value={ id }
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        reviews_id: <TextControl
            help={ _x('Enter the Custom ID of a reviews block, shortcode, or widget where the review should be displayed after submission.', 'admin-text', 'site-reviews') }
            label={ _x('Custom Reviews ID', 'admin-text', 'site-reviews') }
            onChange={ reviews_id => setAttributes({ reviews_id }) }
            value={ reviews_id }
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
    };
    return [
        <InspectorControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.form.InspectorPanels', inspectorPanels, props)) }
        </InspectorControls>,
        <InspectorAdvancedControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.form.InspectorAdvancedControls', inspectorAdvancedControls, props)) }
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes } onRender={ onRender }>
        </ServerSideRender>
    ];
};

export default registerBlockType(blockName, {
    attributes: attributes,
    category: GLSR.nameprefix,
    description: _x('Display a review form.', 'admin-text', 'site-reviews'),
    edit: edit,
    example: {},
    icon: () => (
        <Icon icon={ <svg><path d="M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318L1.609 9.981a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694c-.171-.995.16-2.009.885-2.713l2.693-2.617-3.724-.539c-1.001-.145-1.866-.772-2.313-1.675L12 4.95zM21 1v.963h-3.479v1.683h3.272v.963h-3.272V7.3h-1.017V1H21z"/></svg> } />
    ),
    keywords: ['reviews', 'form'],
    save: () => null,
    supports: {
        html: false,
    },
    title: _x('Review Form', 'admin-text', 'site-reviews'),
    transforms: {
        from: [{
            type: 'block',
            blocks: ['core/legacy-widget'],
            isMatch: ({ idBase, instance }) => idBase === 'glsr_site-reviews-form' && !! instance?.raw,
            transform: ({ instance }) => createBlock(blockName, transformWidgetAttributes(instance, attributes)),
        }],
    },
});
