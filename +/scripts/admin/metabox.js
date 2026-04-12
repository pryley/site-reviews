/** global: GLSR, Image, jQuery */

const Metabox = function () {
    jQuery('.glsr-metabox-field input[data-edit-review]').on('change', this.onToggleInput_.bind(this));
    jQuery('.glsr-metabox-field[data-field="avatar"] input').on('change', this.onChangeImage_.bind(this));
    jQuery('.glsr-metabox-field[data-field="type"] select').on('change', this.onChangeType_.bind(this));
};

Metabox.prototype = {
    /** @return void */
    onChangeImage_: function (ev) {
        var el = jQuery(ev.currentTarget);
        this.switchImage_(el.parent().find('img'), el.val());
    },

    onChangeType_: function (ev) {
        jQuery('.glsr-metabox-field[data-field="url"]')['local' === ev.target.value ? 'hide' : 'show']();
    },

    /** @return void */
    onToggleInput_: function (ev) {
        var isChecked = ev.currentTarget.checked;
       jQuery('.glsr-input-value').each(function (i, el) {
            if (isChecked) {
               jQuery(el).data('value', el.value);
            } else {
                el.value = jQuery(el).data('value');
                if ('url' !== el.type) return;
                this.switchImage_(jQuery(el).parent().find('img'), el.value);
            }
        }.bind(this));
        jQuery('.glsr-input-value').prop('disabled', !isChecked);
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

export default Metabox;
