/** global: GLSR */
(tinymce => {
    'use strict';
    tinymce.PluginManager.add('glsr_shortcode', editor => {
        editor.addCommand('GLSR_Shortcode', () => {
            GLSR.shortcode.create(editor.id);
        });
    });
})(window.tinymce);
