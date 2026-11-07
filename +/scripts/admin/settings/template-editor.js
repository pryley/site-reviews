/**
 * A template setting's tag buttons insert their {tag} at the cursor of its textarea.
 */
export default class TemplateEditor {
    static init (root = document) {
        root.querySelectorAll('.glsr-template-editor input').forEach(button => button.addEventListener('click', TemplateEditor.onClick))
    }

    static insert (textarea, position, text) {
        const value = textarea.value.substring(0, position) + text + textarea.value.substring(textarea.selectionEnd);
        textarea.focus()
        if (!document.execCommand('selectAll', false, null) || !document.execCommand('insertText', false, value)) {
            textarea.value = value; // without undo
        }
        textarea.selectionStart = position + text.length;
        textarea.selectionEnd = position + text.length;
        textarea.focus()
    }

    static onClick (ev) {
        ev.preventDefault()
        const textarea = ev.currentTarget.closest('.glsr-template-editor')?.querySelector('textarea');
        if (!textarea) return;
        const scrollTop = textarea.scrollTop;
        TemplateEditor.insert(textarea, textarea.selectionStart, `{${ev.currentTarget.dataset.tag}}`)
        textarea.scrollTop = scrollTop;
    }
}
