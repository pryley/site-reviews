import assert from 'node:assert/strict';
import { test } from 'node:test';
import { loadPage, settle } from './page.mjs';

const REVIEWS = (text = 'A long review.') => `
    <div class="glsr-reviews-wrap">
        <div class="glsr-reviews">
            <div class="glsr-review" id="review-1">
                <div class="glsr-tag-value">
                    <p class="glsr-hidden-text" data-show-more="Show more" data-show-less="Show less" data-trigger="expand">${text}</p>
                </div>
            </div>
        </div>
        <div class="glsr-pagination glsr-ajax-pagination"><a data-page="2" href="?reviews-page=2">2</a></div>
    </div>`;

const SUMMARY = (text = '4.5 stars') => `<div class="glsr-summary-wrap"><div class="glsr-summary">${text}</div></div>`;

const PAGE = `
    <div class="glsr glsr-default" id="reviews" data-glsr-modules="reviews" data-shortcode="site_reviews">${REVIEWS()}</div>
    <div class="glsr glsr-default" id="summary" data-glsr-modules="reviews" data-shortcode="site_reviews_summary">${SUMMARY()}</div>
    <div class="glsr glsr-default" id="single" data-glsr-modules="reviews" data-shortcode="site_review"><div class="glsr-review" id="review-9"></div></div>
    <div class="glsr glsr-default" id="form" data-glsr-modules="form" data-shortcode="site_reviews_form" data-reviews_id="reviews" data-summary_id="summary">
        <form class="glsr-review-form glsr-form">
            <div class="glsr-field"><input type="text" name="site-reviews[title]" value="A lovely stay"></div>
            <div class="glsr-form-message"></div>
            <button type="submit">Submit</button>
        </form>
    </div>`;

// jsdom has no layout: the validation takes offsetParent as "the field is visible"
const VISIBLE = 'Object.defineProperty(HTMLElement.prototype, "offsetParent", { get () { return this.parentNode } });';

// the page, scanned and with its modules loaded
const load = async (options = {}) => {
    const loaded = loadPage({ html: PAGE, ...options, before: VISIBLE + (options.before || '') });
    await loaded.ready
    return loaded;
};

const submit = async (window, json, status = 201) => {
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => json, ok: status < 300, status });
    window.document.querySelector('form').dispatchEvent(new window.Event('submit', { cancelable: true }))
    await settle(10)
};

test('GLSR has the plumbing of the bootstrap and the modules the page named, and nothing else', async () => {
    const { GLSR } = (await load()).window;
    assert.deepEqual(Object.keys(GLSR).sort(), ['Event', 'Form', 'Request', 'Review', 'Summary', 'Util', 'config', 'lib', 'load', 'version'])
    assert.deepEqual(Object.keys(GLSR.Form).sort(), ['destroy', 'find', 'init', 'instances'])
    assert.deepEqual(Object.keys(GLSR.Summary).sort(), ['destroy', 'find', 'init', 'instances'])
    assert.deepEqual(Object.keys(GLSR.Review).sort(), ['destroy', 'excerpts', 'find', 'init', 'instances', 'open', 'pagination'])
    assert.deepEqual(Object.keys(GLSR.Review.pagination).sort(), ['find', 'instances'])
    assert.deepEqual(Object.keys(GLSR.Review.excerpts), ['init'])
    assert.deepEqual(Object.keys(GLSR.Request).sort(), ['log', 'pagedReviews', 'review', 'send', 'submit'])
});

test('each feature has one instance for each of its elements', async () => {
    const { document, GLSR } = (await load()).window;
    assert.deepEqual([...GLSR.Review.instances].map(({ el }) => el.id), ['reviews', 'single'])
    assert.deepEqual([...GLSR.Summary.instances].map(({ el }) => el.id), ['summary'])
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(GLSR.Form.instances[0].el, document.querySelector('form'))
    assert.equal(GLSR.Form.find(document.querySelector('form')), GLSR.Form.instances[0])
    assert.equal(GLSR.Review.find(document.getElementById('summary')), null)
    // a copy: the list cannot be changed from outside
    assert.equal(Object.isFrozen(GLSR.Form.instances), true)
    assert.notEqual(GLSR.Form.instances, GLSR.Form.instances)
});

