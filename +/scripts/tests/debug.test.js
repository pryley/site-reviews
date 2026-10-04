const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin, loadPublic } = require('./harness.js');

const DEBUG_URL = 'https://example.org/wp-content/plugins/site-reviews/assets/scripts/site-reviews-debug.js?ver=8.4.0';

const inserted = (window) => [...window.document.head.querySelectorAll('script[src]')].map(script => script.src);

test('without the debug script nothing is logged, added or kept', () => {
    const { infos, read, warnings, window } = loadPublic();
    window.GLSR.Utils
    assert.equal('listeners' in window.GLSR.Event, false)
    assert.equal('defined' in window.GLSR.registry, false)
    // a debug script that arrives unannounced has nothing to replay
    window.eval(read('site-reviews-debug.js'))
    assert.deepEqual([infos.length, warnings.length], [0, 0])
});

test('the debug script adds Event.listeners() and registry.defined() to both scripts', () => {
    const defined = ({ window }) => {
        assert.equal(typeof window.GLSR.Event.listeners, 'function')
        return JSON.parse(JSON.stringify(window.GLSR.registry.defined()));
    };
    assert.deepEqual(defined(loadPublic({ debug: true })), [{ id: 'compat.public', ran: true }, { id: 'debug.public', ran: true }])
    assert.deepEqual(defined(loadAdmin({ debug: true })), [{ id: 'compat.admin', ran: true }, { id: 'debug.admin', ran: true }])
});

test('?glsr-debug loads the debug script from the address that PHP printed, and replays what happened meanwhile', () => {
    const { read, warnings, window } = loadPublic({
        config: { debug: { url: DEBUG_URL } },
        url: 'https://example.org/reviews/?glsr-debug',
    });
    assert.deepEqual(inserted(window), [DEBUG_URL])
    window.GLSR.Utils
    assert.equal(warnings.length, 0)
    window.eval(read('site-reviews-debug.js'))
    assert.equal(warnings.length, 1)
    assert.match(warnings[0], /GLSR\.Utils is deprecated/)
});

test('only the presence of ?glsr-debug is read, never its value', () => {
    const { window } = loadPublic({
        config: { debug: { url: DEBUG_URL } },
        url: 'https://example.org/?glsr-debug=https://evil.example/debug.js',
    });
    assert.deepEqual(inserted(window), [DEBUG_URL])
});

test('?glsr-debug does nothing when PHP printed no address, or without the parameter', () => {
    assert.deepEqual(inserted(loadPublic({ url: 'https://example.org/?glsr-debug' }).window), [])
    assert.deepEqual(inserted(loadPublic({ config: { debug: { url: DEBUG_URL } }, url: 'https://example.org/?debug' }).window), [])
});

test('the admin script does not read ?glsr-debug', () => {
    const { window } = loadAdmin({ config: { debug: { url: DEBUG_URL } } });
    assert.deepEqual(inserted(window), [])
});

test('at most fifty reports are kept while the debug script loads', async () => {
    const { infos, read, window } = loadPublic({
        config: { debug: { url: DEBUG_URL } },
        url: 'https://example.org/?glsr-debug',
    });
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => ({}), ok: true, status: 200 });
    for (let i = 0; i < 60; i++) {
        await window.GLSR.Request.send({ path: 'render/reviews' })
    }
    window.eval(read('site-reviews-debug.js'))
    assert.equal(infos.length, 50)
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(infos.length, 51)
});

test('the debug scripts send nothing, store nothing and change nothing on the page', () => {
    const { read } = loadPublic();
    for (const file of ['site-reviews-debug.js', 'site-reviews-admin-debug.js']) {
        const source = read(file);
        for (const forbidden of ['fetch(', 'XMLHttpRequest', 'sendBeacon', 'localStorage', 'sessionStorage', 'indexedDB', 'cookie', 'innerHTML', 'createElement', 'appendChild', 'insertAdjacent', 'classList', 'setAttribute', 'location']) {
            assert.equal(source.includes(forbidden), false, `${file} contains ${forbidden}`)
        }
    }
});
