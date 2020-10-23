/** global: GLSR, jQuery */

const TextareaResize = function () {
    var textarea = document.querySelector('#contentdiv > textarea');
    if (!textarea) return;
    this.resize_(textarea);
    jQuery(document).on('wp-window-resized.editor-expand', function () {
        this.resize_(textarea);
    }.bind(this));
};

TextareaResize.prototype = {
    /** @return void */
    resize_: function (textareaEl) { // HTMLElement
        var minHeight = 320;
        var height = textareaEl.scrollHeight > minHeight ? textareaEl.scrollHeight : minHeight;
        textareaEl.style.height = 'auto';
        textareaEl.style.height = height + 'px';
    },
};

export default TextareaResize;
