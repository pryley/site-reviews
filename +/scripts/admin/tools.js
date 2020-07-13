/** global: GLSR, jQuery */
;(function ($) {
    'use strict';

    GLSR.Tools = function () {
        $('form').on('click', '#clear-console', this.loadConsole_, this.onClick_.bind(this));
        $('form').on('click', '#fetch-console', this.loadConsole_, this.onClick_.bind(this));
        $('form').on('click', '[data-ajax-click]', this.onClick_.bind(this));
        var alt = $('input[data-alt]');
        if (alt.length) {
            $(document).on('keydown', this.onKeyDown_.bind(this, alt));
            $(document).on('keyup', this.onKeyUp_.bind(this, alt));
        }
    };

    GLSR.Tools.prototype = {
        loadConsole_: function (response, success) {
            if (success) {
                $('#log-file').val(response.console);
            }
        },
        onClick_: function (ev) {
            (new GLSR.Ajax({}, ev, ev.currentTarget.closest('form'))).post(function (response, success) {
                if (typeof ev.data === 'function') {
                    ev.data(response, success);
                }
                $('html, body').animate({ scrollTop: 0 }, 500);
                $('#glsr-notices').on('click', 'a', function () {
                    localStorage.setItem('glsr-expand', $(this).data('expand'));
                });
                $('.glsr-notice[data-notice="' + $(ev.currentTarget).data('remove-notice') + '"]').remove();
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
})(jQuery);
