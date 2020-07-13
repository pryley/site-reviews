/** global: GLSR, jQuery */
;(function ($) {
    'use strict';

    GLSR.ColorPicker = function () {
        if (typeof $.wp !== 'object' || typeof $.wp.wpColorPicker !== 'function') return;
        $(document).find('input[type=text].color-picker-hex').each(function () {
            $(this).wpColorPicker($(this).data('colorpicker') || {});
        });
    };
})(jQuery);
