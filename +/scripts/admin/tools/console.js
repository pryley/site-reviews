/**
 * The Console tab: the Reload and Clear buttons' answers fill the log textarea.
 */
export default class Console {
    static init (root = document) {
        const textarea = root.querySelector('#glsr-log-file');
        if (!textarea) return;
        root.querySelectorAll('[data-console]').forEach(button => button.addEventListener('site-reviews/tool', ({ detail }) => {
            if (detail.success && 'console' in detail.data) {
                textarea.value = detail.data.console;
            }
        }))
    }
}
