import assert from 'node:assert/strict';
import { test } from 'node:test';
import harness from './harness.js';
import { loadPage, publicConfig } from './page.mjs';

const { loadAdmin } = harness;

const AJAX_URL = 'https://example.org/wp-admin/admin-ajax.php';
const REST_URL = 'https://example.org/wp-json/site-reviews/v1/';

// Values that cross the jsdom realm are compared as JSON.
const plain = (value) => JSON.parse(JSON.stringify(value));

// a response is { status, json, text, type }, or { error } to reject with
const fakeFetch = (window, responses) => {
    const calls = [];
    window.fetch = async (url, options = {}) => {
        calls.push({ url: String(url), options })
        if (options.signal?.aborted) {
            throw new DOMException('The operation was aborted.', 'AbortError');
        }
        const { error, json, status = 200, text, type = 'application/json' } = responses.shift();
        if (error) {
            throw new Error(error);
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

// jsdom's XMLHttpRequest would send the request; a response is { status, json, text } or { error }
const fakeXhr = (window, responses) => {
    const calls = [];
    window.XMLHttpRequest = class {
        headers = {};
        open(method, url) {
            this.method = method;
            this.url = String(url);
        }
        setRequestHeader(name, value) {
            this.headers[name] = value;
        }
        send(body) {
            calls.push({ url: this.url, options: { body, headers: this.headers, method: this.method } })
            const { error, json, status = 200, text } = responses.shift();
            this.status = status;
            this.responseText = text ?? JSON.stringify(json);
            Promise.resolve().then(() => error ? this.onerror() : this.onload())
        }
    };
    return calls;
};

const fields = (call) => Object.fromEntries(call.options.body.entries());

const parseErrorMessage = (window, text) => {
    try {
        window.JSON.parse(text);
    } catch (e) {
        return e.message;
    }
};

const request = (overrides = {}) => ({ ...publicConfig().request, ...overrides });

test('Request has five members, in both scripts, before and after the request module loads', async () => {
    const { window } = loadPage();
    assert.deepEqual(Object.keys(window.GLSR.Request).sort(), ['log', 'pagedReviews', 'review', 'send', 'submit'])
    await window.GLSR.load('request')
    assert.deepEqual(Object.keys(window.GLSR.Request).sort(), ['log', 'pagedReviews', 'review', 'send', 'submit'])
    assert.deepEqual(Object.keys(loadAdmin().window.GLSR.Request).sort(), ['log', 'pagedReviews', 'review', 'send', 'submit'])
});

for (const [status, level] of [[0, 'error'], [400, 'warn'], [403, 'warn'], [429, 'warn'], [499, 'warn'], [500, 'error'], [503, 'error']]) {
    test(`Request.log logs a status of ${status} with console.${level}`, async () => {
        const { window } = loadPage();
        const lines = [];
        window.console.error = (details) => lines.push(['error', details]);
        window.console.warn = (details) => lines.push(['warn', details]);
        await window.GLSR.Request.log(status, 'the details')
        assert.deepEqual(lines, [[level, 'the details']])
    });
}

test('a request goes to the REST API, with its params in the query', async () => {
    const { window } = loadPage();
    const calls = fakeFetch(window, [{ json: { reviews: '<div></div>' } }]);
    const result = await window.GLSR.Request.send({ path: 'my-addon/route', params: { page: 2, atts: { display: 5 }, skipped: null } });
    assert.equal(calls.length, 1)
    assert.equal(calls[0].url, `${REST_URL}my-addon/route?page=2&atts%5Bdisplay%5D=5`)
    assert.equal(calls[0].options.method, 'GET')
    assert.deepEqual(plain(calls[0].options.headers), { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' })
    assert.deepEqual(plain(result), { data: { reviews: '<div></div>' }, status: 200, success: true })
});

for (const [reason, failed] of [
    ['a fatal error, which WordPress answers as JSON because the request accepts it', { status: 500, json: { code: 'internal_server_error', message: 'There has been a critical error on this website.' } }],
    ['a 500 page that is not JSON', { status: 500, type: 'text/html', text: '<html>Fatal error</html>' }],
    ['a 502 from the server', { status: 502, type: 'text/html', text: '<html>Bad gateway</html>' }],
]) {
    test(`${reason} is the final answer, and the request is not run again over admin-ajax`, async () => {
        const { infos, window } = loadPage();
        const calls = fakeFetch(window, [failed]);
        const sent = fakeXhr(window, [{ json: {} }]);
        const result = await window.GLSR.Request.send({ method: 'POST', path: 'reviews/import' });
        assert.equal(calls.length, 1)
        assert.equal(sent.length, 0)
        assert.equal(result.success, false)
        assert.equal(result.status, failed.status)
        assert.deepEqual(plain(result.data), failed.json ?? { message: `HTTP ${failed.status}` })
        assert.deepEqual(infos, [])
    });
}

test('a logged-in page sends its nonce', async () => {
    const { window } = loadPage({ config: { request: request({ nonce: 'abc123' }) } });
    const calls = fakeFetch(window, [{ json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(calls[0].options.headers['X-WP-Nonce'], 'abc123')
});

test('an error from the route is the final answer', async () => {
    const { window } = loadPage();
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
        const { infos, window } = loadPage();
        const calls = fakeFetch(window, [blocked]);
        const sent = fakeXhr(window, [{ json: { reviews: '<div></div>' } }]);
        const result = await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2, atts: { display: 5 } } });
        assert.equal(calls.length, 1)
        assert.equal(sent.length, 1)
        assert.equal(sent[0].url, AJAX_URL)
        assert.equal(sent[0].options.method, 'POST')
        assert.deepEqual(plain(sent[0].options.headers), { 'X-Requested-With': 'XMLHttpRequest' })
        assert.deepEqual(fields(sent[0]), {
            _rest_method: 'GET',
            _rest_path: 'render/reviews',
            _rest_query: 'page=2&atts%5Bdisplay%5D=5',
            action: 'glsr_rest_request',
        })
        assert.deepEqual(plain(result), { data: { reviews: '<div></div>' }, status: 200, success: true })
        assert.equal(infos.filter((line) => line.includes('admin-ajax fallback')).length, 1)
    });
}

for (const [reason, blocked] of [
    ['a 403 from a plugin that disables the REST API', { status: 403, json: { code: 'rest_forbidden' } }],
    ['a 401 for logged-out visitors', { status: 401, json: { code: 'rest_login_required' } }],
    ['a 503 with rest_disabled', { status: 503, json: { code: 'rest_disabled' } }],
]) {
    test(`after ${reason}, the rest of the page goes over admin-ajax without asking the REST API again`, async () => {
        const { infos, window } = loadPage();
        const calls = fakeFetch(window, [blocked]);
        const sent = fakeXhr(window, [{ json: { reviews: '<div>2</div>' } }, { json: { reviews: '<div>3</div>' } }]);
        await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2 } })
        const result = await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 3 } });
        assert.equal(calls.length, 1)
        assert.equal(sent.length, 2)
        assert.equal(fields(sent[1])._rest_query, 'page=3')
        assert.deepEqual(plain(result), { data: { reviews: '<div>3</div>' }, status: 200, success: true })
        assert.equal(infos.filter((line) => line.includes('admin-ajax fallback')).length, 1)
    });
}

