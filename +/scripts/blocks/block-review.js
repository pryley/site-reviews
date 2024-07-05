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
        <Icon icon={ <svg><path d="M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318l-4.8-4.674a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694a3.06 3.06 0 0 1 .885-2.713l2.693-2.617-3.724-.539a3.07 3.07 0 0 1-2.313-1.675L12 4.95zm4.5 1.36h1.781V2.477l-1.146.865-.582-.776L18.605 1h.792v5.31H21v.99h-4.5z"/></svg> } />
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
