/** global: inlineEditPost, jQuery */

/**
 * Quick edit on the review list: the content and the response come from the row, and Enter works in
 * the response textarea.
 */
export default class QuickEdit {
    static init (root = document) {
        const list = root.querySelector('.post-type-site-review #the-list');
        if (!list || 'undefined' === typeof inlineEditPost) return;
        list.addEventListener('click', QuickEdit.onEdit)
        window.setTimeout(() => { // WordPress binds its keydown after the page is ready
            jQuery('td', '.post-type-site-review #inline-edit').off('keydown') // allow Enter in the response textarea
        }, 50)
    }

    static onEdit (ev) {
        const button = ev.target.closest('.editinline');
        if (!button) return;
        const row = button.closest('tr');
        const fill = (selector, value) => document.querySelectorAll(selector).forEach(field => (field.value = value));
        fill('[data-name="post_content"]', '')
        fill('[name="_response"]', '')
        window.setTimeout(() => { // after WordPress has rendered the editor
            fill('[data-name="post_content"]', row.querySelector('._post_content')?.textContent || '')
            fill('[name="_response"]', row.querySelector('._response')?.textContent || '')
        }, 50)
    }
}
