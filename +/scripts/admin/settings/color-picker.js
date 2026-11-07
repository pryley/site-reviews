/** global: jQuery */

/**
 * A colour setting, on WordPress's wpColorPicker, which changes the field as the colour is picked.
 */
export default class ColorPicker {
    static init (root = document) {
        if ('object' !== typeof jQuery?.wp || 'function' !== typeof jQuery.wp.wpColorPicker) return;
        root.querySelectorAll('.glsr-setting-field input[type=text].color-picker-hex').forEach(input => {
            let options = {};
            try {
                options = JSON.parse(input.dataset.colorpicker || '{}');
            } catch (ignored) {
                // no options
            }
            jQuery(input).wpColorPicker({
                ...options,
                change: (ev, ui) => {
                    ev.target.value = ui.color.toString();
                    ev.target.dispatchEvent(new Event('change', { bubbles: true }))
                },
                mode: 'hsl',
                palettes: false,
                width: 180,
            })
        })
    }
}