test('after a replaced window.fetch, the rest of the page goes over admin-ajax without calling it again', async () => {
    const { window } = loadPage();
    let fetched = 0;
    window.fetch = async () => { fetched++; };
    const sent = fakeXhr(window, [{ json: {} }, { json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(fetched, 1)
    assert.equal(sent.length, 2)
});

for (const [reason, blocked] of [
    ['a removed route', { status: 404, json: { code: 'rest_no_route' } }],
    ['a response that is not JSON', { status: 404, type: 'text/html', text: '<html>' }],
    ['a network error', { error: 'Failed to fetch' }],
]) {
    test(`after ${reason}, only that request goes over admin-ajax and the next one tries the REST API again`, async () => {
        // a route can be missing or broken on its own; the REST API is still there for the others
        const { window } = loadPage();
        const calls = fakeFetch(window, [blocked, { json: { reviews: '<div>REST</div>' } }]);
        const sent = fakeXhr(window, [{ json: { reviews: '<div>admin-ajax</div>' } }]);
        await window.GLSR.Request.send({ path: 'my-addon/route' })
        const result = await window.GLSR.Request.send({ path: 'render/reviews' });
        assert.equal(calls.length, 2)
        assert.equal(sent.length, 1)
        assert.deepEqual(plain(result), { data: { reviews: '<div>REST</div>' }, status: 200, success: true })
    });
}

test('an aborted request is given up, and is not sent again over admin-ajax', async () => {
    const { infos, window } = loadPage();
    const controller = new AbortController();
    const calls = fakeFetch(window, [{ json: {} }]);
    const sent = fakeXhr(window, [{ json: {} }]);
    controller.abort()
    const result = await window.GLSR.Request.send({ path: 'search/posts', params: { search: 'a' }, signal: controller.signal });
    assert.equal(calls.length, 1)
    assert.equal(calls[0].options.signal, controller.signal)
    assert.equal(sent.length, 0)
    assert.deepEqual(plain(result), { data: { message: 'The operation was aborted.' }, status: 0, success: false })
    assert.deepEqual(infos, [])
});

test('over admin-ajax too, an aborted request is given up', async () => {
    const { url, ...withoutUrl } = request();
    const { window } = loadPage({ config: { request: withoutUrl } });
    const controller = new AbortController();
    const sent = fakeXhr(window, [{ json: {} }]);
    controller.abort()
    const result = await window.GLSR.Request.send({ path: 'search/posts', signal: controller.signal });
    assert.equal(sent.length, 0)
    assert.deepEqual(plain(result), { data: { message: 'The request was aborted.' }, status: 0, success: false })
});

test('a config without a REST URL goes straight to admin-ajax', async () => {
    // the guard for an inline config that has no REST URL, such as one a filter changed
    const { url, ...withoutUrl } = request();
    const { infos, window } = loadPage({ config: { request: withoutUrl } });
    const calls = fakeFetch(window, []);
    const sent = fakeXhr(window, [{ json: { reviews: '<div></div>' } }]);
    const result = await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2 } });
    assert.equal(calls.length, 0)
    assert.equal(sent.length, 1)
    assert.equal(sent[0].url, AJAX_URL)
    assert.equal(fields(sent[0])._rest_query, 'page=2')
    assert.deepEqual(plain(result), { data: { reviews: '<div></div>' }, status: 200, success: true })
    assert.deepEqual(infos, ['Site Reviews is using the admin-ajax fallback (no REST URL).'])
});

