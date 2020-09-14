/** global: GLSR, jQuery */

const ColorPicker = function () {
    if (typeof jQuery.wp !== 'object' || typeof jQuery.wp.wpColorPicker !== 'function') return;
    jQuery(document).find('input[type=text].color-picker-hex').each(function () {
        jQuery(this).wpColorPicker(jQuery(this).data('colorpicker') || {});
    });
};

export default ColorPicker;
