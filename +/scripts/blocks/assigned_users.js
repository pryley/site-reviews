const { _x } = wp.i18n;
const users = [];
const selectPlaceholder = {
    label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -',
    value: '',
};
const selectCustom = {
    label: '- ' + _x('Select Multiple Users', 'admin-text', 'site-reviews') + ' -',
    value: 'glsr_custom',
};
wp.apiFetch({ path: '/wp/v2/users?per_page=50' }).then(results => {
    users.push(selectPlaceholder);
    users.push(selectCustom);
    users.push({
        label: _x('The Logged-in user', 'admin-text', 'site-reviews') + ' (user_id)',
        value: 'user_id',
    });
    users.push({
        label: _x('The Page author', 'admin-text', 'site-reviews') + ' (author_id)',
        value: 'author_id',
    });
    users.push({
        label: _x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews') + ' (profile_id)',
        value: 'profile_id',
    });
    jQuery.each(results, (key, user) => {
        users.push({ label: user.name + ' (' + user.slug + ')', value: user.id });
    });
});

export default users;