test('a form instance has the members of the API', async () => {
    const form = (await load()).window.GLSR.Form.instances[0];
    for (const member of ['el', 'button', 'validation', 'conditions', 'captcha', 'session', 'isActive', 'init', 'destroy', 'submit']) {
        assert.equal(member in form, true, member)
    }
    assert.equal(form.isActive, true)
});

test('a list has its excerpts and its pagination; a single review has no pagination', async () => {
    const { document, GLSR } = (await load()).window;
    const list = GLSR.Review.find(document.getElementById('reviews'));
    const single = GLSR.Review.find(document.getElementById('single'));
    assert.equal(document.querySelectorAll('#reviews .glsr-read-more a').length, 1)
    assert.notEqual(list.pagination, null)
    assert.equal(single.pagination, null)
    assert.deepEqual([...GLSR.Review.pagination.instances], [list.pagination])
    assert.equal(GLSR.Review.pagination.find(document.getElementById('reviews')), list.pagination)
    assert.equal(GLSR.Review.pagination.find(document.querySelector('#reviews .glsr-pagination')), list.pagination)
});

test('init(root) sets up inserted markup and returns its instances', async () => {
    const { document, GLSR } = (await load()).window;
    const existing = GLSR.Form.instances[0];
    const container = document.createElement('div');
    container.innerHTML = '<form class="glsr-review-form"><button type="submit">Submit</button></form><form class="glsr-review-form"></form>';
    document.body.appendChild(container)
    const forms = GLSR.Form.init(container);
    assert.equal(forms.length, 1) // a form with no submit button is not a review form
    assert.equal(forms[0].el, container.querySelector('form'))
    assert.equal(forms[0].isActive, true)
    assert.equal(GLSR.Form.instances.length, 2)
    // an element that already has an instance keeps it
    assert.equal(GLSR.Form.init()[0], existing)
    assert.equal(GLSR.Form.instances.length, 2)
});

test('destroy(root) removes the instances inside root; with no root, those that left the page', async () => {
    const { document, GLSR } = (await load()).window;
    GLSR.Review.destroy(document.getElementById('single'))
    assert.deepEqual([...GLSR.Review.instances].map(({ el }) => el.id), ['reviews'])
    document.getElementById('reviews').remove()
    GLSR.Review.destroy()
    assert.equal(GLSR.Review.instances.length, 0)
    const form = GLSR.Form.instances[0];
    GLSR.Form.destroy(document.body)
    assert.equal(form.isActive, false)
    assert.equal(GLSR.Form.instances.length, 0)
});

test('with compat mode, Review.init triggers the three events it always did, once each, with the root', async () => {
    const { document, GLSR } = (await load()).window;
    const calls = [];
    for (const name of ['site-reviews/excerpts/init', 'site-reviews/modal/init', 'site-reviews/pagination/init', 'site-reviews/forms/init']) {
        GLSR.Event.on(name, (...args) => calls.push([name, args[0]?.id]))
    }
    GLSR.Review.init(document.getElementById('reviews'))
    assert.deepEqual(calls, [
        ['site-reviews/excerpts/init', 'reviews'],
        ['site-reviews/modal/init', undefined],
        ['site-reviews/pagination/init', undefined],
    ])
    calls.length = 0;
    GLSR.Form.init()
    assert.deepEqual(calls, [['site-reviews/forms/init', undefined]])
});

