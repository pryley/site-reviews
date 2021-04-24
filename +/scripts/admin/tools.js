/** global: GLSR, jQuery */

import Ajax from './ajax.js';

const Tools = function () {
    jQuery('form').on('click', '#clear-console', this.loadConsole_, this.onClick_.bind(this));
    jQuery('form').on('click', '#fetch-console', this.loadConsole_, this.onClick_.bind(this));
    jQuery('form').on('click', '[data-ajax-click]', this.onClick_.bind(this));
    jQuery('.glsr-button').on('click', this.setExpand_);
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
        var el = jQuery(ev.currentTarget);
        el.addClass('is-busy');
        (new Ajax({}, ev, el.closest('form'))).post(function (response, success) {
            if (typeof ev.data === 'function') {
                ev.data(response, success);
            }
            if (el.get(0).hasAttribute('data-ajax-scroll')) {
                jQuery('html, body').animate({ scrollTop: 0 }, 500);
            }
            el.removeClass('is-busy');
            el.closest('[data-ajax-hide]')
                .css({ backgroundColor: 'rgba(74,184,102,.25)' })
                .fadeOut('normal', function() {
                    jQuery(this).remove();
                });
            jQuery('#glsr-notices').on('click', 'a', function () {
                localStorage.setItem('glsr-expand', el.data('expand'));
            });
            jQuery('.glsr-notice[data-notice="' + el.data('remove-notice') + '"]').remove();
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
    setExpand_: function (ev) {
        var expand = jQuery(ev.currentTarget).data('expand');
        if (expand) {
            localStorage.setItem('glsr-expand', expand);
        }
    },
};

export default Tools;
