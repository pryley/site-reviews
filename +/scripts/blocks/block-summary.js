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

const blockName = GLSR.nameprefix + '/summary';

const attributes = {
    assigned_to: { default: '', type: 'string' },
    assigned_posts: { default: '', type: 'string' },
    assigned_terms: { default: '', type: 'string' },
    assigned_users: { default: '', type: 'string' },
    category: { default: '', type: 'string' },
    className: { default: '', type: 'string' },
    hide: { default: '', type: 'string' },
    post_id: { default: '', type: 'string' },
    rating: { default: 0, type: 'number' },
    rating_field: { default: '', type: 'string' },
    schema: { default: false, type: 'boolean' },
    terms: { default: '', type: 'string' },
    type: { default: 'local', type: 'string' },
    user: { default: '', type: 'string' },
};

const edit = props => {
    props.attributes.post_id = jQuery('#post_ID').val();
    const { attributes: { assigned_to, assigned_posts, assigned_terms, assigned_users, category, display, hide, id, pagination, rating, rating_field, schema, terms, type, user }, className, setAttributes } = props;
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
            />
        </ConditionalSelectControl>,
        terms: <SelectControl
            key={ 'terms' }
            label={ _x('Limit Reviews to terms', 'admin-text', 'site-reviews') }
            onChange={ terms => setAttributes({ terms }) }
            options={ TermOptions }
            value={ terms }
        />,
        type: <SelectControl
            key={ 'type' }
            label={ _x('Limit the Type of Reviews', 'admin-text', 'site-reviews') }
            onChange={ type => setAttributes({ type }) }
            options={ TypeOptions }
            value={ type }
        />,
        rating: <RangeControl
            key={ 'rating' }
            label={ _x('Minimum Rating', 'admin-text', 'site-reviews') }
            min={ 0 }
            max={ GLSR.maxrating }
            onChange={ rating => setAttributes({ rating }) }
            value={ rating }
        />,
        schema: <ToggleControl
            key={ 'schema' }
            checked={ schema }
            help={ _x('The schema should only be enabled once per page.', 'admin-text', 'site-reviews') }
            label={ _x('Enable the schema?', 'admin-text', 'site-reviews') }
            onChange={ schema => setAttributes({ schema }) }
        />,
        hide: CheckboxControlList(GLSR.hideoptions.site_reviews_summary, hide, setAttributes),
    };
    const inspectorPanels = {
        panel_settings: <PanelBody title={ _x('Settings', 'admin-text', 'site-reviews')}>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.summary.InspectorControls', inspectorControls, props)) }
        </PanelBody>
    };
    const inspectorAdvancedControls = {
        id: <TextControl
            label={ _x('Custom ID', 'admin-text', 'site-reviews') }
            onChange={ id => setAttributes({ id }) }
            value={ id }
        />,
        rating_field: <TextControl
            help={ _x('Use the Review Forms add-on to add custom rating fields.', 'admin-text', 'site-reviews') }
            label={ _x('Custom Rating Field Name', 'admin-text', 'site-reviews') }
            onChange={ rating_field => setAttributes({ rating_field }) }
            value={ rating_field }
        />,
    };
    return [
        <InspectorControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.summary.InspectorPanels', inspectorPanels, props)) }
        </InspectorControls>,
        <InspectorAdvancedControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.summary.InspectorAdvancedControls', inspectorAdvancedControls, props)) }
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes } onRender={ onRender }>
        </ServerSideRender>
    ];
};

export default registerBlockType(blockName, {
    attributes: attributes,
    category: GLSR.nameprefix,
    description: _x('Display a summary of your reviews.', 'admin-text', 'site-reviews'),
    edit: edit,
    example: {},
    icon: () => (
        <Icon icon={ <svg><path d="M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318L1.609 9.981a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694c-.171-.995.16-2.009.885-2.713l2.693-2.617-3.724-.539c-1.001-.145-1.866-.772-2.313-1.675L12 4.95zM18.651 1a3.95 3.95 0 0 1 2.277.68l-.518.824c-.536-.342-1.13-.54-1.769-.54-.842 0-1.418.365-1.418.941 0 .522.491.725 1.31.842l.437.059c1.022.14 2.03.563 2.03 1.733 0 1.283-1.161 1.985-2.525 1.985-.855 0-1.881-.284-2.534-.846l.554-.81c.432.396 1.247.693 1.976.693.824 0 1.472-.351 1.472-.932 0-.495-.495-.725-1.418-.851l-.491-.068c-.936-.131-1.868-.572-1.868-1.742C16.167 1.702 17.287 1 18.651 1z"/></svg> } />
    ),
    keywords: ['reviews', 'summary'],
    save: () => null,
    title: _x('Rating Summary', 'admin-text', 'site-reviews'),
    transforms: {
        from: [{
            type: 'block',
            blocks: ['core/legacy-widget'],
            isMatch: ({ idBase, instance }) => idBase === 'glsr_site-reviews-summary' && !! instance?.raw,
            transform: ({ instance }) => createBlock(blockName, transformWidgetAttributes(instance, attributes)),
        }],
    },
});
