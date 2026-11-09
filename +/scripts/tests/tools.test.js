const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// Values that cross the jsdom realm are compared as JSON.
const plain = (value) => JSON.parse(JSON.stringify(value));

// The buttons of views/pages/tools/general/import-reviews.php and views/integrations/woocommerce/tools/import-product-reviews.php.
const HTML = `
<div id="wpwrap">
    <hr class="wp-header-end">
    <form id="csv-import">
        <input type="hidden" name="site-reviews[_action]" value="import-reviews">
        <button type="submit" data-ajax-import data-per_page="50">Import Reviews</button>
    </form>
    <form id="product-import">
        <input type="hidden" name="site-reviews[_action]" value="import-product-reviews">
        <button type="submit" data-ajax-import data-args='{"source":"woocommerce"}' data-per_page="50">Import Reviews</button>
    </form>
    <form id="product-ratings">
        <input type="hidden" name="site-reviews[_action]" value="migrate-product-ratings">
        <button type="submit" data-ajax-click data-args='{"source":"woocommerce-ratings","stage":1}' data-route="import">Migrate Product Ratings</button>
    </form>
</div>`;

// the Tools page's body class, and a fetch that keeps each request and answers an import of one review
const BEFORE = `
document.body.className = 'site-review_page_glsr-tools';
window.sent = [];
window.fetch = async (url, options = {}) => {
    window.sent.push({ url: String(url), options });
    const json = { 1: { total: 1 }, 2: { imported: 1 } }[options.body.get('stage')] || {};
    return { headers: { get: () => 'application/json' }, json: async () => json, ok: true, status: 200, statusText: 'OK' };
};`;

// click the form's button and wait for `count` requests
const click = async (selector, count) => {
    const { window } = loadAdmin({ before: BEFORE, html: HTML });
    window.document.querySelector(`${selector} button`).click()
    for (let i = 0; i < 100 && window.sent.length < count; i++) {
        await new Promise(resolve => setTimeout(resolve, 10))
    }
    return plain([...window.sent].map(({ options, url }) => ({
        source: options.body.get('source'),
        stage: options.body.get('stage'),
        url: new URL(url).pathname,
    })));
};

test('the CSV import sends no source, which the import route reads as csv', async () => {
    const sent = await click('#csv-import', 3);
    assert.deepEqual(sent.map(({ stage }) => stage), ['1', '2', '4'])
    assert.ok(sent.every(({ source }) => null === source))
});

test('an import sends the source of its data-args with every stage', async () => {
    const sent = await click('#product-import', 3);
    assert.deepEqual(sent.map(({ stage }) => stage), ['1', '2', '4'])
    assert.ok(sent.every(({ source }) => 'woocommerce' === source))
});

test('a tool button sends its data-args to its route', async () => {
    const sent = await click('#product-ratings', 1);
    assert.deepEqual(sent, [{ source: 'woocommerce-ratings', stage: '1', url: '/wp-json/site-reviews/v1/reviews/import' }])
});

// views/pages/tools/index.php with the System Info tab first, which makes it the tab of the address.
const TABS = `
<div class="wrap">
    <hr class="wp-header-end">
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <a class="glsr-nav-tab nav-tab" data-id="system-info" href="?page=glsr-tools&tab=system-info">System Info</a>
        <a class="glsr-nav-tab nav-tab" data-id="general" href="?page=glsr-tools&tab=general">General</a>
    </nav>
    <div class="glsr-nav-view ui-tabs-hide" id="system-info">
        <form><textarea>Loading, please wait...</textarea><button disabled type="submit">Download System Info</button></form>
    </div>
    <div class="glsr-nav-view ui-tabs-hide" id="general"></div>
    <input type="hidden" name="_active_tab">
</div>`;

// jsdom has no ResizeObserver, which the tabs need; the fetch answers the System Info route
const TABS_BEFORE = `
document.body.className = 'site-review_page_glsr-tools';
window.ResizeObserver = class { observe () {} };
window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => ({ system_info: '[PLUGIN]' }), ok: true, status: 200, statusText: 'OK' });`;

test('the System Info tab loads its report when the page opens on it', async () => {
    const { window } = loadAdmin({ before: TABS_BEFORE, html: TABS });
    const view = window.document.querySelector('#system-info');
    for (let i = 0; i < 100 && view.querySelector('button').disabled; i++) {
        await new Promise(resolve => setTimeout(resolve, 10))
    }
    assert.equal(view.querySelector('textarea').value, '[PLUGIN]')
    assert.equal(view.querySelector('button').disabled, false)
});
