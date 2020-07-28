/** global: GLSR, Image, jQuery */
;(function ($) {
    'use strict';

    GLSR.Metabox = function () {
        $('.glsr-metabox-field .glsr-toggle__input').on('change', this.onToggleInput_.bind(this));
        $('.glsr-metabox-field input[type=url]').on('change', this.onChangeImage_.bind(this));
    };

    GLSR.Metabox.prototype = {

        /** @return void */
        onChangeImage_: function (ev) {
            var el = $(ev.currentTarget);
            this.switchImage_(el.parent().find('img'), el.val());
        },

        /** @return void */
        onToggleInput_: function (ev) {
            var isChecked = ev.currentTarget.checked;
            $('.glsr-input-value').each(function (i, el) {
                if (isChecked) {
                    $(el).data('value', el.value);
                } else {
                    el.value = $(el).data('value');
                    if ('url' !== el.type) return;
                    this.switchImage_($(el).parent().find('img'), el.value);
                }
            }.bind(this));
            $('.glsr-input-value').prop('disabled', !isChecked);
            GLSR.stars.rebuild();
        },

        /** @return void */
        switchImage_: function (imgEl, imgSrc) {
            if (!imgEl) return;
            var image = new Image();
            image.src = imgSrc;
            image.onerror = function () {
                imgEl.attr('src', imgEl.data('fallback'));
            };
            image.onload = function () {
                imgEl.attr('src', image.src);
            };
        },
    };
})(jQuery);
