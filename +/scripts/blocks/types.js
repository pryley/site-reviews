const { __ } = wp.i18n;
const selectPlaceholder = {
    label: '- ' + __('Select', 'site-reviews') + ' -',
    value: '',
};
const types = [];
wp.apiFetch({ path: '/site-reviews/v1/types?per_page=50'}).then(reviewTypes => {
    if (reviewTypes.length < 2) return;
    types.push(selectPlaceholder);
    jQuery.each(reviewTypes, (key, type) => {
        types.push({ label: type.name, value: type.id });
    });
});

export default types;
