/** global: GLSR, jQuery */

import Ajax from './ajax.js';

const Tools = function () {
    jQuery('form').on('click', '#clear-console', this.loadConsole_, this.onClick_.bind(this));
    jQuery('form').on('click', '#fetch-console', this.loadConsole_, this.onClick_.bind(this));
    jQuery('form').on('click', '[data-ajax-click]', this.onClick_.bind(this));
    var alt = jQuery('input[data-alt]');
    if (alt.length) {
        jQuery(document).on('keydown', this.onKeyDown_.bind(this, alt));
        jQuery(document).on('keyup', this.onKeyUp_.bind(this, alt));
    }
};

Tools.prototype = {
    loadConsole_: function (response, success) {
        if (success) {
            jQuery('#log-file').val(response.console);
        }
    },
    onClick_: function (ev) {
        (new Ajax({}, ev, ev.currentTarget.closest('form'))).post(function (response, success) {
            if (typeof ev.data === 'function') {
                ev.data(response, success);
            }
            jQuery('html, body').animate({ scrollTop: 0 }, 500);
            jQuery('#glsr-notices').on('click', 'a', function () {
                localStorage.setItem('glsr-expand', jQuery(this).data('expand'));
            });
            jQuery('.glsr-notice[data-notice="' + jQuery(ev.currentTarget).data('remove-notice') + '"]').remove();
        });
    },
    onKeyDown_: function (alt, ev) {
        if (GLSR.keys.ALT !== ev.keyCode || ev.repeat) return;
        alt.closest('form').find('[data-alt-text]').addClass('alt');
        alt.val(1);
    },
    onKeyUp_: function (alt, ev) {
        if (GLSR.keys.ALT !== ev.keyCode) return;
        alt.closest('form').find('[data-alt-text]').removeClass('alt');
        alt.val(0);
    },
};

export default Tools;
