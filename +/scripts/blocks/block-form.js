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
            help={ _x('This should be a unique value.', 'admin-text', 'site-reviews') }
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
        <Icon icon={ <svg><path d="M9.506 19.714H3.351c-.43 0-.779.288-.779.643v1.286c0 .355.349.643.779.643h6.156c.43 0 .779-.288.779-.643v-1.286c0-.355-.349-.643-.779-.643zm11.187-7.286H3.306a.73.73 0 0 0-.735.723v4.339a.73.73 0 0 0 .735.723h17.388a.73.73 0 0 0 .735-.723v-4.339a.73.73 0 0 0-.735-.723zm0-3H3.306c-.406 0-.735.349-.735.78v-.06c0 .431.329.78.735.78h17.388c.406 0 .735-.349.735-.78v.06c0-.431-.329-.78-.735-.78zM5.831 1.714c.067-.001.128.04.151.103l.79 1.892 1.889.151 1.891-.151.787-1.892c.023-.059.078-.098.14-.103h.107c.067-.001.128.04.151.103l.79 1.892 1.89.151 1.89-.151.787-1.892c.023-.059.078-.098.14-.103h.107c.067-.001.128.04.151.103l.79 1.892 2.052.164c.066.004.122.049.141.112l.026.08c.022.062.003.131-.048.173l-1.548 1.33.475 1.994c.016.064-.008.131-.061.17l-.093.051a.16.16 0 0 1-.18 0L17.3 6.724l-1.756 1.069a.16.16 0 0 1-.18 0l-.071-.048c-.053-.039-.077-.106-.061-.17l.462-2.007-1.273-1.093-1.271 1.093.475 1.994c.016.064-.008.131-.061.17l-.093.051a.16.16 0 0 1-.18 0l-1.747-1.06-1.756 1.069a.16.16 0 0 1-.18 0l-.071-.048c-.053-.039-.077-.106-.061-.17l.462-2.007-1.273-1.093-1.271 1.093.475 1.994c.016.064-.008.131-.061.17l-.093.051a.16.16 0 0 1-.18 0L5.79 6.724 4.033 7.794a.16.16 0 0 1-.18 0l-.071-.048c-.053-.039-.077-.106-.061-.17l.462-2.007-1.548-1.33c-.054-.04-.078-.109-.058-.173l.026-.08c.019-.063.075-.108.141-.112l2.052-.164.787-1.892c.023-.059.078-.098.14-.103h.107z"/></svg> } />
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
