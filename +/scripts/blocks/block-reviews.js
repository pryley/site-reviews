const { _x } = wp.i18n;
const { createBlock, registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.blockEditor;
const { Icon, PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } = wp.components;
const {
    AssignedPostsOptions,
    AssignedTermsOptions,
    AssignedUsersOptions,
    CheckboxControlList,
    ConditionalSelectControl,
    ServerSideRender,
    onRender,
    TermOptions,
    TypeOptions,
    transformWidgetAttributes,
} = GLSR.blocks;

const blockName = GLSR.nameprefix + '/reviews';

const attributes = {
    assigned_to: { default: '', type: 'string' },
    assigned_posts: { default: '', type: 'string' },
    assigned_terms: { default: '', type: 'string' },
    assigned_users: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    display: { default: 5, type: 'number' },
    hide: { default: '', type: 'string' },
    id: { default: '', type: 'string' },
    pagination: { default: '', type: 'string' },
    post_id: { default: '', type: 'string' },
    rating: { default: 0, type: 'number' },
    schema: { default: false, type: 'boolean' },
    terms: { default: '', type: 'string' },
    type: { default: 'local', type: 'string' },
    user: { default: '', type: 'string' },
};

const edit = props => {
    props.attributes.post_id = jQuery('#post_ID').val();
    const { attributes: { assigned_to, assigned_posts, assigned_terms, assigned_users, category, display, hide, id, pagination, rating, schema, terms, type, user }, className, setAttributes } = props;
    const inspectorControls = {
        assigned_to: <ConditionalSelectControl
            key={ 'assigned_posts' }
            label={ _x('Limit Reviews to an Assigned Page', 'admin-text', 'site-reviews') }
            onChange={ assigned_to => setAttributes({
                assigned_to: assigned_to,
                assigned_posts: ('custom' === assigned_to ? assigned_posts : ''),
            })}
            options={ AssignedPostsOptions }
            value={ assigned_to }
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
            label={ _x('Limit Reviews to an Assigned Category', 'admin-text', 'site-reviews') }
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
            label={ _x('Limit Reviews to an Assigned User', 'admin-text', 'site-reviews') }
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
        terms: <SelectControl
            key={ 'terms' }
            label={ _x('Limit Reviews to terms', 'admin-text', 'site-reviews') }
            onChange={ terms => setAttributes({ terms }) }
            options={ TermOptions }
            value={ terms }
            __next36pxDefaultSize
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        pagination: <SelectControl
            key={ 'pagination' }
            label={ _x('Pagination Type', 'admin-text', 'site-reviews') }
            onChange={ pagination => setAttributes({ pagination }) }
            options={[
                { label: _x('No Pagination', 'admin-text', 'site-reviews'), value: '' },
                { label: _x('Load More Button', 'admin-text', 'site-reviews'), value: 'loadmore' },
                { label: _x('Pagination (AJAX)', 'admin-text', 'site-reviews'), value: 'ajax' },
                { label: _x('Pagination (page reload)', 'admin-text', 'site-reviews'), value: 'true' },
            ]}
            value={ pagination }
            __next36pxDefaultSize
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        type: <SelectControl
            key={ 'type' }
            label={ _x('Limit the Type of Reviews', 'admin-text', 'site-reviews') }
            onChange={ type => setAttributes({ type }) }
            options={ TypeOptions }
            value={ type }
            __next36pxDefaultSize
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        display: <RangeControl
            key={ 'display' }
            label={ _x('Reviews Per Page', 'admin-text', 'site-reviews') }
            min={ 1 }
            max={ 50 }
            onChange={ display => setAttributes({ display }) }
            value={ display }
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        rating: <RangeControl
            key={ 'rating' }
            label={ _x('Minimum Rating', 'admin-text', 'site-reviews') }
            min={ 0 }
            max={ GLSR.maxrating }
            onChange={ rating => setAttributes({ rating }) }
            value={ rating }
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        schema: <ToggleControl
            key={ 'schema' }
            checked={ schema }
            help={ _x('The schema should only be enabled once per page.', 'admin-text', 'site-reviews') }
            label={ _x('Enable the schema?', 'admin-text', 'site-reviews') }
            onChange={ schema => setAttributes({ schema }) }
            __nextHasNoMarginBottom
        />,
        hide: CheckboxControlList(GLSR.hideoptions.site_reviews, hide, setAttributes),
    };
    const inspectorPanels = {
        panel_settings: <PanelBody title={ _x('Settings', 'admin-text', 'site-reviews')}>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.reviews.InspectorControls', inspectorControls, props)) }
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
    };
    return [
        <InspectorControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.reviews.InspectorPanels', inspectorPanels, props)) }
        </InspectorControls>,
        <InspectorAdvancedControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.reviews.InspectorAdvancedControls', inspectorAdvancedControls, props)) }
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes } onRender={ onRender }>
        </ServerSideRender>
    ];
};

