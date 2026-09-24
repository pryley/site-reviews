import Ajax from '@/admin/ajax.js';

class Notices {
    constructor () {
        jQuery('.glsr-notice[data-notice]').on('click.wp-dismiss-notice', this.onDismiss.bind(this))
        // Delegated: the notices added later (add) and the ones the page
        // rendered, flash or persistent, all expand the same way.
        jQuery(document).on('click', '.bulk-action-notice button.button-link', this.onToggleDetails)
        this.showBanner()
        this.showPopup()
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
    }

    dismissNotice ($el, $notice) {
        if (!$el.hasClass('notice-dismiss') && !$el.parent().hasClass('glsr-notice-buttons')) return;
        this.hideBanner($notice)
        this.removeNotice($notice)
        const ajax = new Ajax({
            _action: 'dismiss-notice',
            dismiss: $el.data('dismiss'),
            notice: $notice.data('notice'),
        });
        ajax.post()
    }

    error (message) {
        this.notice('error', message)
    }

    hideBanner ($notice) {
        if ($notice.hasClass('glsr-notice-banner')) {
            $notice.slideUp()
        }
    }

    notice (level, message) {
        this.add(`<div class="notice notice-${level} inline is-dismissible"><p>${message}</p></div>`)
    }

    onDismiss (ev) {
        const $el = jQuery(ev.target);
        const $notice = jQuery(ev.currentTarget);
        this.dismissNotice($el, $notice)
    }

    onToggleDetails (ev) {
        const $button = jQuery(ev.currentTarget)
            .toggleClass('bulk-action-errors-collapsed');
        $button.attr('aria-expanded', !$button.hasClass('bulk-action-errors-collapsed'))
        $button.closest('.bulk-action-notice').find('.bulk-action-errors').toggleClass('hidden')
    }

    removeNotice ($notice) {
        if ($notice.hasClass('glsr-notice-popup')) {
            $notice.on('transitionend', () => $notice.remove())
            setTimeout(() => $notice.addClass('is-closing'), 50)
        } else {
            $notice.fadeTo(100, 0, () => $notice.slideUp(100, () => $notice.remove()))
        }
    }

    showBanner () {
        const $el = jQuery('.glsr-notice-banner');
        if ($el.length) {
            jQuery('#glsr-page-header').prepend($el.detach())
            $el.delay(1000).slideDown()
        }
    }

    showPopup () {
        const $el = jQuery('.glsr-notice-popup');
        if ($el.length) {
            $el.addClass('is-closing')
            jQuery('#glsr-notices').prepend($el.detach())
            setTimeout(() => $el.removeClass('is-closing'), 1000)
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
