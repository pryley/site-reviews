const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin, loadPublic, publicConfig } = require('./harness.js');

const AJAX_URL = 'https://example.org/wp-admin/admin-ajax.php';
const REST_URL = 'https://example.org/wp-json/site-reviews/v1/';

// Values that cross the jsdom realm are compared as JSON.
const plain = (value) => JSON.parse(JSON.stringify(value));

// a response is { status, json, text, type }, or { error } to reject with
const fakeFetch = (window, responses) => {
    const calls = [];
    window.fetch = async (url, options = {}) => {
        calls.push({ url: String(url), options })
        const { status = 200, json, text, type = 'application/json', ...rest } = responses.shift();
        if (rest.error) {
            throw new Error(rest.error);
        }
        return {
            headers: { get: () => type },
            json: async () => json,
            ok: status >= 200 && status < 300,
            status,
            text: async () => text,
        };
    };
    return calls;
};

const fields = (call) => Object.fromEntries(call.options.body.entries());

const request = (overrides = {}) => ({ ...publicConfig().request, ...overrides });

test('Request has four members, in both scripts', () => {
    assert.deepEqual(Object.keys(loadPublic().window.GLSR.Request).sort(), ['pagedReviews', 'review', 'send', 'submit'])
    assert.deepEqual(Object.keys(loadAdmin().window.GLSR.Request).sort(), ['pagedReviews', 'review', 'send', 'submit'])
});

test('a request goes to the REST API, with its params in the query', async () => {
    const { window } = loadPublic();
    const calls = fakeFetch(window, [{ json: { reviews: '<div></div>' } }]);
    const result = await window.GLSR.Request.send({ path: 'my-addon/route', params: { page: 2, atts: { display: 5 }, skipped: null } });
    assert.equal(calls.length, 1)
    assert.equal(calls[0].url, `${REST_URL}my-addon/route?page=2&atts%5Bdisplay%5D=5`)
    assert.equal(calls[0].options.method, 'GET')
    assert.deepEqual(plain(calls[0].options.headers), { 'X-Requested-With': 'XMLHttpRequest' })
    assert.deepEqual(plain(result), { data: { reviews: '<div></div>' }, status: 200, success: true })
});

test('a logged-in page sends its nonce', async () => {
    const { window } = loadPublic({ config: { request: request({ nonce: 'abc123' }) } });
    const calls = fakeFetch(window, [{ json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(calls[0].options.headers['X-WP-Nonce'], 'abc123')
});

test('an error from the route is the final answer', async () => {
    const { window } = loadPublic();
    const calls = fakeFetch(window, [{ status: 400, json: { errors: { title: ['This field is required.'] } } }]);
    const result = await window.GLSR.Request.send({ path: 'submissions', method: 'POST' });
    assert.equal(calls.length, 1)
    assert.deepEqual(plain(result), { data: { errors: { title: ['This field is required.'] } }, status: 400, success: false })
});

for (const [reason, blocked] of [
    ['a 403 from a plugin that disables the REST API', { status: 403, json: { code: 'rest_forbidden' } }],
    ['a 401 for logged-out visitors', { status: 401, json: { code: 'rest_login_required' } }],
    ['a removed route', { status: 404, json: { code: 'rest_no_route' } }],
    ['a response that is not JSON', { status: 200, type: 'text/html', text: '<html>' }],
    ['a network error', { error: 'Failed to fetch' }],
]) {
    test(`after ${reason}, the same request goes over admin-ajax`, async () => {
        const { infos, window } = loadPublic();
        const calls = fakeFetch(window, [blocked, { json: { reviews: '<div></div>' } }]);
        const result = await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2, atts: { display: 5 } } });
        assert.equal(calls.length, 2)
        assert.equal(calls[1].url, AJAX_URL)
        assert.equal(calls[1].options.method, 'POST')
        assert.deepEqual(fields(calls[1]), {
            _rest_method: 'GET',
            _rest_path: 'render/reviews',
            _rest_query: 'page=2&atts%5Bdisplay%5D=5',
            action: 'glsr_rest_request',
        })
        assert.deepEqual(plain(result), { data: { reviews: '<div></div>' }, status: 200, success: true })
        assert.equal(infos.filter((line) => line.includes('admin-ajax fallback')).length, 1)
    });
}