wp.hooks.addFilter('blocks.getBlockAttributes', blockName, (attributes, block, unknown, saved) => {
    if (saved && saved.count) { // @deprecated in 4.1.0
        attributes.display = saved.count;
    }
    return attributes;
});

export default registerBlockType(blockName, {
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
    icon: () => (
        <Icon icon={ <svg><path d="M18.646 1.821a3.44 3.44 0 0 1 1.772.481 3.57 3.57 0 0 1 1.281 1.285 3.46 3.46 0 0 1 .479 1.775v8.126a3.51 3.51 0 0 1-.477 1.78c-.315.545-.746.981-1.283 1.298a3.44 3.44 0 0 1-1.772.481h-6.28l-.064.052-6.144 4.914c-.392.304-.976.263-1.359-.066-.358-.307-.472-.83-.337-1.233l1.377-3.742h-.485a3.44 3.44 0 0 1-1.567-.369l-.206-.112a3.57 3.57 0 0 1-1.281-1.285 3.46 3.46 0 0 1-.479-1.775V5.362a3.46 3.46 0 0 1 .479-1.775 3.57 3.57 0 0 1 1.281-1.285 3.44 3.44 0 0 1 1.772-.481h13.292zm0 1.5H5.354a1.94 1.94 0 0 0-1.01.273 2.07 2.07 0 0 0-.749.752 1.96 1.96 0 0 0-.273 1.016v8.069a1.96 1.96 0 0 0 .273 1.016 2.07 2.07 0 0 0 .749.752 1.94 1.94 0 0 0 1.01.273H7.99l-.371 1.009-1.275 3.464.103-.082 1.871-1.496 1.1-.88 1.955-1.564.263-.21.205-.164h6.806a1.94 1.94 0 0 0 1.01-.273c.315-.186.561-.435.747-.757a2.01 2.01 0 0 0 .275-1.029V5.362a1.96 1.96 0 0 0-.273-1.016 2.07 2.07 0 0 0-.749-.752 1.94 1.94 0 0 0-1.01-.273zm-6.582 1.4a.22.22 0 0 1 .211.144l1.106 2.648 2.873.229a.22.22 0 0 1 .198.157l.036.112a.22.22 0 0 1-.067.243l-2.167 1.861.665 2.792a.23.23 0 0 1-.085.238l-.13.072c-.076.051-.176.051-.252 0l-2.446-1.484-2.459 1.497c-.076.051-.176.051-.252 0l-.099-.067a.23.23 0 0 1-.085-.238l.647-2.81-2.167-1.861a.22.22 0 0 1-.081-.243l.036-.112a.22.22 0 0 1 .198-.157l2.873-.229 1.101-2.648a.23.23 0 0 1 .196-.144h.15z"/></svg> } />
    ),
    keywords: ['reviews'],
    save: () => null,
    supports: {
        html: false,
    },
    title: _x('Latest Reviews', 'admin-text', 'site-reviews'),
    transforms: {
        from: [{
            type: 'block',
            blocks: ['core/legacy-widget'],
            isMatch: ({ idBase, instance }) => idBase === 'glsr_site-reviews' && !! instance?.raw,
            transform: ({ instance }) => createBlock(blockName, transformWidgetAttributes(instance, attributes)),
        }],
    },
});
