/** global: GLSR, Image */

/**
 * The Details metabox of the review editor: the Edit Details toggle enables the fields, the avatar
 * field previews its image, and the type field shows the URL field for a remote review.
 */
export default class Metabox {
    static init (root = document) {
        root.querySelectorAll('.glsr-metabox-field input[data-edit-review]').forEach(input => input.addEventListener('change', Metabox.onToggle))
        root.querySelectorAll('.glsr-metabox-field[data-field="avatar"] input').forEach(input => input.addEventListener('change', Metabox.onChangeImage))
        root.querySelectorAll('.glsr-metabox-field[data-field="type"] select').forEach(select => select.addEventListener('change', Metabox.onChangeType))
    }

    static onChangeImage (ev) {
        const input = ev.currentTarget;
        Metabox.switchImage(input.parentElement.querySelector('img'), input.value)
    }

    static onChangeType (ev) {
        document.querySelectorAll('.glsr-metabox-field[data-field="url"]').forEach(field => {
            field.style.display = 'local' === ev.target.value ? 'none' : '';
        })
    }

    // unticking puts the values back as they were
    static onToggle (ev) {
        const editing = ev.currentTarget.checked;
        document.querySelectorAll('.glsr-input-value').forEach(field => {
            if (editing) {
                field.dataset.value = field.value;
            } else {
                field.value = field.dataset.value ?? field.value;
                if ('url' === field.type) {
                    Metabox.switchImage(field.parentElement.querySelector('img'), field.value)
                }
            }
            field.disabled = !editing;
        })
        GLSR.Rating.rebuild()
    }

    static switchImage (img, src) {
        if (!img) return;
        const image = new Image();
        image.onerror = () => (img.src = img.dataset.fallback);
        image.onload = () => (img.src = image.src);
        image.src = src;
    }
}