test('a submission over admin-ajax carries the form fields, and leaves the form data alone', async () => {
    const { window } = loadPublic({ config: { request: request({ nonce: 'abc123' }) } });
    const calls = fakeFetch(window, [{ status: 403, json: { code: 'rest_forbidden' } }, { status: 201, json: { success: true } }]);
    const formData = new window.FormData();
    formData.append('site-reviews[title]', 'A lovely stay')
    formData.append('cf-turnstile-response', 'token')
    const result = await window.GLSR.Request.submit(formData);
    assert.equal(calls[0].options.body, formData)
    assert.deepEqual(fields(calls[1]), {
        _rest_method: 'POST',
        _rest_nonce: 'abc123',
        _rest_path: 'submissions',
        _rest_query: '',
        action: 'glsr_rest_request',
        'cf-turnstile-response': 'token',
        'site-reviews[title]': 'A lovely stay',
    })
    assert.equal(formData.has('action'), false)
    assert.deepEqual(plain(result), { data: { success: true }, status: 201, success: true })
});

test('an expired nonce is replaced with a fresh one and the request is sent again', async () => {
    const { window } = loadPublic({ config: { request: request({ nonce: 'expired' }) } });
    const calls = fakeFetch(window, [
        { status: 403, json: { code: 'rest_cookie_invalid_nonce' } },
        { type: 'text/html', text: 'fresh' },
        { json: { reviews: '' } },
    ]);
    const result = await window.GLSR.Request.send({ path: 'render/reviews' });
    assert.equal(calls.length, 3)
    assert.equal(calls[0].options.headers['X-WP-Nonce'], 'expired')
    assert.equal(calls[1].url, `${AJAX_URL}?action=rest-nonce`)
    assert.equal(calls[2].url, `${REST_URL}render/reviews`)
    assert.equal(calls[2].options.headers['X-WP-Nonce'], 'fresh')
    assert.equal(result.success, true)
});

