const { _x } = wp.i18n;
const { createBlock, registerBlockType } = wp.blocks;
const { InspectorAdvancedControls, InspectorControls } = wp.blockEditor;
const { Icon, PanelBody, TextControl } = wp.components;
const {
    CheckboxControlList,
    ServerSideRender,
    onRender,
    transformWidgetAttributes,
} = GLSR.blocks;

const blockName = GLSR.nameprefix + '/review';

const attributes = {
    className: { default: '', type: 'string' },
    hide: { default: '', type: 'string' },
    id: { default: '', type: 'string' },
    post_id: { default: '', type: 'string' },
};

const edit = props => {
    const { attributes: { hide, id, post_id }, className, setAttributes } = props;
    const inspectorControls = {
        post_id: <TextControl
            key={ 'post_id' }
            label={ _x('Review Post ID', 'admin-text', 'site-reviews') }
            onChange={ post_id => setAttributes({ post_id }) }
            placeholder={ _x('Enter a Review Post ID', 'admin-text', 'site-reviews') }
            value={ post_id }
            __next40pxDefaultSize
            __nextHasNoMarginBottom
        />,
        hide: CheckboxControlList(GLSR.hideoptions.site_review, hide, setAttributes),
    };
    const inspectorPanels = {
        panel_settings: <PanelBody title={ _x('Settings', 'admin-text', 'site-reviews')}>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.review.InspectorControls', inspectorControls, props)) }
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
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.review.InspectorPanels', inspectorPanels, props)) }
        </InspectorControls>,
        <InspectorAdvancedControls>
            { Object.values(wp.hooks.applyFilters(GLSR.nameprefix+'.review.InspectorAdvancedControls', inspectorAdvancedControls, props)) }
        </InspectorAdvancedControls>,
        <ServerSideRender block={ blockName } attributes={ props.attributes } onRender={ onRender }>
        </ServerSideRender>
    ];
};

wp.hooks.addFilter('blocks.getBlockAttributes', blockName, (attributes, block, unknown, saved) => {
    return attributes;
});

export default registerBlockType(blockName, {
    attributes: attributes,
    category: GLSR.nameprefix,
    description: _x('Display a single review.', 'admin-text', 'site-reviews'),
    edit: edit,
    example: {},
    icon: () => (
        <Icon icon={ <svg><path d="M18.646 1.821a3.44 3.44 0 0 1 1.772.481 3.57 3.57 0 0 1 1.281 1.285 3.46 3.46 0 0 1 .479 1.775v8.126a3.51 3.51 0 0 1-.477 1.78c-.315.545-.746.981-1.283 1.298a3.44 3.44 0 0 1-1.772.481h-6.28l-.064.052-6.144 4.914c-.392.304-.976.263-1.359-.066-.358-.307-.472-.83-.337-1.233l1.377-3.742h-.485a3.44 3.44 0 0 1-1.567-.369l-.206-.112a3.57 3.57 0 0 1-1.281-1.285 3.46 3.46 0 0 1-.479-1.775V5.362a3.46 3.46 0 0 1 .479-1.775 3.57 3.57 0 0 1 1.281-1.285 3.44 3.44 0 0 1 1.772-.481h13.292zm0 1.5H5.354a1.94 1.94 0 0 0-1.01.273 2.07 2.07 0 0 0-.749.752 1.96 1.96 0 0 0-.273 1.016v8.069a1.96 1.96 0 0 0 .273 1.016 2.07 2.07 0 0 0 .749.752 1.94 1.94 0 0 0 1.01.273H7.99l-.371 1.009-1.275 3.464.103-.082 1.871-1.496 1.1-.88 1.955-1.564.263-.21.205-.164h6.806a1.94 1.94 0 0 0 1.01-.273c.315-.186.561-.435.747-.757a2.01 2.01 0 0 0 .275-1.029V5.362a1.96 1.96 0 0 0-.273-1.016 2.07 2.07 0 0 0-.749-.752 1.94 1.94 0 0 0-1.01-.273zm-5.783 1.995v5.757c0 .087-.028.444-.038.592v.061l1.365-.042v1.17H9.81V11.81l.688-.013.407-.029c.084-.021.168-.042.232-.105s.105-.147.147-.274a1.02 1.02 0 0 0 .034-.136V7.578H9.81v-.754c.829-.226 1.507-.829 1.96-1.507h1.093z"/></svg> } />
    ),
    keywords: ['review'],
    save: () => null,
    supports: {
        html: false,
    },
    title: _x('Single Review', 'admin-text', 'site-reviews'),
    transforms: {
        from: [{
            type: 'block',
            blocks: ['core/legacy-widget'],
            isMatch: ({ idBase, instance }) => idBase === 'glsr_site-review' && !! instance?.raw,
            transform: ({ instance }) => createBlock(blockName, transformWidgetAttributes(instance, attributes)),
        }],
    },
});
