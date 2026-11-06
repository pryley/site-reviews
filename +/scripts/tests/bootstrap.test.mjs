import assert from 'node:assert/strict';
import { test } from 'node:test';
import { loadPage, settle } from './page.mjs';

const LIST = (tokens = 'reviews') => `
    <div class="glsr glsr-default" id="reviews" data-glsr-modules="${tokens}" data-shortcode="site_reviews">
        <div class="glsr-reviews-wrap"><div class="glsr-reviews"><div class="glsr-review" id="review-1"></div></div></div>
    </div>`;

const FORM = `
    <div class="glsr glsr-default" id="form" data-glsr-modules="form" data-shortcode="site_reviews_form">
        <form class="glsr-review-form glsr-form"><button type="submit">Submit</button></form>
    </div>`;

test('the bootstrap carries the plumbing and no behaviour', async () => {
    const { ready, window } = loadPage();
    const { GLSR } = window;
    assert.deepEqual(Object.keys(GLSR).sort(), ['Event', 'Request', 'Util', 'config', 'lib', 'load', 'version'])
    assert.equal(typeof GLSR.load, 'function')
    assert.equal(typeof window.GLSR_init, 'function')
    await ready
    // a page with no root loads nothing
    assert.equal('Form' in GLSR, false)
    assert.equal('Review' in GLSR, false)
    assert.equal('Modal' in GLSR, false)
});

test('a root that names a module loads it, and the module sets the root up', async () => {
    const { errors, ready, window } = loadPage({ html: LIST() });
    await ready
    const { document, GLSR } = window;
    assert.deepEqual(errors, [])
    assert.equal(typeof GLSR.Review.init, 'function')
    assert.equal(typeof GLSR.Summary.init, 'function')
    assert.equal(GLSR.Review.instances.length, 1)
    assert.equal(GLSR.Review.instances[0].el, document.getElementById('reviews'))
    assert.equal('Form' in GLSR, false) // nothing on the page named it
    assert.equal(document.getElementById('reviews').classList.contains('glsr-'), true) // the direction, which jsdom does not compute
});

test('a root that arrives later is found by the watcher', async () => {
    const { ready, window } = loadPage();
    await ready
    const { document, GLSR } = window;
    document.body.insertAdjacentHTML('beforeend', FORM)
    await settle(50)
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(GLSR.Form.instances[0].el, document.querySelector('form'))
});

test('GLSR.load() fetches a module nothing on the page named, once', async () => {
    const { ready, window } = loadPage();
    await ready
    const { GLSR } = window;
    assert.equal('Modal' in GLSR, false)
    await GLSR.load('modal')
    assert.deepEqual(Object.keys(GLSR.Modal).sort(), ['close', 'get', 'init', 'open'])
    const before = GLSR.Modal;
    await GLSR.load('modal')
    assert.equal(GLSR.Modal, before)
});

test('GLSR.Request is a shim until the request module loads, and every method returns a promise', async () => {
    const { ready, window } = loadPage();
    const { GLSR } = window;
    assert.deepEqual(Object.keys(GLSR.Request).sort(), ['log', 'pagedReviews', 'review', 'send', 'submit'])
    const shim = GLSR.Request;
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => ({ reviews: '' }), ok: true, status: 200 });
    const result = await GLSR.Request.send({ path: 'render/reviews' });
    assert.equal(result.success, true)
    assert.notEqual(GLSR.Request, shim) // the module replaced it
    await ready
});

test('a root is set up once by the watcher, and again by GLSR_init(el)', async () => {
    const { ready, window } = loadPage({ html: LIST() });
    await ready
    const { document, GLSR } = window;
    const list = GLSR.Review.find(document.getElementById('reviews'));
    let inits = 0;
    GLSR.Event.on('site-reviews/review/initialized', () => inits++)
    document.body.appendChild(document.createElement('p')) // a mutation that adds no root
    await settle(50)
    assert.equal(inits, 0)
    await window.GLSR_init(document.getElementById('reviews'))
    assert.equal(inits, 1)
    assert.equal(GLSR.Review.find(document.getElementById('reviews')), list) // the same instance, set up again
});

