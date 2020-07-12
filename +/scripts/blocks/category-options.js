const { _x } = wp.i18n;
const categories = [];
const selectPlaceholder = {
    label: '- ' + _x('Select', 'admin-text', 'site-reviews') + ' -',
    value: '',
};
const selectCustom = {
    label: '- ' + _x('Select Multiple Categories', 'admin-text', 'site-reviews') + ' -',
    value: 'glsr_custom',
};
wp.apiFetch({ path: '/site-reviews/v1/categories?per_page=50' }).then(terms => {
    categories.push(selectPlaceholder);
    categories.push(selectCustom);
    jQuery.each(terms, (key, term) => {
        categories.push({ label: term.name, value: term.id });
    });
});

export default categories;
