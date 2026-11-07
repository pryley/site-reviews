const assert = require('node:assert/strict');
const { test } = require('node:test');
const { adminConfig, loadAdmin } = require('./harness.js');

const REMOVED = ['action', 'addons', 'addonsurl', 'ajax', 'autosize', 'keys', 'maxrating', 'minrating', 'nameprefix', 'nonce', 'notices', 'shortcode', 'shortcodes', 'stars', 'text', 'tinymce', 'Tippy', 'Utils'];

const UTIL = ['debounce', 'dom', 'fadeIn', 'fadeOut', 'feature', 'historyState', 'isEmpty', 'parseJson', 'selectText', 'throttle'];

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
    assert.equal(Object.isFrozen(window.GLSR.config.rating), true)
});

test('Util has the same ten helpers as the public script', () => {
    const { window } = loadAdmin();
    assert.deepEqual(Object.keys(window.GLSR.Util).sort(), UTIL)
});

test('each key that the 8.x admin script had is a hidden getter that gives undefined, and is gone without compat mode', () => {
    const { GLSR } = loadAdmin().window;
    for (const key of REMOVED) {
        assert.equal(GLSR[key], undefined, key)
        assert.equal(Object.getOwnPropertyDescriptor(GLSR, key).enumerable, false, key)
    }
    const off = loadAdmin({ compat: false }).window.GLSR;
    for (const key of REMOVED) {
        assert.equal(key in off, false, key)
    }
});

test('GLSR.addons holds what an 8.x filter wrote', () => {
    const { window } = loadAdmin({ added: { addons: { 'site-reviews-forms': { options: [] } } } });
    assert.deepEqual(plain(window.GLSR.addons['site-reviews-forms']), { options: [] })
});

test('the admin script has its two modules and the tippy library', () => {
    const { GLSR } = loadAdmin({ compat: false }).window;
    assert.deepEqual(Object.keys(GLSR.Notice).sort(), ['add', 'error', 'notice'])
    assert.deepEqual(Object.keys(GLSR.Rating).sort(), ['destroy', 'init', 'rebuild'])
    assert.equal(typeof GLSR.lib.tippy.tippy, 'function')
    assert.equal(typeof GLSR.lib.tippy.plugins.followCursor, 'object')
});

test('debug mode names the replacement of a removed admin key, once', () => {
    const { warnings, window } = loadAdmin({ debug: true });
    window.GLSR.stars
    window.GLSR.autosize
    window.GLSR.ajax
    window.GLSR.nonce
    window.GLSR.stars
    assert.deepEqual(warnings, [
        '[site-reviews] GLSR.stars was removed in Site Reviews 9.0.0; use GLSR.Rating instead.',
        '[site-reviews] GLSR.autosize was removed in Site Reviews 9.0.0; it has no replacement.',
        '[site-reviews] GLSR.ajax was removed in Site Reviews 9.0.0; use GLSR.Request instead.',
        '[site-reviews] GLSR.nonce was removed in Site Reviews 9.0.0; it has no replacement.',
    ])
});
