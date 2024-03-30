/** global: GLSR, jQuery */

import Ajax from '@/admin/ajax.js';

const Tools = function () {
    jQuery('form').on('click', '[data-ajax-click]', this.onAjaxClick_.bind(this))
    jQuery('.glsr-button').on('click', this.onExpand_)
    jQuery('form.wp-upload-form').each((index, formEl) => {
        jQuery(formEl).find('input[type="file"]').on('change', () => this.toggleUploadButton_(formEl))
        this.toggleUploadButton_(formEl)
    })
    jQuery('#proxy_http_header').on('change', function (ev) {
        const val = jQuery(this).val();
        jQuery('#trusted_proxies').closest('p')['' === val ? 'addClass' : 'removeClass']('hidden')
    })
};

Tools.prototype = {
    onAjaxClick_: function (ev) {
        var el = jQuery(ev.currentTarget);
        var form = el.closest('form');
        var self = this;
        form.find('input[data-alt]').val(void(0) === el.data('alt') ? 0 : 1);
        (new Ajax({}, ev, form)).post(function (response, success) {
            if (void(0) !== el.data('console') && success) {
                jQuery('#glsr-log-file').val(response.console);
            }
            if (void(0) !== el.data('ajax-scroll')) {
                jQuery('html, body').animate({ scrollTop: 0 }, 500);
            }
            el.closest('[data-ajax-hide]')
                .css({ backgroundColor: 'rgba(74,184,102,.25)' })
                .fadeOut('normal', function() {
                    jQuery(this).remove();
                });
            jQuery('.glsr-notice[data-notice="' + el.data('remove-notice') + '"]').remove();
            jQuery('.glsr-notice a').on('click', self.onExpand_);
        });
    },
    onExpand_: function (ev) {
        var el = jQuery(ev.currentTarget);
        if (el.data('expand')) {
            localStorage.setItem('glsr-expand', el.data('expand'));
        }
    },
    toggleUploadButton_: function (formEl) {
        const input = jQuery(formEl).find('input[type="file"]');
        const submit = jQuery(formEl).find('[type="submit"]');
        submit.prop('disabled', '' === input.map(function () {
            return jQuery(this).val();
        }).get().join(''));
    },
};

export default Tools;

