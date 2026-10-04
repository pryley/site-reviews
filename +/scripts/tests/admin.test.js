const assert = require('node:assert/strict');
const { test } = require('node:test');
const { adminConfig, loadAdmin } = require('./harness.js');

const UTIL = ['debounce', 'dom', 'fadeIn', 'fadeOut', 'feature', 'isEmpty', 'parseJson', 'selectText', 'throttle'];

// Values that cross the jsdom realm are compared as JSON.
const plain = (value) => JSON.parse(JSON.stringify(value));

test('a listener added before the admin script runs is kept', () => {
    const { window } = loadAdmin({
        before: 'window.calls = []; GLSR.Event.on("probe", (value) => calls.push(value));',
    });
    window.GLSR.Event.trigger('probe', 7)
    assert.deepEqual(plain(window.calls), [7])
    assert.deepEqual(Object.keys(window.GLSR.Event).sort(), ['off', 'on', 'once', 'trigger'])
});

test('the config is the one PHP printed, deep-frozen', () => {
    const { window } = loadAdmin();
    assert.deepEqual(plain(window.GLSR.config), adminConfig())
    assert.equal(Object.isFrozen(window.GLSR.config.nonce), true)
    assert.equal(Object.isFrozen(window.GLSR.config.tinymce.required.site_review), true)
});

test('Util has the same nine helpers as the public script', () => {
    const { window } = loadAdmin();
    assert.deepEqual(Object.keys(window.GLSR.Util).sort(), UTIL)
});

test('each key that 8.3 printed is a hidden getter onto the config', () => {
    const { window } = loadAdmin();
    const { GLSR } = window;
    const { config } = GLSR;
    assert.equal(GLSR.Utils, GLSR.Util)
    assert.equal(GLSR.action, config.request.ajax.action)
    assert.equal(GLSR.addonsurl, config.urls.addons)
    assert.equal(GLSR.maxrating, 5)
    assert.equal(GLSR.minrating, 0)
    assert.equal(GLSR.nameprefix, config.nameprefix)
    assert.equal(GLSR.nonce, config.nonce)
    assert.equal(GLSR.shortcodes, config.tinymce.required)
    assert.equal(GLSR.tinymce, config.tinymce.plugins)
    assert.equal(GLSR.text.import_error, config.text.importError)
    assert.equal(GLSR.text.system_info_500, config.text.systemInfo500)
    assert.equal(GLSR.text.searching, 'Searching...')
    assert.equal(Object.getOwnPropertyDescriptor(GLSR, 'nonce').enumerable, false)
});

test('GLSR.addons holds what an 8.x filter wrote', () => {
    const { window } = loadAdmin({ added: { addons: { 'site-reviews-forms': { options: [] } } } });
    assert.deepEqual(plain(window.GLSR.addons['site-reviews-forms']), { options: [] })
});

test('no deprecated key is defined when the layer is off', () => {
    const { window } = loadAdmin({ compat: false });
    assert.equal('Utils' in window.GLSR, false)
    assert.equal('nonce' in window.GLSR, false)
});

test('the admin script has its three modules and the tippy library', () => {
    const { GLSR } = loadAdmin({ compat: false }).window;
    assert.deepEqual(Object.keys(GLSR.Notice).sort(), ['add', 'error', 'notice'])
    assert.deepEqual(Object.keys(GLSR.Rating).sort(), ['destroy', 'init', 'rebuild'])
    assert.deepEqual(Object.keys(GLSR.Tinymce), ['create'])
    assert.equal(typeof GLSR.lib.tippy.tippy, 'function')
    assert.equal(typeof GLSR.lib.tippy.plugins.followCursor, 'object')
    assert.equal(GLSR.Tinymce.create('content'), undefined) // before the page is ready there is no button
});

test('the keys that the admin script set are deprecated getters, and are gone without compat mode', () => {
    const OLD = ['ajax', 'autosize', 'keys', 'notices', 'shortcode', 'stars', 'Tippy'];
    const { GLSR } = loadAdmin().window;
    assert.equal(typeof GLSR.ajax, 'function') // Review Notifications 3.0.3: new GLSR.ajax(data, ev).post()
    assert.equal(GLSR.keys.ENTER, 13)
    assert.equal(GLSR.stars, GLSR.Rating)
    assert.equal(GLSR.Tippy, GLSR.lib.tippy)
    assert.equal(GLSR.autosize.update(), undefined)
    assert.equal(GLSR.shortcode, null) // the page is not ready
    for (const key of OLD) {
        assert.equal(Object.getOwnPropertyDescriptor(GLSR, key).enumerable, false, key)
    }
    const off = loadAdmin({ compat: false }).window.GLSR;
    for (const key of OLD) {
        assert.equal(key in off, false, key)
    }
});

test('debug mode names the replacement of an admin key', () => {
    const { warnings, window } = loadAdmin({ debug: true });
    window.GLSR.stars
    window.GLSR.autosize
    assert.deepEqual(warnings, [
        '[site-reviews] GLSR.stars is deprecated; use GLSR.Rating instead.',
        '[site-reviews] GLSR.autosize is deprecated; it has no replacement.',
    ])
});
