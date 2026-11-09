const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadNotices } = require('./harness.js');

// What a screen without the admin bundle prints: a dismissible notice of ours, with WordPress's X.
const HTML = `
<hr class="wp-header-end">
<div class="notice notice-error is-dismissible glsr-notice bulk-action-notice" data-notice="gatekeeper">
    <p>Something is wrong. <button type="button" class="button-link bulk-action-errors-collapsed" aria-expanded="false">Show more details</button></p>
    <ul class="bulk-action-errors hidden"><li>Detail</li></ul>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
</div>
<div class="notice glsr-notice glsr-notice-popup" data-notice="write-review">
    <div class="glsr-notice-buttons"><button type="button" data-dismiss="interval">Remind me later</button></div>
</div>`;

const fakeFetch = (window) => {
    const calls = [];
    window.fetch = async (url, options = {}) => {
        calls.push({ options, url: String(url) })
        return { headers: { get: () => 'application/json' }, json: async () => ({}), ok: true, status: 200, text: async () => '{}' };
    };
    return calls;
};

test('the X of a notice sends its dismissal to plugin/notices/{key}', async () => {
    const { window } = loadNotices({ html: HTML });
    const calls = fakeFetch(window);
    window.document.querySelector('[data-notice="gatekeeper"] .notice-dismiss').click()
    await new Promise(resolve => setTimeout(resolve, 10));
    assert.equal(calls.length, 1)
    assert.ok(calls[0].url.endsWith('/site-reviews/v1/plugin/notices/gatekeeper'))
    assert.equal(calls[0].options.headers['X-HTTP-Method-Override'], 'DELETE')
});

test('a "remind me later" button snoozes the notice with a PUT', async () => {
    const { window } = loadNotices({ html: HTML });
    const calls = fakeFetch(window);
    window.document.querySelector('[data-notice="write-review"] [data-dismiss="interval"]').click()
    await new Promise(resolve => setTimeout(resolve, 10));
    assert.ok(calls[0].url.endsWith('/plugin/notices/write-review'))
    assert.equal(calls[0].options.headers['X-HTTP-Method-Override'], 'PUT')
    assert.equal(JSON.parse(calls[0].options.body).until, 'interval')
});

test('the details toggle of a bulk-action notice opens and closes the list', () => {
    const { window } = loadNotices({ html: HTML });
    const button = window.document.querySelector('.bulk-action-notice button.button-link');
    const errors = window.document.querySelector('.bulk-action-errors');
    button.click()
    assert.equal(errors.classList.contains('hidden'), false)
    assert.equal(button.getAttribute('aria-expanded'), 'true')
    button.click()
    assert.equal(errors.classList.contains('hidden'), true)
});

test('the banner moves into the page header and shows after a second', async () => {
    const { window } = loadNotices({ html: `<div id="glsr-page-header"></div><div class="glsr-notice glsr-notice-banner" data-notice="license-promoted" style="display: none;"></div>` });
    const banner = window.document.querySelector('#glsr-page-header > .glsr-notice-banner');
    assert.ok(banner)
    await new Promise(resolve => setTimeout(resolve, 1100));
    assert.equal(window.getComputedStyle(banner).display, 'block')
});
