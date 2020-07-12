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
    jQuery.each(results, (key, user) => {
        users.push({ label: user.name + ' (' + user.slug + ')', value: user.id });
    });
});

export default users;
