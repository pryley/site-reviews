const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin, loadPublic } = require('./harness.js');

const wait = (ms = 25) => new Promise(resolve => setTimeout(resolve, ms));

test('a factory defined before the page is ready runs once, when the registry boots', async () => {
    const { window } = loadPublic({ before: 'window.ran = [];' });
    window.GLSR.registry.define('one', () => window.ran.push('one'))
    assert.deepEqual([...window.ran], [])
    await wait()
    window.GLSR.registry.boot()
    assert.deepEqual([...window.ran], ['one'])
});

test('a factory defined after the registry has booted runs at once', async () => {
    const { window } = loadPublic({ before: 'window.ran = [];' });
    await wait()
    window.GLSR.registry.define('late', () => window.ran.push('late'))
    assert.deepEqual([...window.ran], ['late'])
});

test('the first definition of an id wins', async () => {
    const { window } = loadPublic({ before: 'window.ran = [];' });
    window.GLSR.registry.define('one', () => window.ran.push('first'))
    window.GLSR.registry.define('one', () => window.ran.push('second'))
    await wait()
    assert.deepEqual([...window.ran], ['first'])
});

test('a factory that throws does not stop the next one', async () => {
    const { window } = loadPublic({ before: 'window.ran = []; console.error = () => {};' });
    window.GLSR.registry.define('broken', () => { throw new Error('broken') })
    window.GLSR.registry.define('fine', () => window.ran.push('fine'))
    await wait()
    assert.deepEqual([...window.ran], ['fine'])
});

test('load() resolves at once for a defined id and rejects for an id that has no url', async () => {
    const { window } = loadPublic();
    window.GLSR.registry.define('one', () => {})
    await window.GLSR.registry.load('one')
    await assert.rejects(window.GLSR.registry.load('unknown'), { message: 'unknown' })
});

test('load() inserts the registered script once and resolves when it has defined its id', async () => {
    const { window } = loadPublic();
    window.GLSR.registry.register({ 'themes.swiper': 'https://example.org/swiper.js' })
    const first = window.GLSR.registry.load('themes.swiper');
    const second = window.GLSR.registry.load('themes.swiper');
    const scripts = window.document.head.querySelectorAll('script[src="https://example.org/swiper.js"]');
    assert.equal(scripts.length, 1)
    window.GLSR.registry.define('themes.swiper', () => {})
    scripts[0].onload()
    await first
    await second
});

test('load() rejects when the script fails to load, and can be tried again', async () => {
    const { window } = loadPublic();
    window.GLSR.registry.register({ gone: 'https://example.org/gone.js' })
    const failed = window.GLSR.registry.load('gone');
    window.document.head.querySelector('script[src="https://example.org/gone.js"]').onerror()
    await assert.rejects(failed, { message: 'gone' })
    window.GLSR.registry.load('gone').catch(() => {})
    assert.equal(window.document.head.querySelectorAll('script[src="https://example.org/gone.js"]').length, 2)
});

test('the first registration of a library wins, and an entry cannot be replaced', () => {
    for (const { window } of [loadPublic(), loadAdmin()]) {
        const first = {};
        window.GLSR.lib.register('swiper', first)
        window.GLSR.lib.register('swiper', {})
        assert.equal(window.GLSR.lib.swiper, first)
        window.GLSR.lib.swiper = {};
        assert.equal(window.GLSR.lib.swiper, first)
        assert.deepEqual(Object.keys(window.GLSR.lib).filter(name => 'tippy' !== name), ['swiper']) // the admin script registers tippy
        assert.equal(typeof window.GLSR.registry.define, 'function')
    }
});
