/** global: jQuery */

import { fadeOut } from '@/public/core/index.js';
import Request from '@/public/modules/request/request.js';

/**
 * The notices of a plugin screen: the ones PHP printed (dismissed through their route), the
 * banner and the popup, and the ones a request answers with, printed in #glsr-notices.
 */
class Notices {
    static init (root = document) {
        root.querySelectorAll('.glsr-notice[data-notice]').forEach(notice => notice.addEventListener('click', Notices.onDismiss))
        document.addEventListener('click', Notices.onToggleDetails)
        Notices.showBanner(root)
        Notices.showPopup(root)
    }

    static onDismiss (ev) {
        const el = ev.target.closest('.notice-dismiss, .glsr-notice-buttons > *');
        if (!el) return;
        const notice = ev.currentTarget;
        Notices.remove(notice)
        const until = el.dataset.dismiss;
        Request.send({
            body: 'interval' === until ? { until } : undefined,
            method: 'interval' === until ? 'PUT' : 'DELETE',
            path: `plugin/notices/${notice.dataset.notice}`,
        })
    }

    // WordPress's bulk action notice has a details toggle
    static onToggleDetails (ev) {
        const button = ev.target.closest('.bulk-action-notice button.button-link');
        if (!button) return;
        button.classList.toggle('bulk-action-errors-collapsed')
        button.setAttribute('aria-expanded', String(!button.classList.contains('bulk-action-errors-collapsed')))
        button.closest('.bulk-action-notice').querySelector('.bulk-action-errors')?.classList.toggle('hidden')
    }

    static async remove (notice) {
        if (notice.classList.contains('glsr-notice-popup')) {
            notice.addEventListener('transitionend', () => notice.remove(), { once: true })
            window.setTimeout(() => notice.classList.add('is-closing'), 50)
            return;
        }
        await fadeOut(notice, 100)
        notice.remove()
    }

    static showBanner (root) {
        const banner = root.querySelector('.glsr-notice-banner');
        const header = document.getElementById('glsr-page-header');
        if (banner && header) {
            header.prepend(banner)
            window.setTimeout(() => Notices.slideDown(banner), 1000)
        }
    }

    static showPopup (root) {
        const popup = root.querySelector('.glsr-notice-popup');
        if (popup) {
            popup.classList.add('is-closing')
            Notices.container().prepend(popup)
            window.setTimeout(() => popup.classList.remove('is-closing'), 1000)
        }
    }

    static slideDown (el) {
        el.style.display = 'block'; // the stylesheet hides the banner
        const { height, paddingBottom, paddingTop } = getComputedStyle(el);
        el.animate?.([
            { height: 0, overflow: 'hidden', paddingBottom: 0, paddingTop: 0 },
            { height, overflow: 'hidden', paddingBottom, paddingTop },
        ], { duration: 400, easing: 'ease' })
    }

    // the request's notices are rendered here
    static container () {
        let container = document.getElementById('glsr-notices');
        if (!container) {
            document.querySelector('#message.notice')?.remove()
            container = document.createElement('div');
            container.id = 'glsr-notices';
            document.querySelector('hr.wp-header-end')?.after(container)
        }
        return container;
    }
}

const add = (notices) => {
    if (!notices) return;
    Notices.container().innerHTML = notices;
    if ('function' === typeof jQuery) {
        jQuery(document).trigger('wp-updates-notice-added') // WordPress triggers it through jQuery
    }
    window.scrollTo({ behavior: 'smooth', top: 0 })
}

const notice = (level, message) => add(`<div class="notice notice-${level} inline is-dismissible"><p>${message}</p></div>`)

const error = (message) => notice('error', message)

export { Notices }

export default { add, error, notice }
