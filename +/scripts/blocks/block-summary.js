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
    id: { default: '', type: 'string' },
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
        hide: CheckboxControlList(GLSR.hideoptions.site_reviews_summary, hide, setAttributes),
    };
    const inspectorPanels = {
        panel_settings: <PanelBody title={ _x('Settings', 'admin-text', 'site-reviews')}>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.summary.InspectorControls', inspectorControls, props)) }
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
        rating_field: <TextControl
            help={ _x('Use the Review Forms addon to add custom rating fields.', 'admin-text', 'site-reviews') }
            label={ _x('Custom Rating Field Name', 'admin-text', 'site-reviews') }
            onChange={ rating_field => setAttributes({ rating_field }) }
            value={ rating_field }
            __next40pxDefaultSize
            __nextHasNoMarginBottom
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
        <Icon icon={ <svg><path d="M20.694 18.429c.406 0 .735.336.735.75v1.5c0 .414-.329.75-.735.75H3.306c-.406 0-.735-.336-.735-.75v-1.5c0-.414.329-.75.735-.75h17.388zm-.513 1.071H6.39c-.097 0-.176.064-.176.143v.571c0 .079.079.143.176.143h13.79c.097 0 .176-.064.176-.143v-.571c0-.079-.079-.143-.176-.143zm.513-4.929c.406 0 .735.336.735.75v1.5c0 .414-.329.75-.735.75H3.306c-.406 0-.735-.336-.735-.75v-1.5c0-.414.329-.75.735-.75h17.388zm-.527 1.188H8.976c-.105 0-.19.064-.19.143v.571c0 .079.085.143.19.143h11.192c.105 0 .19-.064.19-.143v-.571c0-.079-.085-.143-.19-.143zm.527-5.045c.406 0 .735.336.735.75v1.5c0 .414-.329.75-.735.75H3.306c-.406 0-.735-.336-.735-.75v-1.5c0-.414.329-.75.735-.75h17.388zm-.551 1.071h-2.571c-.118 0-.214.064-.214.143v.571c0 .079.096.143.214.143h2.571c.118 0 .214-.064.214-.143v-.571c0-.079-.096-.143-.214-.143zM6.944 1.786l-.083 1.678-3.253.062v1.844c.083-.041.166-.083.269-.104l.332-.083.174-.028.08-.009.078-.004c.104 0 .228-.021.332-.021.435 0 .808.062 1.119.186a2.01 2.01 0 0 1 .767.518c.207.228.373.456.477.746s.145.58.145.87c0 .394-.062.746-.207 1.057s-.352.559-.622.767a2.59 2.59 0 0 1-.85.456c-.332.104-.642.145-.974.145-.352 0-.663-.041-.912-.124s-.497-.207-.684-.332-.311-.29-.414-.456-.145-.332-.145-.497a.83.83 0 0 1 .062-.311.98.98 0 0 1 .145-.249c.062-.083.145-.124.228-.186s.186-.062.29-.062c.124 0 .228.021.311.062s.145.104.207.166c.033.033.055.072.07.114l.033.114c.021.083.041.145.041.228 0 .062 0 .124-.021.207s-.062.145-.104.207c.021.062.083.124.124.166s.145.083.207.124.166.062.249.083a.93.93 0 0 0 .228.021 1.09 1.09 0 0 0 .539-.124c.145-.083.249-.207.352-.352s.145-.29.186-.456a2.26 2.26 0 0 0 .062-.539v-.352c0-.124-.021-.249-.062-.373-.021-.145-.062-.269-.104-.394s-.145-.249-.228-.332-.207-.186-.332-.249-.311-.104-.518-.104c-.145 0-.332.041-.539.083s-.456.166-.705.311l-.435-.352-.041-3.461h2.694c.124 0 .228 0 .311-.021s.186-.041.269-.104.104-.104.145-.207a.86.86 0 0 0 .062-.332h.642zm5.203-.293c.092-.002.175.055.207.141l1.083 2.594 2.814.225c.091.006.168.067.194.154l.035.11c.03.085.004.18-.066.238l-2.123 1.823.652 2.735c.021.088-.012.179-.084.233l-.128.07a.22.22 0 0 1-.247 0L12.09 8.364 9.681 9.83a.22.22 0 0 1-.247 0l-.097-.066c-.072-.054-.105-.146-.084-.233l.634-2.753-2.123-1.823c-.074-.054-.106-.15-.079-.238l.035-.11c.026-.087.103-.149.194-.154l2.814-.225 1.079-2.594c.032-.081.107-.135.192-.141h.147z"/></svg> } />
    ),
    keywords: ['reviews', 'summary'],
    save: () => null,
    supports: {
        html: false,
    },
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
