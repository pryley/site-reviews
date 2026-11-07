/** global: inlineEditTax */

/**
 * Quick edit on the categories screen: the Priority field is filled from the row.
 */
export default class QuickEdit {
    static init (root = document) {
        if ('undefined' === typeof inlineEditTax || !root.querySelector('.taxonomy-site-review-category')) return;
        const edit = inlineEditTax.edit;
        inlineEditTax.edit = function (id) {
            edit.apply(this, arguments)
            if ('object' === typeof id) {
                id = this.getId(id);
            }
            const priority = document.querySelector(`${this.what}${id} td.term_priority`)?.textContent || '';
            document.querySelectorAll('.wp-list-table [name="term_priority"]').forEach(field => (field.value = priority))
        };
    }
}
