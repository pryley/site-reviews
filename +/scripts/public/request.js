/** global: FormData */

import config from '@/public/config.js';
import report from '@/public/report.js';
import xhr from '@/public/xhr.js';

// codes that say the REST API is unavailable (security plugin, login requirement, etc.)
const REST_UNAVAILABLE_CODES = ['rest_disabled', 'rest_forbidden', 'rest_not_logged_in'];

const HEADERS = { 'X-Requested-With': 'XMLHttpRequest' };

let nonce = config.request?.nonce;
let noticeShown = false;
let restUnavailable = null;
let unrouted = null;

// this request goes to admin-ajax
class FallbackError extends Error {}

// this request and the rest of the page go to admin-ajax
class RestUnavailableError extends FallbackError {}

// the compat script submits a form whose action has no route
export const submitUnrouted = (fn) => {
    unrouted = fn;
}

// the answer of the route is final regardless of status
const isFinal = (json) => 'string' === typeof json?.code && json.code.startsWith('glsr_');

const isInvalidNonce = (json) => 'rest_cookie_invalid_nonce' === json?.code;

const pagedReviews = (values) => send({
    method: 'GET',
    params: { atts: values.atts, page: values.page, schema: values.schema, url: values.url },
    path: 'render/reviews',
})

const review = (reviewId, values = {}) => send({
    method: 'GET',
    params: values,
    path: `render/reviews/${reviewId}`,
})

// for config without routes: admin script or page was cached before 8.4.0
const ROUTES = { 'submit-review': 'submissions' };

// for config without rest action: page was cached before 8.4.0
const REST_ACTION = 'glsr_rest_request';

const submit = (formData) => {
    if (!config.request?.ajax?.url) {
        return Promise.resolve(_unconfigured());
    }
    const action = formData.get(`${config.nameprefix}[_action]`) || 'submit-review';
    const path = (config.request?.routes || ROUTES)[action];
    if (path) {
        return send({ body: formData, method: 'POST', path });
    }
    if (unrouted) {
        return unrouted(formData, action);
    }
    console.error(`Site Reviews: the form action "${action}" has no REST route.`)
    return Promise.resolve({ data: { message: `No route is registered for the form action "${action}".` }, status: 0, success: false });
}

const _notice = (reason) => {
    if (!noticeShown) {
        console.info(`Site Reviews is using the admin-ajax fallback (${reason}).`);
        noticeShown = true;
    }
}

const _unconfigured = () => {
    console.error('Site Reviews: the request config is missing.')
    return { data: { message: 'The request was not sent: the Site Reviews config is missing.' }, status: 0, success: false };
}

const _query = (params, search = new URLSearchParams()) => {
    Object.entries(params || {}).forEach(([key, value]) => {
        if (undefined === value || null === value) return;
        if ('[object Object]' === Object.prototype.toString.call(value)) {
            Object.entries(value).forEach(([k, v]) => search.append(`${key}[${k}]`, v));
        } else {
            search.append(key, value);
        }
    });
    return search;
}

// as wp.apiFetch does when a nonce has expired (logged in users only)
const _refreshNonce = async () => {
    try {
        const url = new URL(config.request.ajax.url);
        url.searchParams.set('action', 'rest-nonce');
        const { ok, text } = await xhr(url, { method: 'GET' });
        nonce = ok ? text : false;
    } catch (e) {
        nonce = false;
    }
}

const _rest = async (method, path, params, body, isRetry = false) => {
    if (restUnavailable) {
        throw restUnavailable;
    }
    if (!config.request?.url) {
        throw new RestUnavailableError('no REST URL');
    }
    const headers = nonce ? { ...HEADERS, 'X-WP-Nonce': nonce } : HEADERS;
    const response = await fetch(_restUrl(path, params), { body, headers, method });
    if ('function' !== typeof response?.json) {
        throw new RestUnavailableError('window.fetch has been replaced by another script and returned no Response');
    }
    if (!(response.headers.get('content-type') || '').includes('application/json')) {
        throw new FallbackError(`unexpected response: HTTP ${response.status}`);
    }
    const json = await response.json();
    if (isInvalidNonce(json) && !isRetry) {
        await _refreshNonce()
        return _rest(method, path, params, body, true); // once more, with the fresh nonce
    }
    if (!response.ok && !isFinal(json)) {
        if ('rest_no_route' === json.code) {
            throw new FallbackError(json.code); // a removed route says nothing about the others
        }
        if ([401, 403].includes(response.status) || REST_UNAVAILABLE_CODES.includes(json.code)) {
            throw new RestUnavailableError(json.code || `HTTP ${response.status}`);
        }
    }
    return { data: json, status: response.status, success: response.ok }; // a 4xx here is the final answer of a route
}

const _restUrl = (path, params) => {
    const url = new URL(config.request.url);
    if (url.searchParams.has('rest_route')) { // plain permalinks
        url.searchParams.set('rest_route', url.searchParams.get('rest_route').replace(/\/$/, '') + '/' + path);
    } else {
        url.pathname = url.pathname.replace(/\/$/, '') + '/' + path;
    }
    if (config.request.language) {
        url.searchParams.set('lang', config.request.language) // Polylang and WPML read lang
    }
    _query(params, url.searchParams)
    return url;
}

// the same request over admin-ajax (RestController::ajaxResponse)
const _tunnel = async (method, path, params, body, isRetry = false) => {
    const data = new FormData();
    if (body instanceof FormData) {
        body.forEach((value, key) => data.append(key, value))
    } else {
        Object.entries(body || {}).forEach(([key, value]) => data.append(key, value))
    }
    data.append('action', config.request.ajax.rest || REST_ACTION)
    data.append('_rest_method', method)
    data.append('_rest_path', path)
    data.append('_rest_query', _query(params).toString())
    if (config.request.language) {
        // Polylang and WPML read lang here, not in _rest_query
        data.append('lang', config.request.language)
    }
    if (nonce) {
        data.append('_rest_nonce', nonce)
    }
    try {
        const { ok, status, text } = await xhr(config.request.ajax.url, { body: data });
        const json = JSON.parse(text);
        if (isInvalidNonce(json) && !isRetry) {
            await _refreshNonce()
            return _tunnel(method, path, params, body, true); // once more, with the fresh nonce
        }
        return { data: json, status, success: ok };
    } catch (e) {
        return { data: { message: e.message }, status: 0, success: false };
    }
}

// REST first, admin-ajax on any sign that the REST API itself is unavailable
const send = async ({ body, method = 'GET', params, path }) => {
    if (!config.request?.ajax?.url) {
        return _unconfigured();
    }
    const start = Date.now();
    let result;
    let transport = 'REST';
    try {
        result = await _rest(method, path, params, body);
    } catch (e) {
        _notice(e.message);
        if (e instanceof RestUnavailableError) {
            restUnavailable = e;
        }
        transport = 'admin-ajax';
        result = await _tunnel(method, path, params, body);
    }
    report('request', { method, ms: Date.now() - start, path, status: result.status, transport })
    return result;
}

export default { pagedReviews, review, send, submit }
