

const Notices = function () { // string
    this.init_();
};

Notices.prototype = {
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

    success: function (message) {
        this.add('<div class="notice notice-success inline is-dismissible"><p>' + message + '</p></div>');
    },

    error: function (message) {
        this.add('<div class="notice notice-error inline is-dismissible"><p>' + message + '</p></div>');
    },

    init_: function () {
        jQuery('.glsr-notice[data-dismiss]').on('click.wp-dismiss-notice', this.onDismiss_.bind(this))
        jQuery('.glsr-notice-footer').on('click', this.onDismiss_.bind(this));
        this.showBanner_()
    },

    data_: function (notice) {
        return {
            [GLSR.nameprefix]: { _action: 'dismiss-notice', notice },
        };
    },

    hideBanner_: function (noticeEl) {
        if (noticeEl.hasClass('glsr-notice-top-of-page')) {
            noticeEl.slideUp();
        }
    },

    showBanner_: function () {
        const el = jQuery('.glsr-notice-top-of-page');
        if (el) {
            jQuery('#glsr-page-header').prepend(el.detach());
            el.delay(1000).slideDown();
        }
    },

    onDismiss_: function (ev) {
        const noticeEl = jQuery(ev.currentTarget);
        const targetEl = jQuery(ev.target);
        this.dismissNotice_(targetEl, noticeEl)
    },

    dismissNotice_: function (targetEl, noticeEl) {
        if (!targetEl.hasClass('notice-dismiss') && !targetEl.hasClass('button')) return;
        this.hideBanner_(noticeEl)
        if (targetEl.hasClass('notice-dismiss')) {
            this.hideBanner_(noticeEl)
        }
        const data = this.data_(noticeEl.data('dismiss'));
        this.removeNotice_(noticeEl)
        wp.ajax.post(GLSR.action, data)
    },

    removeNotice_: function (noticeEl) {
        noticeEl.fadeTo(100, 0, () => noticeEl.slideUp(100, () => noticeEl.remove()))
    },
};

export default Notices;
