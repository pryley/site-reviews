/** global: FormData */

import { config, report } from '@/public/core/index.js';
import xhr from '@/public/modules/request/xhr.js';

// codes that say the REST API is unavailable (security plugin, login requirement, etc.)
const REST_UNAVAILABLE_CODES = ['rest_disabled', 'rest_forbidden', 'rest_not_logged_in'];

// with Accept, WordPress answers a fatal error as JSON
const HEADERS = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };

let nonce = config.request?.nonce;
let noticeShown = false;
let restUnavailable = null;

// this request goes to admin-ajax
class FallbackError extends Error {}

// this request and the rest of the page go to admin-ajax
class RestUnavailableError extends FallbackError {}

// the answer of the route is final regardless of status
const isFinal = (json) => 'string' === typeof json?.code && json.code.startsWith('glsr_');

const isInvalidNonce = (json) => 'rest_cookie_invalid_nonce' === json?.code;

// a body that is not a FormData is sent as JSON
const isJsonBody = (body) => !!body && !(body instanceof FormData);

// a 4xx is the server saying no, any other status is a fault
const log = (status, details) => console[400 <= status && 500 > status ? 'warn' : 'error'](details);

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

const submit = (formData) => {
    if (!config.request?.ajax?.url) {
        return Promise.resolve(_unconfigured());
    }
    const action = formData.get(`${config.nameprefix}[_action]`) || 'submit-review';
    const route = _route(action, _formValues(formData));
    if (route) {
        return send({ body: formData, ...route });
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

// the form's site-reviews[key] fields by key, which fill the {placeholders} of its route's path
const _formValues = (formData) => {
    const prefix = `${config.nameprefix}[`;
    const values = {};
    for (const [name, value] of formData.entries()) {
        if (name.startsWith(prefix) && name.endsWith(']')) {
            values[name.slice(prefix.length, -1)] = value;
        }
    }
    return values;
};

// the request to send: a route of the config by its name, or the request's own method and path
const _request = ({ body, method = 'GET', params, path, route, signal, ...values }) => {
    const named = route ? _route(route, values) : { method, path };
    if (!named) {
        console.error(`Site Reviews: the config names no route "${route}".`)
        return null;
    }
    return { body, params, signal, ...named };
}

// a route of the config: a path (a POST to it) or { method, path }, with the {placeholders} of the path filled
const _route = (name, values = {}) => {
    const route = config.request?.routes?.[name];
    if (!route) {
        return null;
    }
    const { method = 'POST', path } = 'string' === typeof route ? { path: route } : route;
    return { method, path: path.replace(/\{(\w+)\}/g, (match, key) => key in values ? encodeURIComponent(values[key]) : match) };
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

const _rest = async (request, isRetry = false) => {
    const { body, method, params, path, signal } = request;
    if (restUnavailable) {
        throw restUnavailable;
    }
    if (!config.request?.url) {
        throw new RestUnavailableError('no REST URL');
    }
    const headers = { ...HEADERS };
    if (nonce) {
        headers['X-WP-Nonce'] = nonce;
    }
    if (isJsonBody(body)) {
        headers['Content-Type'] = 'application/json';
    }
    if (!['GET', 'POST'].includes(method)) {
        headers['X-HTTP-Method-Override'] = method; // WordPress reads a body and $_FILES as a POST only
    }
    const response = await fetch(_restUrl(path, params), {
        body: isJsonBody(body) ? JSON.stringify(body) : body,
        headers,
        method: 'GET' === method ? 'GET' : 'POST',
        signal,
    });
    if ('function' !== typeof response?.json) {
        throw new RestUnavailableError('window.fetch has been replaced by another script and returned no Response');
    }
    if (!(response.headers.get('content-type') || '').includes('application/json')) {
        if (500 <= response.status) {
            return { data: { message: `HTTP ${response.status}` }, status: response.status, success: false }; // the route failed: not run a second time over admin-ajax
        }
        throw new FallbackError(`unexpected response: HTTP ${response.status}`);
    }
    const json = await response.json();
    if (isInvalidNonce(json) && !isRetry) {
        await _refreshNonce()
        return _rest(request, true); // once more, with the fresh nonce
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

// a path that starts with / is read from the REST root, which the config's URL ends the namespace of
const _restPath = (base, path) => {
    if (path.startsWith('/')) {
        base = base.replace(new RegExp(`${config.nameprefix || 'site-reviews'}/v1/?$`), '');
    }
    return base.replace(/\/$/, '') + '/' + path.replace(/^\//, '');
}

const _restUrl = (path, params) => {
    const url = new URL(config.request.url);
    if (url.searchParams.has('rest_route')) { // plain permalinks
        url.searchParams.set('rest_route', _restPath(url.searchParams.get('rest_route'), path));
    } else {
        url.pathname = _restPath(url.pathname, path);
    }
    if (config.request.language) {
        url.searchParams.set('lang', config.request.language) // Polylang and WPML read lang
    }
    _query(params, url.searchParams)
    return url;
}

// the same request over admin-ajax (RestController::ajaxResponse)
const _tunnel = async (request, isRetry = false) => {
    const { body, method, params, path, signal } = request;
    const data = new FormData();
    if (body instanceof FormData) {
        body.forEach((value, key) => data.append(key, value))
    } else if (isJsonBody(body)) {
        data.append('_rest_body', JSON.stringify(body))
    }
    data.append('action', config.request.ajax.rest)
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
        const { ok, status, text } = await xhr(config.request.ajax.url, { body: data, signal });
        const json = JSON.parse(text);
        if (isInvalidNonce(json) && !isRetry) {
            await _refreshNonce()
            return _tunnel(request, true); // once more, with the fresh nonce
        }
        return { data: json, status, success: ok };
    } catch (e) {
        return { data: { message: e.message }, status: 0, success: false };
    }
}

// REST first, admin-ajax on any sign that the REST API itself is unavailable
const send = async (options) => {
    if (!config.request?.ajax?.url) {
        return _unconfigured();
    }
    const request = _request(options);
    if (!request) {
        return { data: { message: `No route is named "${options.route}".` }, status: 0, success: false };
    }
    const { method, path } = request;
    const start = Date.now();
    let result;
    let transport = 'REST';
    try {
        result = await _rest(request);
    } catch (e) {
        if ('AbortError' === e.name) {
            return { data: { message: e.message }, status: 0, success: false }; // the page gave the request up
        }
        _notice(e.message);
        if (e instanceof RestUnavailableError) {
            restUnavailable = e;
        }
        transport = 'admin-ajax';
        result = await _tunnel(request);
    }
    report('request', { method, ms: Date.now() - start, path, status: result.status, transport })
    return result;
}

export default { log, pagedReviews, review, send, submit }