test('when the login has ended, the request is sent again without a nonce', async () => {
    const { window } = loadPublic({ config: { request: request({ nonce: 'expired' }) } });
    const calls = fakeFetch(window, [
        { status: 403, json: { code: 'rest_cookie_invalid_nonce' } },
        { status: 400, type: 'text/html', text: '0' },
        { json: { reviews: '' } },
    ]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(calls.length, 3)
    assert.equal('X-WP-Nonce' in calls[2].options.headers, false)
});

test('with plain permalinks the route goes in rest_route, and never into the admin-ajax fields', async () => {
    const { window } = loadPublic({ config: { request: request({ url: 'https://example.org/index.php?rest_route=/site-reviews/v1/' }) } });
    const calls = fakeFetch(window, [{ status: 403, json: { code: 'rest_forbidden' } }, { json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2 } })
    assert.equal(calls[0].url, 'https://example.org/index.php?rest_route=%2Fsite-reviews%2Fv1%2Frender%2Freviews&page=2')
    assert.equal(fields(calls[1])._rest_query, 'page=2')
    assert.equal(Object.keys(fields(calls[1])).some((key) => key.includes('rest_route')), false)
});

test('pagedReviews, review and submit are the requests of the plugin', async () => {
    const { window } = loadPublic();
    const calls = fakeFetch(window, [{ json: {} }, { json: {} }, { json: {} }]);
    await window.GLSR.Request.pagedReviews({ atts: { display: 5 }, page: 3, schema: false, url: 'https://example.org/reviews/' })
    await window.GLSR.Request.review(13, { theme: 4 })
    await window.GLSR.Request.submit(new window.FormData())
    assert.equal(calls[0].url, `${REST_URL}render/reviews?atts%5Bdisplay%5D=5&page=3&schema=false&url=https%3A%2F%2Fexample.org%2Freviews%2F`)
    assert.equal(calls[1].url, `${REST_URL}render/reviews/13?theme=4`)
    assert.equal(calls[2].url, `${REST_URL}submissions`)
    assert.equal(calls[2].options.method, 'POST')
});

const formOf = (window, action) => {
    const formData = new window.FormData();
    formData.append('site-reviews[_action]', action)
    formData.append('site-reviews[title]', 'A lovely stay')
    return formData;
};

test('a form is posted to the route of its action', async () => {
    const routes = { 'submit-review': 'submissions', 'update-review': 'authors/update-review' };
    const { window } = loadPublic({ config: { request: request({ routes }) } });
    const calls = fakeFetch(window, [{ json: {} }, { json: {} }]);
    await window.GLSR.Request.submit(formOf(window, 'update-review'))
    await window.GLSR.Request.submit(formOf(window, 'submit-review'))
    assert.equal(calls[0].url, `${REST_URL}authors/update-review`)
    assert.equal(calls[0].options.method, 'POST')
    assert.equal(calls[1].url, `${REST_URL}submissions`)
});

test('a config without routes still posts the review form to submissions', async () => {
    const { routes, ...withoutRoutes } = request();
    const { window } = loadPublic({ config: { request: withoutRoutes } });
    const calls = fakeFetch(window, [{ json: {} }]);
    await window.GLSR.Request.submit(formOf(window, 'submit-review'))
    assert.equal(calls[0].url, `${REST_URL}submissions`)
});

test('with compat mode, a form whose action has no route is posted to admin-ajax, and debug mode says so', async () => {
    const { warnings, window } = loadPublic({ debug: true });
    const calls = fakeFetch(window, [{ json: { data: { message: 'Saved.' }, success: true } }]);
    const formData = formOf(window, 'update-review');
    const result = await window.GLSR.Request.submit(formData);
    assert.equal(calls.length, 1)
    assert.equal(calls[0].url, AJAX_URL)
    assert.deepEqual(fields(calls[0]), {
        _ajax_request: 'true',
        action: 'glsr_public_action',
        'site-reviews[_action]': 'update-review',
        'site-reviews[title]': 'A lovely stay',
    })
    assert.equal(formData.has('action'), false)
    assert.deepEqual(plain(result), { data: { message: 'Saved.' }, status: 200, success: true })
    assert.equal(warnings.filter(text => text.includes('The form action "update-review" over admin-ajax is deprecated')).length, 1)
});

test('without compat mode, a form whose action has no route is not sent', async () => {
    const { window } = loadPublic({ compat: false });
    const errors = [];
    window.console.error = (...args) => errors.push(args.join(' '));
    const calls = fakeFetch(window, []);
    const result = await window.GLSR.Request.submit(formOf(window, 'update-review'));
    assert.equal(calls.length, 0)
    assert.deepEqual(plain(result), { data: { message: 'No route is registered for the form action "update-review".' }, status: 0, success: false })
    assert.equal(errors.length, 1)
});

for (const status of [401, 403, 404]) {
    test(`a ${status} with a glsr_ code is the answer of the route, and is not sent again`, async () => {
        const { infos, window } = loadPublic();
        const refusal = { code: 'glsr_forbidden', message: 'You cannot edit this review.' };
        const calls = fakeFetch(window, [{ status, json: refusal }]);
        const result = await window.GLSR.Request.send({ method: 'POST', path: 'authors/update-review' });
        assert.equal(calls.length, 1)
        assert.deepEqual(plain(result), { data: refusal, status, success: false })
        assert.deepEqual(infos, [])
    });
}

test('debug mode logs each request with its transport, status and time', async () => {
    const { infos, window } = loadPublic({ debug: true });
    fakeFetch(window, [{ json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.match(infos[0], /^\[site-reviews\] GET render\/reviews: REST, 200, \d+ms$/)
});

test('GLSR.request and GLSR.ajax of 8.x are hidden getters', async () => {
    const { window } = loadPublic();
    const { GLSR } = window;
    assert.equal(GLSR.request.send, GLSR.Request.send)
    assert.deepEqual(plain(GLSR.request.data('my-action', { page: 2 })), { 'site-reviews[_action]': 'my-action', 'site-reviews[page]': 2 })
    assert.equal(GLSR.ajax.data, GLSR.request.data)
    assert.deepEqual(Object.keys(GLSR.ajax).sort(), ['data', 'get', 'post'])
    assert.equal(Object.getOwnPropertyDescriptor(GLSR, 'ajax').enumerable, false)
    // an 8.3 caller passed `legacy`; the request is the same without it
    const calls = fakeFetch(window, [{ json: {} }]);
    await GLSR.request.send({ path: 'my-addon/route', legacy: () => GLSR.request.data('my-action') })
    assert.equal(calls[0].url, `${REST_URL}my-addon/route`)
});

test('GLSR.ajax.post posts to the Router action and calls back with the data and the success flag', async () => {
    const { window } = loadPublic();
    const calls = fakeFetch(window, [{ json: { data: { message: 'ok' }, success: true } }]);
    const [data, success] = await new Promise((resolve) => {
        window.GLSR.ajax.post(window.GLSR.ajax.data('my-action', { id: 7 }), (...args) => resolve(args))
    });
    assert.equal(calls[0].url, AJAX_URL)
    assert.deepEqual(fields(calls[0]), {
        _ajax_request: 'true',
        action: 'glsr_public_action',
        'site-reviews[_action]': 'my-action',
        'site-reviews[id]': '7',
    })
    assert.deepEqual(plain(data), { message: 'ok' })
    assert.equal(success, true)
});
