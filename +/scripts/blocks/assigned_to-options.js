const { _x } = wp.i18n;
export default [
    { label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
    { label: '- ' + _x('Select Multiple Post IDs', 'admin-text', 'site-reviews') + ' -', value: 'custom' },
    { label: _x('Assigned to the Current Page', 'admin-text', 'site-reviews'), value: 'post_id' },
    { label: _x('Assigned to the Parent Page', 'admin-text', 'site-reviews'), value: 'parent_id' },
];
