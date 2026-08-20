/** global: FormData, GLSR */

// REST error codes that mean the REST API itself is unavailable (security plugin,
// stale nonce, removed routes) rather than a final answer from a Site Reviews route.
const FALLBACK_CODES = ['rest_cookie_invalid_nonce', 'rest_disabled', 'rest_forbidden', 'rest_no_route', 'rest_not_logged_in'];

let noticeShown = false;

class FallbackError extends Error {}

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
        const response = await fetch(GLSR.ajax_url, {
            body: _legacyBody(formOrData),
            headers: Object.assign({}, headers, { 'X-Requested-With': 'XMLHttpRequest' }),
            method: 'POST',
        });
        const json = await response.json();
        return { data: json.data, success: json.success };
    } catch (e) {
        return { data: { message: e.message }, success: false };
    }
}

const pagedReviews = (values) => _send({
    legacy: () => {
        const legacyValues = { page: values.page, schema: values.schema, url: values.url };
        Object.entries(values.atts || {}).forEach(([key, value]) => legacyValues[`atts][${key}`] = value);
        return legacyData('fetch-paged-reviews', legacyValues);
    },
    method: 'GET',
    params: { atts: values.atts, page: values.page, schema: values.schema, url: values.url },
    path: 'render/reviews',
})

const review = (reviewId, values = {}) => _send({
    legacy: () => legacyData(values.verified ? 'verified-review' : 'approved-review', Object.assign({}, values, { review_id: reviewId })),
    method: 'GET',
    params: values,
    path: `render/reviews/${reviewId}`,
})

const submit = (formData) => _send({
    body: formData,
    legacy: () => formData,
    method: 'POST',
    path: 'submissions',
})

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
    if (!GLSR.rest_url) {
        throw new FallbackError('no REST URL');
    }
    const headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (GLSR.rest_nonce) {
        headers['X-WP-Nonce'] = GLSR.rest_nonce;
    }
    const response = await fetch(_restUrl(path, params), { body, headers, method });
    if (!(response.headers.get('content-type') || '').includes('application/json')) {
        throw new FallbackError(`unexpected response: HTTP ${response.status}`);
    }
    const json = await response.json();
    if (response.ok) {
        return { data: json, success: true };
    }
    if ([401, 403].includes(response.status) || FALLBACK_CODES.includes(json.code)) {
        throw new FallbackError(json.code || `HTTP ${response.status}`);
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

const _send = async ({ body, legacy, method, params, path }) => {
    try {
        return await _rest(method, path, params, body);
    } catch (e) {
        _notice(e.message);
        return legacyPost(legacy());
    }
}

export default { pagedReviews, review, submit }