test('with compat mode, a script can still start an init by triggering one of those events', async () => {
    const { document, GLSR } = (await load()).window;
    const container = document.createElement('div');
    container.innerHTML = '<p class="glsr-hidden-text" data-show-more="Show more" data-trigger="expand">Text</p><form class="glsr-review-form"><button type="submit">Submit</button></form>';
    document.body.appendChild(container)
    GLSR.Event.trigger('site-reviews/excerpts/init', container)
    await settle() // the command waits for the module, which is on the page
    assert.equal(container.querySelectorAll('.glsr-read-more a').length, 1)
    GLSR.Event.trigger('site-reviews/forms/init')
    await settle()
    assert.equal(GLSR.Form.instances.length, 2)
    const pagination = GLSR.Review.pagination.instances[0];
    GLSR.Event.trigger('site-reviews/pagination/init')
    await settle()
    assert.notEqual(GLSR.Review.pagination.instances[0], pagination)
});

test('site-reviews/init sets up everything and ends with site-reviews/loaded', async () => {
    const { ready, window } = loadPage({ html: PAGE });
    const { document, GLSR } = window;
    let loaded = 0;
    GLSR.Event.on('site-reviews/loaded', () => loaded++)
    GLSR.Event.trigger('site-reviews/init') // before GLSR_init or DOMContentLoaded: nothing listens yet
    assert.equal('Form' in GLSR, false)
    await ready
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(loaded, 1)
    await window.GLSR_init()
    assert.equal(loaded, 2)
    assert.equal(document.getElementById('form').classList.contains('glsr-'), true) // the direction, which jsdom does not compute
});

test('Summary.update replaces the content and triggers site-reviews/summary/updated', async () => {
    const { document, GLSR } = (await load()).window;
    const summary = GLSR.Summary.find(document.getElementById('summary'));
    let updated = null;
    GLSR.Event.on('site-reviews/summary/updated', ({ summary }) => (updated = summary))
    summary.update(SUMMARY('5 stars'))
    assert.equal(document.querySelector('#summary .glsr-summary').textContent, '5 stars')
    assert.equal(updated, summary)
});

test('a submission sets up only the list and the summary it replaced, and the form', async () => {
    const { window } = await load();
    const { document, GLSR } = window;
    const events = [];
    GLSR.Event.on('site-reviews/init', () => events.push('init'))
    GLSR.Event.on('site-reviews/loaded', () => events.push('loaded'))
    GLSR.Event.on('site-reviews/form/handle', (response, formEl) => {
        // the response is handed over before the page changes
        events.push(['handle', formEl === document.querySelector('form'), document.querySelector('#reviews .glsr-hidden-text').textContent])
    })
    GLSR.Event.on('site-reviews/excerpts/init', (el) => events.push(['excerpts', el?.id]))
    GLSR.Event.on('site-reviews/summary/updated', ({ summary }) => events.push(['summary', summary.el.id]))
    const single = GLSR.Review.find(document.getElementById('single'));
    const form = GLSR.Form.instances[0];

    await submit(window, { message: 'Thanks!', reviews: REVIEWS('The new review.'), success: true, summary: SUMMARY('4.6 stars') })

    assert.deepEqual(events, [
        ['handle', true, 'A long review.Show more'],
        ['excerpts', 'reviews'],
        ['summary', 'summary'],
    ])
    assert.equal(document.querySelector('#reviews .glsr-hidden-text').firstChild.textContent, 'The new review.')
    assert.equal(document.querySelectorAll('#reviews .glsr-read-more a').length, 1)
    assert.notEqual(GLSR.Review.find(document.getElementById('reviews')).pagination, null)
    assert.equal(document.querySelector('#summary .glsr-summary').textContent, '4.6 stars')
    assert.equal(document.querySelector('.glsr-form-message').innerHTML, 'Thanks!')
    assert.equal(document.querySelector('.glsr-form-message').classList.contains('glsr-form-success'), true)
    // the other instances are the ones that were there
    assert.equal(GLSR.Review.find(document.getElementById('single')), single)
    assert.equal(GLSR.Form.instances[0], form)
    assert.equal(form.isActive, true)
});

