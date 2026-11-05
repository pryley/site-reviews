import assert from 'node:assert/strict';
import { test } from 'node:test';
import { evaluate, loadPage } from './page.mjs';

const DEBUG_URL = 'https://example.org/wp-content/plugins/site-reviews/assets/scripts/site-reviews-debug.js?ver=8.4.0';

test('at most fifty reports are kept while the debug script loads', async () => {
    const { infos, read, window } = loadPage({
        config: { debug: { url: DEBUG_URL } },
        url: 'https://example.org/?glsr-debug',
    });
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => ({}), ok: true, status: 200 });
    for (let i = 0; i < 60; i++) {
        await window.GLSR.Request.send({ path: 'render/reviews' })
    }
    evaluate(read('site-reviews-debug.js'))
    assert.equal(infos.length, 50)
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.equal(infos.length, 51)
});

test('a module reports to the debug script through the bootstrap', async () => {
    // the report state is shared: the module's copy of core and the bootstrap's are one
    const { infos, window } = loadPage({ debug: true });
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => ({}), ok: true, status: 200 });
    await window.GLSR.Request.send({ path: 'render/reviews' })
    assert.match(infos[0], /^\[site-reviews\] GET render\/reviews: REST, 200, \d+ms$/)
});
