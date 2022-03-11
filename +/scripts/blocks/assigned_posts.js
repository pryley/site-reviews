const { _x } = wp.i18n;
export default [
    { label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
    { label: '- ' + _x('Specific Post ID', 'admin-text', 'site-reviews') + ' -', value: 'custom' },
    { label: _x('The Current Page', 'admin-text', 'site-reviews'), value: 'post_id' },
    { label: _x('The Parent Page', 'admin-text', 'site-reviews'), value: 'parent_id' },
];
