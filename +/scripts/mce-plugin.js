/** global: GLSR */
(tinymce => {
    'use strict';
    tinymce.PluginManager.add('glsr_shortcode', function (editor) {
        editor.addCommand('GLSR_Shortcode', () => {
            GLSR.Tinymce.create(editor.id);
        });
    });
})(window.tinymce);
