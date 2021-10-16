/** global: GLSR, jQuery */

const ColorPicker = function () {
    if (typeof jQuery.wp !== 'object' || typeof jQuery.wp.wpColorPicker !== 'function') return;
    jQuery(document).find('input[type=text].color-picker-hex').each(function () {
        const el = jQuery(this);
        const options = jQuery.extend({}, el.data('colorpicker') || {}, {
            change: (ev, ui) => {
                jQuery(ev.target).val(ui.color.toString()).trigger('change:setting:colorpicker')
            },
            mode: 'hsl',
            palettes: false,
            width: 180,
        })
        el.wpColorPicker(options);
    });
};

export default ColorPicker;
