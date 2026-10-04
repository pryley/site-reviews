/** global: FormData */

import config from '@/public/config.js';
import report from '@/public/report.js';

// the REST API itself is unavailable (a security plugin, removed routes)
const FALLBACK_CODES = ['rest_disabled', 'rest_forbidden', 'rest_no_route', 'rest_not_logged_in'];

const HEADERS = { 'X-Requested-With': 'XMLHttpRequest' };

let nonce = config.request?.nonce;
let noticeShown = false;
let unrouted = null;

class FallbackError extends Error {}

// the compat script submits a form whose action has no route
export const submitUnrouted = (fn) => {
    unrouted = fn;
}

// the answer of a Site Reviews route is final, whatever the status
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

// for a config without routes: the admin script, and a page cached before 8.4.0
const ROUTES = { 'submit-review': 'submissions' };

const submit = (formData) => {
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

// as wp.apiFetch does when a nonce has expired; a visitor whose login has ended gets none
const _refreshNonce = async () => {
    try {
        const url = new URL(config.request.ajax.url);
        url.searchParams.set('action', 'rest-nonce');
        const response = await fetch(url);
        nonce = response.ok ? await response.text() : false;
    } catch (e) {
        nonce = false;
    }
}

const _rest = async (method, path, params, body, isRetry = false) => {
    if (!config.request?.url) {
        throw new FallbackError('no REST URL');
    }
    const headers = nonce ? { ...HEADERS, 'X-WP-Nonce': nonce } : HEADERS;
    const response = await fetch(_restUrl(path, params), { body, headers, method });
    if (!(response.headers.get('content-type') || '').includes('application/json')) {
        throw new FallbackError(`unexpected response: HTTP ${response.status}`);
    }
    const json = await response.json();
    if (isInvalidNonce(json) && !isRetry) {
        await _refreshNonce()
        return _rest(method, path, params, body, true);
    }
    if (!response.ok && !isFinal(json) && ([401, 403].includes(response.status) || FALLBACK_CODES.includes(json.code))) {
        throw new FallbackError(json.code || `HTTP ${response.status}`);
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
    data.append('action', config.request.ajax.rest)
    data.append('_rest_method', method)
    data.append('_rest_path', path)
    data.append('_rest_query', _query(params).toString())
    if (nonce) {
        data.append('_rest_nonce', nonce)
    }
    try {
        const response = await fetch(config.request.ajax.url, { body: data, headers: HEADERS, method: 'POST' });
        const json = await response.json();
        if (isInvalidNonce(json) && !isRetry) {
            await _refreshNonce()
            return _tunnel(method, path, params, body, true);
        }
        return { data: json, status: response.status, success: response.ok };
    } catch (e) {
        return { data: { message: e.message }, status: 0, success: false };
    }
}

// REST first, admin-ajax on any sign that the REST API itself is unavailable
const send = async ({ body, method = 'GET', params, path }) => {
    const start = Date.now();
    let result;
    let transport = 'REST';
    try {
        result = await _rest(method, path, params, body);
    } catch (e) {
        _notice(e.message);
        transport = 'admin-ajax';
        result = await _tunnel(method, path, params, body);
    }
    report('request', { method, ms: Date.now() - start, path, status: result.status, transport })
    return result;
}

export default { pagedReviews, review, send, submit }
