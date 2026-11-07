/**
 * An upload form: its submit button waits for a file to be chosen.
 */
export default class UploadForm {
    static init (root = document) {
        root.querySelectorAll('form.wp-upload-form').forEach(form => new UploadForm(form))
    }

    constructor (form) {
        this.form = form;
        form.querySelectorAll('input[type="file"]').forEach(input => input.addEventListener('change', this.update.bind(this)))
        this.update()
    }

    update () {
        const empty = [...this.form.querySelectorAll('input[type="file"]')].every(input => '' === input.value);
        this.form.querySelectorAll('[type="submit"]').forEach(button => (button.disabled = empty))
    }
}
