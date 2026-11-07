import { config } from '@/public/core/index.js';

// the fields of the Router's forms that a route does not take
const ROUTER_FIELDS = ['_action', '_ajax_request', '_nonce', '_wp_http_referer', '_wpnonce', 'action'];

export const buttonArgs = (button) => {
    try {
        return JSON.parse(button.dataset.args || '{}');
    } catch (error) {
        return {};
    }
}

/**
 * The fields of a form as a route reads them: the site-reviews[…] prefix removed, the Router's
 * fields left out, and `values` set over them.
 */
export const formBody = (form, values = {}) => {
    const body = new FormData();
    const prefix = `${config.nameprefix}[`;
    new FormData(form).forEach((value, name) => {
        const key = name.startsWith(prefix) ? name.slice(prefix.length).replace(']', '') : name;
        if (!ROUTER_FIELDS.includes(key)) {
            body.append(key, value)
        }
    })
    Object.entries(values).forEach(([key, value]) => body.set(key, value))
    return body;
}
