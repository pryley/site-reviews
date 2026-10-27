/** global: FormData, GLSR */

import xhr from '@/public/xhr.js';

// codes that send only this request to admin-ajax (a stale nonce, a removed route)
const FALLBACK_CODES = ['rest_cookie_invalid_nonce', 'rest_no_route'];

// codes that say the REST API is unavailable (security plugin, login requirement, etc.)
const REST_UNAVAILABLE_CODES = ['rest_disabled', 'rest_forbidden', 'rest_not_logged_in'];

let noticeShown = false;
let restUnavailable = null;

// this request goes to admin-ajax
class FallbackError extends Error {}

// this request and the rest of the page go to admin-ajax
class RestUnavailableError extends FallbackError {}

export const legacyData = (action, values = {}) => {
    let data = {};
    values._action = action;
    for (let key of Object.keys(values)) {
        data[`${GLSR.nameprefix}[${key}]`] = values[key];
    }
    return data;
}

export const legacyPost = async (formOrData, headers = {}) => {
    try {
        const { text } = await xhr(GLSR.ajax_url, { body: _legacyBody(formOrData), headers });
        const json = JSON.parse(text);
        return { data: json.data, success: json.success };
    } catch (e) {
        return { data: { message: e.message }, success: false };
    }
}

const pagedReviews = (values) => send({
    legacy: () => {
        const legacyValues = { page: values.page, schema: values.schema, url: values.url };
        Object.entries(values.atts || {}).forEach(([key, value]) => legacyValues[`atts][${key}`] = value);
        return legacyData('fetch-paged-reviews', legacyValues);
    },
    method: 'GET',
    params: { atts: values.atts, page: values.page, schema: values.schema, url: values.url },
    path: 'render/reviews',
})

const review = (reviewId, values = {}) => send({
    legacy: () => legacyData(values.verified ? 'verified-review' : 'approved-review', Object.assign({}, values, { review_id: reviewId })),
    method: 'GET',
    params: values,
    path: `render/reviews/${reviewId}`,
})

// The submissions route creates a review. A form with another action is
// an addon's, and its action is routed by admin-ajax.
const submit = (formData) => {
    const action = formData.get(`${GLSR.nameprefix}[_action]`);
    if (action && 'submit-review' !== action) {
        return legacyPost(formData);
    }
    return send({
        body: formData,
        legacy: () => formData,
        method: 'POST',
        path: 'submissions',
    });
}

const _legacyBody = (data) => {
    let formData = new FormData();
    const objectType = Object.prototype.toString.call(data);
    if ('[object FormData]' === objectType) {
        formData = data;
    }
    if ('[object HTMLFormElement]' === objectType) {
        formData = new FormData(data);
    }
    if ('[object Object]' === objectType) {
        Object.keys(data).forEach(key => formData.append(key, data[key]));
    }
    formData.append('action', GLSR.action);
    formData.append('_ajax_request', true);
    return formData;
}

const _notice = (reason) => {
    if (!noticeShown) {
        console.info(`Site Reviews is using the admin-ajax fallback (${reason}).`);
        noticeShown = true;
    }
}

const _rest = async (method, path, params, body) => {
    if (restUnavailable) {
        throw restUnavailable;
    }
    if (!GLSR.rest_url) {
        throw new RestUnavailableError('no REST URL');
    }
    const headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (GLSR.rest_nonce) {
        headers['X-WP-Nonce'] = GLSR.rest_nonce;
    }
    const response = await fetch(_restUrl(path, params), { body, headers, method });
    if ('function' !== typeof response?.json) {
        throw new RestUnavailableError('window.fetch has been replaced by another script and returned no Response');
    }
    if (!(response.headers.get('content-type') || '').includes('application/json')) {
        throw new FallbackError(`unexpected response: HTTP ${response.status}`);
    }
    const json = await response.json();
    if (response.ok) {
        return { data: json, success: true };
    }
    if (FALLBACK_CODES.includes(json.code)) {
        throw new FallbackError(json.code);
    }
    if ([401, 403].includes(response.status) || REST_UNAVAILABLE_CODES.includes(json.code)) {
        throw new RestUnavailableError(json.code || `HTTP ${response.status}`);
    }
    return { data: json, success: false }; // a final error from a Site Reviews route
}

const _restUrl = (path, params = {}) => {
    const url = new URL(GLSR.rest_url);
    if (url.searchParams.has('rest_route')) { // plain permalinks
        url.searchParams.set('rest_route', url.searchParams.get('rest_route').replace(/\/$/, '') + '/' + path);
    } else {
        url.pathname = url.pathname.replace(/\/$/, '') + '/' + path;
    }
    Object.entries(params || {}).forEach(([key, value]) => {
        if (undefined === value || null === value) return;
        if ('[object Object]' === Object.prototype.toString.call(value)) {
            Object.entries(value).forEach(([k, v]) => url.searchParams.append(`${key}[${k}]`, v));
        } else {
            url.searchParams.append(key, value);
        }
    });
    return url;
}

/**
 * The generic transport: REST first, admin-ajax on any signal that the REST
 * API itself is unavailable. `legacy` is a thunk building the admin-ajax
 * body (see legacyData) so the fallback costs nothing on the REST path.
 * Public surface — addons ride it for their own routes (GLSR.request).
 */
const send = async ({ body, legacy, method, params, path }) => {
    try {
        return await _rest(method, path, params, body);
    } catch (e) {
        _notice(e.message);
        if (e instanceof RestUnavailableError) {
            restUnavailable = e;
        }
        return legacyPost(legacy());
    }
}

export default { data: legacyData, pagedReviews, review, send, submit }
