import { config, fadeOut } from '@/public/core/index.js';
import BusyButton from '@/admin/shared/busy-button.js';
import { buttonArgs, formBody } from '@/admin/shared/request.js';
import Notice from '@/admin/shared/notice.js';
import Request from '@/public/modules/request/request.js';

/**
 * A tool's button: it sends its form to the route the button names (data-route, with data-args
 * set over the fields), shows the notices of the answer, and dispatches `site-reviews/tool` with
 * the answer for the tool's module.
 */
export default class ToolButton {
    static init (root = document) {
        root.querySelectorAll('form [data-ajax-click][data-route]').forEach(button => button.addEventListener('click', ToolButton.onClick))
    }

    static async onClick (ev) {
        ev.preventDefault()
        const button = ev.currentTarget;
        const form = button.closest('form');
        const route = button.dataset.route;
        const state = BusyButton(button);
        state.loading()
        const fields = formBody(form, buttonArgs(button));
        const values = Object.fromEntries(fields);
        const isRead = 'GET' === (config.request.routes?.[route]?.method || 'POST');
        const { data, success } = await Request.send({
            ...values, // fills the {placeholders} of the route's path
            [isRead ? 'params' : 'body']: isRead ? values : fields,
            route,
        });
        if (data?.notices) {
            Notice.add(data.notices)
        } else if (!success) {
            Notice.error(data?.message || 'Unknown error.')
        }
        state.loaded()
        button.dispatchEvent(new CustomEvent('site-reviews/tool', { bubbles: true, detail: { data, success } }))
        if (!success) return;
        if ('ajaxScroll' in button.dataset) {
            window.scrollTo({ behavior: 'smooth', top: 0 })
        }
        const hidden = button.closest('[data-ajax-hide]');
        if (hidden) {
            hidden.style.backgroundColor = 'rgba(74,184,102,.25)';
            await fadeOut(hidden, 400)
            hidden.remove()
        }
        if (button.dataset.removeNotice) {
            document.querySelectorAll(`.glsr-notice[data-notice="${button.dataset.removeNotice}"]`).forEach(notice => notice.remove())
        }
    }
}