test('a root cached before 9.0 has no tokens and loads nothing', async () => {
    const { ready, window } = loadPage({ html: '<div class="glsr glsr-default" data-shortcode="site_reviews"><div class="glsr-reviews-wrap"></div></div>' });
    await ready
    assert.equal('Review' in window.GLSR, false)
});

test('a module that cannot be loaded is logged and the other tokens still load', async () => {
    const { errors, ready, window } = loadPage({ html: LIST('nothing reviews') });
    await ready
    assert.equal(errors.length, 1)
    assert.match(errors[0], /The module "nothing" could not be loaded/)
    assert.equal(window.GLSR.Review.instances.length, 1)
});

test('site-reviews/initialized fires once the modules the page names have set it up', async () => {
    const { ready, window } = loadPage({ html: LIST() + FORM });
    const heard = [];
    window.GLSR.Event.on('site-reviews/initialized', ({ root }) => heard.push([root === window.document, 'Form' in window.GLSR, window.GLSR.Form?.instances.length]))
    await ready
    assert.deepEqual(heard, [[true, true, 1]])
});

test('the compat script keeps the old request names on the shim, and no module name', async () => {
    const { ready, window } = loadPage();
    await ready
    const { GLSR } = window;
    assert.deepEqual(Object.keys(GLSR.ajax).sort(), ['data', 'get', 'post'])
    assert.equal(typeof GLSR.request.send, 'function')
    assert.equal(GLSR.Utils, GLSR.Util)
    assert.equal(GLSR.ajax_url, 'https://example.org/wp-admin/admin-ajax.php')
    assert.equal('forms' in GLSR, false)
    assert.equal('pagination' in GLSR, false)
    assert.equal('addons' in GLSR, false)
});

test('GLSR_init is a bare global function that binds the plugin listeners once, and resolves when the page is set up', async () => {
    const { ready, window } = loadPage();
    let loaded = 0;
    window.GLSR.Event.on('site-reviews/loaded', () => loaded++)
    assert.equal(typeof window.GLSR_init, 'function')
    await ready
    await window.GLSR_init()
    await window.GLSR_init()
    assert.equal(loaded, 3)
});

test('the bootstrap adds the stylesheets a root or a rating names that the page lacks, once', async () => {
    const html = LIST().replace('data-shortcode', 'data-glsr-styles="review star-rating flags pagination button" data-shortcode');
    const { ready, window } = loadPage({ html }); // a fragment of a page render, with no stylesheets beside it
    await ready
    const { document } = window;
    const links = [...document.head.querySelectorAll('link[id^="glsr-css-"]')].map(link => link.id);
    assert.deepEqual(links, ['glsr-css-variables', 'glsr-css-keyframes', 'glsr-css-base', 'glsr-css-loaders', 'glsr-css-review', 'glsr-css-star-rating', 'glsr-css-flags', 'glsr-css-pagination', 'glsr-css-button'])
    assert.match(document.getElementById('glsr-css-review').href, /\/assets\/styles\/base\/review\.css\?ver=/)
    // a rating on its own, arriving later with no module
    const rating = document.createElement('div');
    rating.innerHTML = '<div class="glsr-star-rating glsr-stars" data-glsr-styles="star-rating summary"></div>';
    document.body.appendChild(rating)
    await new Promise(resolve => window.setTimeout(resolve, 0))
    assert.equal(document.querySelectorAll('#glsr-css-star-rating').length, 1)
    assert.equal(document.querySelectorAll('#glsr-css-summary').length, 1)
    // a name the config has no file for is skipped
    rating.innerHTML = '<div data-glsr-styles="unknown"></div>';
    await new Promise(resolve => window.setTimeout(resolve, 0))
    assert.equal(document.getElementById('glsr-css-unknown'), null)
});
