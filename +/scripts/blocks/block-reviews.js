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
        <Icon icon={ <svg><path d="M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318L1.609 9.981a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694c-.171-.995.16-2.009.885-2.713l2.693-2.617-3.724-.539c-1.001-.145-1.866-.772-2.313-1.675L12 4.95zM18.768 1C20.217 1 21 1.648 21 2.823c0 1.071-.819 1.782-2.102 1.827L20.973 7.3h-1.26L17.706 4.65h-.513V7.3h-1.017V1h2.592zm-.027.954h-1.548v1.773h1.548c.819 0 1.202-.297 1.202-.905 0-.599-.405-.869-1.202-.869z"/></svg> } />
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
