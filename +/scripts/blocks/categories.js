const { __ } = wp.i18n;
const categories = [];
const selectPlaceholder = {
    label: '- ' + __('Select', 'site-reviews') + ' -',
    value: '',
};
wp.apiFetch({ path: '/site-reviews/v1/categories?per_page=50' }).then(terms => {
    categories.push(selectPlaceholder);
    jQuery.each(terms, (key, term) => {
        categories.push({ label: term.name, value: term.id });
    });
});

export default categories;