test('a submission over admin-ajax carries the form fields, and leaves the form data alone', async () => {
    const { window } = loadPage({ config: { request: request({ nonce: 'abc123' }) } });
    const calls = fakeFetch(window, [{ status: 403, json: { code: 'rest_forbidden' } }]);
    const sent = fakeXhr(window, [{ status: 201, json: { success: true } }]);
    const formData = new window.FormData();
    formData.append('site-reviews[title]', 'A lovely stay')
    formData.append('cf-turnstile-response', 'token')
    const result = await window.GLSR.Request.submit(formData);
    assert.equal(calls[0].options.body, formData)
    assert.deepEqual(fields(sent[0]), {
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

for (const method of ['DELETE', 'PATCH', 'PUT']) {
    test(`a ${method} goes to the REST API as a POST with the X-HTTP-Method-Override header, and over admin-ajax as itself`, async () => {
        const { window } = loadPage();
        const calls = fakeFetch(window, [{ json: {} }, { status: 403, json: { code: 'rest_forbidden' } }]);
        const sent = fakeXhr(window, [{ json: {} }]);
        const body = new window.FormData();
        body.append('value', '1')
        await window.GLSR.Request.send({ body, method, path: 'reviews/7/pinned' })
        assert.equal(calls[0].options.method, 'POST')
        assert.equal(calls[0].options.headers['X-HTTP-Method-Override'], method)
        assert.equal(calls[0].options.body, body)
        await window.GLSR.Request.send({ body, method, path: 'reviews/7/pinned' })
        assert.equal(fields(sent[0])._rest_method, method)
        assert.equal(fields(sent[0]).value, '1')
    });
}

test('a GET and a POST are sent as themselves', async () => {
    const { window } = loadPage();
    const calls = fakeFetch(window, [{ json: {} }, { json: {} }]);
    await window.GLSR.Request.send({ path: 'plugin/console' })
    await window.GLSR.Request.send({ method: 'POST', path: 'plugin/migrations' })
    assert.equal(calls[0].options.method, 'GET')
    assert.equal('X-HTTP-Method-Override' in calls[0].options.headers, false)
    assert.equal(calls[1].options.method, 'POST')
    assert.equal('X-HTTP-Method-Override' in calls[1].options.headers, false)
});

test('a body that is not a FormData is sent as JSON, over the REST API and over admin-ajax', async () => {
    const { window } = loadPage({ config: { request: request({ nonce: 'abc123' }) } });
    const calls = fakeFetch(window, [{ json: {} }, { status: 403, json: { code: 'rest_forbidden' } }]);
    const sent = fakeXhr(window, [{ json: {} }]);
    const body = { meta: { 'edit_site-review_filters': [] } };
    await window.GLSR.Request.send({ body, method: 'POST', path: '/wp/v2/users/me' })
    assert.equal(calls[0].options.body, JSON.stringify(body))
    assert.deepEqual(plain(calls[0].options.headers), { Accept: 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-WP-Nonce': 'abc123' })
    await window.GLSR.Request.send({ body, method: 'POST', path: '/wp/v2/users/me' })
    assert.deepEqual(fields(sent[0]), {
        _rest_body: JSON.stringify(body),
        _rest_method: 'POST',
        _rest_nonce: 'abc123',
        _rest_path: '/wp/v2/users/me',
        _rest_query: '',
        action: 'glsr_rest_request',
    })
});

test('when a page script has replaced window.fetch with one that returns nothing, the review is submitted over admin-ajax', async () => {
    const { infos, window } = loadPage();
    window.fetch = async () => undefined;
    const sent = fakeXhr(window, [{ status: 201, json: { success: true } }]);
    const result = await window.GLSR.Request.submit(formOf(window, 'submit-review'));
    assert.equal(sent.length, 1)
    assert.equal(sent[0].url, AJAX_URL)
    assert.equal(fields(sent[0])._rest_path, 'submissions')
    assert.deepEqual(plain(result), { data: { success: true }, status: 201, success: true })
    assert.deepEqual(infos, ['Site Reviews is using the admin-ajax fallback (window.fetch has been replaced by another script and returned no Response).'])
});

test('a failed admin-ajax request is the answer, with its status', async () => {
    const { window } = loadPage();
    fakeFetch(window, [{ status: 403, json: { code: 'rest_forbidden' } }, { status: 403, json: { code: 'rest_forbidden' } }]);
    fakeXhr(window, [{ status: 400, json: { errors: { title: ['This field is required.'] } } }, { error: 'network error' }]);
    const refused = await window.GLSR.Request.send({ method: 'POST', path: 'submissions' });
    assert.deepEqual(plain(refused), { data: { errors: { title: ['This field is required.'] } }, status: 400, success: false })
    const failed = await window.GLSR.Request.send({ method: 'POST', path: 'submissions' });
    assert.deepEqual(plain(failed), { data: { message: 'network error' }, status: 0, success: false })
});

test('an expired nonce is replaced with a fresh one and the request is sent again', async () => {
    const { window } = loadPage({ config: { request: request({ nonce: 'expired' }) } });
    const calls = fakeFetch(window, [
        { status: 403, json: { code: 'rest_cookie_invalid_nonce' } },
        { json: { reviews: '' } },
    ]);
    const sent = fakeXhr(window, [{ text: 'fresh' }]);
    const result = await window.GLSR.Request.send({ path: 'render/reviews' });
    assert.equal(calls.length, 2)
    assert.equal(calls[0].options.headers['X-WP-Nonce'], 'expired')
    assert.equal(sent.length, 1)
    assert.equal(sent[0].url, `${AJAX_URL}?action=rest-nonce`)
    assert.equal(sent[0].options.method, 'GET')
    assert.equal(calls[1].url, `${REST_URL}render/reviews`)
    assert.equal(calls[1].options.headers['X-WP-Nonce'], 'fresh')
    assert.equal(result.success, true)
});

test('when the login has ended, the request is sent again without a nonce', async () => {
    const { window } = loadPage({ config: { request: request({ nonce: 'expired' }) } });
    const calls = fakeFetch(window, [
        { status: 403, json: { code: 'rest_cookie_invalid_nonce' } },
        { json: { reviews: '' } },
    ]);
    fakeXhr(window, [{ status: 400, text: '0' }]); // admin-ajax: the rest-nonce action has no nopriv handler
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(calls.length, 2)
    assert.equal('X-WP-Nonce' in calls[1].options.headers, false)
});

test('when the nonce refresh fails, the request is sent again without a nonce', async () => {
    const { window } = loadPage({ config: { request: request({ nonce: 'expired' }) } });
    const calls = fakeFetch(window, [
        { status: 403, json: { code: 'rest_cookie_invalid_nonce' } },
        { json: { reviews: '' } },
    ]);
    fakeXhr(window, [{ error: 'network error' }]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(calls.length, 2)
    assert.equal('X-WP-Nonce' in calls[1].options.headers, false)
});

test('over admin-ajax too, an expired nonce is refreshed and the request is sent again', async () => {
    // the refresh must not use window.fetch either: it is the one request between a logged-in
    // submitter and a review saved as a visitor's, or refused on a site that requires a login
    const { window } = loadPage({ config: { request: request({ nonce: 'expired' }) } });
    window.fetch = async () => undefined;
    const sent = fakeXhr(window, [
        { status: 403, json: { code: 'rest_cookie_invalid_nonce' } },
        { text: 'fresh' },
        { status: 201, json: { success: true } },
    ]);
    const result = await window.GLSR.Request.submit(formOf(window, 'submit-review'));
    assert.equal(sent.length, 3)
    assert.equal(fields(sent[0])._rest_nonce, 'expired')
    assert.equal(sent[1].url, `${AJAX_URL}?action=rest-nonce`)
    assert.equal(sent[1].options.method, 'GET')
    assert.equal(fields(sent[2])._rest_nonce, 'fresh')
    assert.deepEqual(plain(result), { data: { success: true }, status: 201, success: true })
});

test('an admin-ajax answer that is not JSON is a failed request', async () => {
    const { window } = loadPage();
    fakeFetch(window, [{ status: 403, json: { code: 'rest_forbidden' } }]);
    fakeXhr(window, [{ status: 500, text: '<html>Fatal error</html>' }]);
    const result = await window.GLSR.Request.send({ method: 'POST', path: 'submissions' });
    assert.deepEqual(plain(result), { data: { message: parseErrorMessage(window, '<html>Fatal error</html>') }, status: 0, success: false })
});

test('with plain permalinks the route goes in rest_route, and never into the admin-ajax fields', async () => {
    const { window } = loadPage({ config: { request: request({ url: 'https://example.org/index.php?rest_route=/site-reviews/v1/' }) } });
    const calls = fakeFetch(window, [{ status: 403, json: { code: 'rest_forbidden' } }]);
    const sent = fakeXhr(window, [{ json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2 } })
    assert.equal(calls[0].url, 'https://example.org/index.php?rest_route=%2Fsite-reviews%2Fv1%2Frender%2Freviews&page=2')
    assert.equal(fields(sent[0])._rest_query, 'page=2')
    assert.equal(Object.keys(fields(sent[0])).some((key) => key.includes('rest_route')), false)
});

for (const [permalinks, url, expected] of [
    ['pretty permalinks', REST_URL, 'https://example.org/wp-json/wp/v2/users/me'],
    ['a filtered REST prefix', 'https://example.org/api/site-reviews/v1/', 'https://example.org/api/wp/v2/users/me'],
    ['a subdirectory install', 'https://example.org/blog/wp-json/site-reviews/v1/', 'https://example.org/blog/wp-json/wp/v2/users/me'],
    ['plain permalinks', 'https://example.org/index.php?rest_route=/site-reviews/v1/', 'https://example.org/index.php?rest_route=%2Fwp%2Fv2%2Fusers%2Fme'],
]) {
    test(`with ${permalinks}, a path that starts with / is read from the REST root, over admin-ajax as it is`, async () => {
        const { window } = loadPage({ config: { request: request({ url }) } });
        const calls = fakeFetch(window, [{ json: {} }, { status: 403, json: { code: 'rest_forbidden' } }]);
        const sent = fakeXhr(window, [{ json: {} }]);
        await window.GLSR.Request.send({ path: '/wp/v2/users/me' })
        assert.equal(calls[0].url, expected)
        await window.GLSR.Request.send({ path: '/wp/v2/users/me' })
        assert.equal(fields(sent[0])._rest_path, '/wp/v2/users/me')
    });
}

test('pagedReviews, review and submit are the requests of the plugin', async () => {
    const { window } = loadPage();
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
    const { window } = loadPage({ config: { request: request({ routes }) } });
    const calls = fakeFetch(window, [{ json: {} }, { json: {} }]);
    await window.GLSR.Request.submit(formOf(window, 'update-review'))
    await window.GLSR.Request.submit(formOf(window, 'submit-review'))
    assert.equal(calls[0].url, `${REST_URL}authors/update-review`)
    assert.equal(calls[0].options.method, 'POST')
    assert.equal(calls[1].url, `${REST_URL}submissions`)
});

test('a form is posted with the method of its route when the route names one', async () => {
    const routes = { 'update-review': { method: 'PATCH', path: 'authors/update-review' } };
    const { window } = loadPage({ config: { request: request({ routes }) } });
    const calls = fakeFetch(window, [{ json: {} }]);
    await window.GLSR.Request.submit(formOf(window, 'update-review'))
    assert.equal(calls[0].url, `${REST_URL}authors/update-review`)
    assert.equal(calls[0].options.method, 'POST')
    assert.equal(calls[0].options.headers['X-HTTP-Method-Override'], 'PATCH')
});

test('a {placeholder} in the route of a form is filled from the form\'s field of that name', async () => {
    const routes = { 'update-review': { method: 'PATCH', path: 'authors/reviews/{review_id}' } };
    const { window } = loadPage({ config: { request: request({ routes }) } });
    const calls = fakeFetch(window, [{ json: {} }]);
    const formData = formOf(window, 'update-review');
    formData.append('site-reviews[review_id]', '12')
    await window.GLSR.Request.submit(formData)
    assert.equal(calls[0].url, `${REST_URL}authors/reviews/12`)
    assert.equal(calls[0].options.headers['X-HTTP-Method-Override'], 'PATCH')
});

test('a request names a route of the config, whose path template is filled from the options', async () => {
    const routes = { status: { method: 'PUT', path: 'reviews/{id}/status' }, 'submit-review': 'submissions' };
    const { window } = loadPage({ config: { request: request({ routes }) } });
    const calls = fakeFetch(window, [{ json: {} }, { json: {} }]);
    const body = new window.FormData();
    await window.GLSR.Request.send({ body, id: 7, params: { context: 'list' }, route: 'status' })
    assert.equal(calls[0].url, `${REST_URL}reviews/7/status?context=list`)
    assert.equal(calls[0].options.headers['X-HTTP-Method-Override'], 'PUT')
    assert.equal(calls[0].options.body, body)
    await window.GLSR.Request.send({ route: 'submit-review' })
    assert.equal(calls[1].url, `${REST_URL}submissions`)
    assert.equal(calls[1].options.method, 'POST')
});

test('a request that names a route the config does not have is not sent', async () => {
    const { window } = loadPage();
    const errors = [];
    window.console.error = (...args) => errors.push(args.join(' '));
    const calls = fakeFetch(window, []);
    const result = await window.GLSR.Request.send({ id: 7, route: 'status' });
    assert.equal(calls.length, 0)
    assert.deepEqual(plain(result), { data: { message: 'No route is named "status".' }, status: 0, success: false })
    assert.deepEqual(errors, ['Site Reviews: the config names no route "status".'])
});

test('a config without routes sends no form', async () => {
    const { routes, ...withoutRoutes } = request();
    const { window } = loadPage({ config: { request: withoutRoutes } });
    const errors = [];
    window.console.error = (...args) => errors.push(args.join(' '));
    const calls = fakeFetch(window, []);
    const result = await window.GLSR.Request.submit(formOf(window, 'submit-review'));
    assert.equal(calls.length, 0)
    assert.equal(result.success, false)
    assert.equal(errors.length, 1)
});

test('a form whose action has no route is not sent', async () => {
    const { window } = loadPage();
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
        const { infos, window } = loadPage();
        const refusal = { code: 'glsr_forbidden', message: 'You cannot edit this review.' };
        const calls = fakeFetch(window, [{ status, json: refusal }]);
        const result = await window.GLSR.Request.send({ method: 'POST', path: 'authors/update-review' });
        assert.equal(calls.length, 1)
        assert.deepEqual(plain(result), { data: refusal, status, success: false })
        assert.deepEqual(infos, [])
    });
}

test('debug mode logs each request with its transport, status and time', async () => {
    const { infos, window } = loadPage({ debug: true });
    fakeFetch(window, [{ json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.match(infos[0], /^\[site-reviews\] GET render\/reviews: REST, 200, \d+ms$/)
});

test('without the inline config, a request is not sent and the answer says why', async () => {
    // an optimisation plugin moved the inline script, or a filter removed the request config
    const { window } = loadPage({ inline: '' });
    const errors = [];
    window.console.error = (...args) => errors.push(args.join(' '));
    const calls = fakeFetch(window, []);
    const sent = fakeXhr(window, []);
    const failed = { data: { message: 'The request was not sent: the Site Reviews config is missing.' }, status: 0, success: false };
    assert.deepEqual(plain(await window.GLSR.Request.send({ path: 'render/reviews' })), failed)
    assert.deepEqual(plain(await window.GLSR.Request.submit(formOf(window, 'submit-review'))), failed)
    assert.deepEqual(plain(await window.GLSR.Request.submit(formOf(window, 'update-review'))), failed)
    assert.equal(calls.length, 0)
    assert.equal(sent.length, 0)
    assert.equal(errors.length, 3)
});

test('under Polylang or WPML, a request carries the language of the page, over admin-ajax too', async () => {
    const { window } = loadPage({ config: { request: request({ language: 'de' }) } });
    const calls = fakeFetch(window, [{ json: {} }, { status: 404, json: { code: 'rest_no_route' } }]);
    const sent = fakeXhr(window, [{ json: {} }]);
    await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2 } })
    assert.equal(calls[0].url, `${REST_URL}render/reviews?lang=de&page=2`)
    await window.GLSR.Request.send({ path: 'render/reviews', params: { page: 2 } })
    assert.equal(fields(sent[0]).lang, 'de')
    assert.equal(fields(sent[0])._rest_query, 'page=2')
});
