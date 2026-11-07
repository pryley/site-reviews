/**
 * The reset button beside a textarea setting puts its default back.
 */
export default class ResetButton {
    static init (root = document) {
        root.querySelectorAll('.glsr-setting-field button.is-reset-button').forEach(button => button.addEventListener('click', ResetButton.onClick))
    }

    static onClick (ev) {
        const button = ev.currentTarget;
        const textarea = button.previousElementSibling;
        if (textarea?.matches('textarea.autosized')) {
            textarea.value = button.dataset.default;
        }
    }
}
