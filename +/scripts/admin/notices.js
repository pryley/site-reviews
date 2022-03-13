

const Notices = function () { // string
    this.init_();
};

Notices.prototype = {
    /** @return void */
    add: function (notices) { // string
        if (!notices) return;
        if (!jQuery('#glsr-notices').length) {
            jQuery('#message.notice').remove();
            jQuery('hr.wp-header-end').after('<div id="glsr-notices" />');
        }
        jQuery('#glsr-notices').html(notices);
        jQuery(document).trigger('wp-updates-notice-added');
        jQuery('html').animate({ scrollTop: 0 }, 500);
    },

    error: function (message) {
        this.add('<div class="notice notice-error inline is-dismissible"><p>' + message + '</p></div>');
    },

    /** @return void */
    init_: function () {
        jQuery('.glsr-notice[data-dismiss]').on('click.wp-dismiss-notice', this.onClick_.bind(this));
    },

    /** @return void */
    onClick_: function (ev) {
        var data = {};
        data[GLSR.nameprefix] = {
            _action: 'dismiss-notice',
            notice: jQuery(ev.currentTarget).data('dismiss'),
        };
        wp.ajax.post(GLSR.action, data);
    },
};

export default Notices;