test('a submission that fails leaves the page as it was and shows the errors', async () => {
    const { window } = await load();
    const { document } = window;
    await submit(window, { errors: { title: ['Too short.'] }, message: 'Please fix the errors.', success: false }, 400)
    assert.equal(document.querySelector('#reviews .glsr-hidden-text').firstChild.textContent, 'A long review.')
    assert.equal(document.querySelector('.glsr-form-message').innerHTML, 'Please fix the errors.')
    assert.equal(document.querySelector('.glsr-form-message').classList.contains('glsr-form-failed'), true)
    assert.equal(document.querySelector('input').classList.contains('glsr-is-invalid'), true)
});

test('an invalid form shows the messages and classes that the config names', async () => {
    const { window } = await load({
        html: `
            <div class="glsr" data-glsr-modules="form">
                <form class="glsr-review-form glsr-form">
                    <div class="glsr-field">
                        <input type="text" name="site-reviews[title]" required>
                        <div class="glsr-field-error"></div>
                    </div>
                    <div class="glsr-form-message"></div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        `,
    });
    const { document } = window;
    const form = document.querySelector('form');
    form.dispatchEvent(new window.Event('submit', { cancelable: true }))
    assert.equal(form.classList.contains('glsr-form-is-invalid'), true)
    assert.equal(document.querySelector('.glsr-field').classList.contains('glsr-field-is-invalid'), true)
    assert.equal(document.querySelector('input').classList.contains('glsr-is-invalid'), true)
    assert.equal(document.querySelector('.glsr-field-error').innerHTML, 'This field is required.')
    assert.equal(document.querySelector('.glsr-form-message').innerHTML, 'Please fix the submission errors.')
    assert.equal(document.querySelector('.glsr-form-message').classList.contains('glsr-form-failed'), true)
});

test('GLSR.Modal.modify and a modal\'s dom are kept for 8.x while compat mode is on, and gone without it', async () => {
    const { window } = await load();
    await window.GLSR.load('modal')
    let received = null;
    window.GLSR.Modal.init('glsr-modal-probe')
    window.GLSR.Modal.modify('glsr-modal-probe', (modal) => (received = modal))
    assert.notEqual(received, null)
    assert.equal(received, window.GLSR.Modal.get('glsr-modal-probe'))
    assert.deepEqual(Object.keys(received.dom).sort(), ['body', 'close', 'content', 'dialog', 'footer', 'header'])

    const plain = await load({ compat: false });
    await plain.window.GLSR.load('modal')
    assert.equal('modify' in plain.window.GLSR.Modal, false)
});

test('the form captcha is a stub that submits until the captcha module takes over', async () => {
    const { window } = await load();
    const form = window.GLSR.Form.instances[0];
    assert.equal(typeof form.captcha.execute, 'function')
    window.document.getElementById('form').dataset.glsrModules = 'form captcha';
    await window.GLSR_init(window.document.getElementById('form'))
    assert.equal(form.captcha.constructor.name !== 'Object', true)
    assert.equal(form.captcha.Form, form)
});

test('debug mode names a listener of site-reviews/init that no longer runs after a submission', async () => {
    const quiet = await load({ debug: true });
    await submit(quiet.window, { reviews: REVIEWS(), success: true })
    assert.equal(quiet.warnings.length, 0)

    const { warnings, window } = await load({ debug: true });
    window.GLSR.Event.on('site-reviews/init', () => {})
    await submit(window, { reviews: REVIEWS(), success: true })
    await submit(window, { reviews: REVIEWS(), success: true })
    assert.equal(warnings.length, 1)
    assert.match(warnings[0], /A script listens to site-reviews\/init\. Since Site Reviews 8\.4\.0 this event does not fire after a review is submitted; use site-reviews\/review\/initialized/)
});

test('debug mode logs each rule of each field, and the status of the answer', async () => {
    const { infos, window } = await load({
        debug: true,
        html: PAGE.replace('value="A lovely stay"', 'value="A lovely stay" required'),
    });
    await submit(window, { success: true })
    assert.equal(infos.includes('[site-reviews] validation: site-reviews[title], required, "A lovely stay", pass'), true)
    assert.equal(infos.includes('[site-reviews] form: the submission was answered with 201'), true)
});
