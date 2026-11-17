/**
 * The controls of a textarea setting: the tag select inserts the chosen {tag} at the cursor
 * and the reset button puts the default back.
 */
export default class TextareaEditor {
    static init (root = document) {
        root.querySelectorAll('.glsr-textarea-editor select').forEach(select => select.addEventListener('change', TextareaEditor.onChange))
        root.querySelectorAll('.glsr-textarea-editor button.is-reset-button').forEach(button => button.addEventListener('click', TextareaEditor.onClick))
    }

    static onChange (ev) {
        const select = ev.currentTarget;
        const tag = select.value;
        const textarea = TextareaEditor.textarea(select);
        select.value = ''; // show "Insert a tag" again
        if (tag && textarea) {
            const { scrollTop, selectionEnd, selectionStart } = textarea;
            TextareaEditor.write(textarea, `{${tag}}`, selectionStart, selectionEnd)
            textarea.scrollTop = scrollTop;
        }
    }

    static onClick (ev) {
        const button = ev.currentTarget;
        const textarea = TextareaEditor.textarea(button);
        if (textarea && textarea.value !== button.dataset.default) {
            TextareaEditor.write(textarea, button.dataset.default)
        }
    }

    static textarea (control) {
        return control.closest('.glsr-textarea-editor')?.querySelector('textarea');
    }

    /**
     * Replace the text between two positions of the textarea and leave the focus in it.
     */
    static write (textarea, text, start = 0, end = textarea.value.length) {
        textarea.focus() // execCommand writes to the focused field
        textarea.setSelectionRange(start, end)
        if (!document.execCommand('insertText', false, text)) {
            textarea.setRangeText(text, start, end, 'end') // without undo
        }
    }
}
