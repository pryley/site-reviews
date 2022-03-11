const { _x } = wp.i18n;
export default [
    { label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -', value: '' },
    { label: '- ' + _x('Specific User ID', 'admin-text', 'site-reviews') + ' -', value: 'custom' },
    { label: _x('The Logged-in user', 'admin-text', 'site-reviews'), value: 'user_id' },
    { label: _x('The Page author', 'admin-text', 'site-reviews'), value: 'author_id' },
    { label: _x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews'), value: 'profile_id' },
];
