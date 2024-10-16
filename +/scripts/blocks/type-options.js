const { _x } = wp.i18n;
const types = [];
wp.apiFetch({ path: '/site-reviews/v1/types?per_page=50'}).then(reviewTypes => {
    if (reviewTypes.length < 2) return;
    types.push({
        label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -',
        value: '',
    });
    jQuery.each(reviewTypes, (key, type) => {
        types.push({ label: type.name, value: type.id });
    });
});

export default types;
