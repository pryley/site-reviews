import Ajax from '@/admin/ajax.js';

class Notices {
    constructor () {
        jQuery('.glsr-notice[data-dismiss]').on('click.wp-dismiss-notice', this.onDismiss.bind(this))
        jQuery('.glsr-notice-footer').on('click', this.onDismiss.bind(this))
        this.showBanner()
    }

    add (notices) {
        if (!notices) return;
        if (!jQuery('#glsr-notices').length) {
            jQuery('#message.notice').remove()
            jQuery('hr.wp-header-end').after('<div id="glsr-notices" />')
        }
        jQuery('#glsr-notices').html(notices)
        jQuery(document).trigger('wp-updates-notice-added')
        jQuery('html').animate({ scrollTop: 0 }, 500)
        this.initBulkActions();
    }

    dismissNotice ($el, $notice) {
        if (!$el.hasClass('notice-dismiss') && !$el.hasClass('button')) return;
        this.hideBanner($notice)
        this.removeNotice($notice)
        const ajax = new Ajax({
            _action: 'dismiss-notice',
            notice: $notice.data('dismiss'),
        });
        ajax.post()
    }

    error (message) {
        this.notice('error', message)
    }

    hideBanner ($notice) {
        if ($notice.hasClass('glsr-notice-top-of-page')) {
            $notice.slideUp()
        }
    }

    initBulkActions () {
        const $notice = jQuery('#glsr-notices .bulk-action-notice').on('click', 'button.button-link', function () {
            jQuery(this)
                .toggleClass('bulk-action-errors-collapsed')
                .attr('aria-expanded', !jQuery(this).hasClass('bulk-action-errors-collapsed'))
            $notice.find('.bulk-action-errors').toggleClass('hidden')
        });
    }

    notice (level, message) {
        this.add(`<div class="notice notice-${level} inline is-dismissible"><p>${message}</p></div>`)
    }

    onDismiss (ev) {
        const $el = jQuery(ev.target);
        const $notice = jQuery(ev.currentTarget);
        this.dismissNotice($el, $notice)
    }

    removeNotice ($notice) {
        $notice.fadeTo(100, 0, () => $notice.slideUp(100, () => $notice.remove()))
    }

    showBanner () {
        const $el = jQuery('.glsr-notice-top-of-page');
        if ($el.length) {
            jQuery('#glsr-page-header').prepend($el.detach())
            $el.delay(1000).slideDown()
        }
    }

    success (message) {
        this.notice('success', message)
    }

    warning (message) {
        this.notice('warning', message)
    }
}

export default Notices;
