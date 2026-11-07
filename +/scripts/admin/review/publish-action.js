/**
 * The Pinned and Verified controls of the publish box: Edit shows the select, OK writes the choice
 * into the text and the hidden field the form posts, Cancel puts the select back.
 */
export default class PublishAction {
    static init (root = document) {
        root.querySelectorAll('.misc-pub-section[data-action]').forEach(section => new PublishAction(section))
    }

    constructor (root) {
        this.root = root;
        this.edit = root.querySelector('a[data-click="edit"]');
        this.section = root.querySelector('.misc-pub-select');
        this.select = this.section?.querySelector('select');
        this.value = this.section?.querySelector('input[type="hidden"]');
        if (!this.edit || !this.section || !this.select || !this.value) return;
        root.querySelectorAll('a[data-click]').forEach(link => link.addEventListener('click', this.onClick.bind(this)))
    }

    onCancel () {
        this.select.value = this.value.value;
        this.section.style.display = 'none';
        this.edit.style.display = '';
        this.edit.focus()
    }

    onClick (ev) {
        ev.preventDefault()
        const action = ev.currentTarget.dataset.click;
        const method = `on${action.charAt(0).toUpperCase()}${action.slice(1)}`;
        if ('function' === typeof this[method]) {
            this[method]()
        }
    }

    onEdit () {
        if (this.section.offsetParent) return; // visible
        this.section.style.display = 'block';
        this.edit.style.display = 'none';
        this.select.focus()
    }

    onSave () {
        const option = this.select.selectedOptions[0];
        this.value.value = option.value;
        const text = this.root.querySelector('.misc-pub-text');
        if (text) {
            text.textContent = option.dataset.alt || option.textContent.trim();
        }
        this.section.style.display = 'none';
        this.edit.style.display = '';
        this.edit.focus()
    }
}
