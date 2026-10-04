const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadPublic } = require('./harness.js');

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
    <div class="glsr glsr-default" id="reviews" data-shortcode="site_reviews">${REVIEWS()}</div>
    <div class="glsr glsr-default" id="summary" data-shortcode="site_reviews_summary">${SUMMARY()}</div>
    <div class="glsr glsr-default" id="single" data-shortcode="site_review"><div class="glsr-review" id="review-9"></div></div>
    <div class="glsr glsr-default" id="form" data-shortcode="site_reviews_form" data-reviews_id="reviews" data-summary_id="summary">
        <form class="glsr-review-form glsr-form">
            <div class="glsr-field"><input type="text" name="site-reviews[title]" value="A lovely stay"></div>
            <div class="glsr-form-message"></div>
            <button type="submit">Submit</button>
        </form>
    </div>`;

// jsdom has no layout: the validation takes offsetParent as "the field is visible"
const VISIBLE = 'Object.defineProperty(HTMLElement.prototype, "offsetParent", { get () { return this.parentNode } });';

const load = (options = {}) => {
    const loaded = loadPublic({ html: PAGE, ...options, before: VISIBLE + (options.before || '') });
    loaded.window.GLSR_init()
    return loaded;
};

// jsdom fires DOMContentLoaded after the script was evaluated, which triggers site-reviews/init
const pageLoaded = () => new Promise((resolve) => setTimeout(resolve, 25));

const submit = async (window, json, status = 201) => {
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => json, ok: status < 300, status });
    window.document.querySelector('form').dispatchEvent(new window.Event('submit', { cancelable: true }))
    await new Promise((resolve) => setTimeout(resolve, 0))
};

test('GLSR has the modules of the public script and nothing else', () => {
    const { GLSR } = load().window;
    assert.deepEqual(Object.keys(GLSR).sort(), ['Event', 'Form', 'Modal', 'Request', 'Review', 'Summary', 'Util', 'config', 'lib', 'registry', 'version'])
    assert.deepEqual(Object.keys(GLSR.Form).sort(), ['destroy', 'find', 'init', 'instances'])
    assert.deepEqual(Object.keys(GLSR.Summary).sort(), ['destroy', 'find', 'init', 'instances'])
    assert.deepEqual(Object.keys(GLSR.Review).sort(), ['destroy', 'excerpts', 'find', 'init', 'instances', 'open', 'pagination'])
    assert.deepEqual(Object.keys(GLSR.Review.pagination).sort(), ['find', 'instances'])
    assert.deepEqual(Object.keys(GLSR.Review.excerpts), ['init'])
});

test('each feature has one instance for each of its elements', () => {
    const { document, GLSR } = load().window;
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

test('a form instance has the members of the API', () => {
    const form = load().window.GLSR.Form.instances[0];
    for (const member of ['el', 'button', 'validation', 'conditions', 'captcha', 'session', 'isActive', 'init', 'destroy', 'submit']) {
        assert.equal(member in form, true, member)
    }
    assert.equal(form.isActive, true)
});

test('a list has its excerpts and its pagination; a single review has no pagination', () => {
    const { document, GLSR } = load().window;
    const list = GLSR.Review.find(document.getElementById('reviews'));
    const single = GLSR.Review.find(document.getElementById('single'));
    assert.equal(document.querySelectorAll('#reviews .glsr-read-more a').length, 1)
    assert.notEqual(list.pagination, null)
    assert.equal(single.pagination, null)
    assert.deepEqual([...GLSR.Review.pagination.instances], [list.pagination])
    assert.equal(GLSR.Review.pagination.find(document.getElementById('reviews')), list.pagination)
    assert.equal(GLSR.Review.pagination.find(document.querySelector('#reviews .glsr-pagination')), list.pagination)
});

test('init(root) sets up inserted markup and returns its instances', () => {
    const { document, GLSR } = load().window;
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

test('destroy(root) removes the instances inside root; with no root, those that left the page', () => {
    const { document, GLSR } = load().window;
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

test('with compat mode, Review.init triggers the three events it always did, once each, with the root', () => {
    const { document, GLSR } = load().window;
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

test('with compat mode, a script can still start an init by triggering one of those events', () => {
    const { document, GLSR } = load().window;
    const container = document.createElement('div');
    container.innerHTML = '<p class="glsr-hidden-text" data-show-more="Show more" data-trigger="expand">Text</p><form class="glsr-review-form"><button type="submit">Submit</button></form>';
    document.body.appendChild(container)
    GLSR.Event.trigger('site-reviews/excerpts/init', container)
    assert.equal(container.querySelectorAll('.glsr-read-more a').length, 1)
    GLSR.Event.trigger('site-reviews/forms/init')
    assert.equal(GLSR.Form.instances.length, 2)
    const pagination = GLSR.Review.pagination.instances[0];
    GLSR.Event.trigger('site-reviews/pagination/init')
    assert.notEqual(GLSR.Review.pagination.instances[0], pagination)
});

test('site-reviews/init sets up everything and ends with site-reviews/loaded', () => {
    const { document, GLSR } = loadPublic({ html: PAGE }).window;
    let loaded = 0;
    GLSR.Event.on('site-reviews/loaded', () => loaded++)
    GLSR.Event.trigger('site-reviews/init') // before GLSR_init or DOMContentLoaded: nothing listens yet
    assert.equal(GLSR.Form.instances.length, 0)
    document.defaultView.GLSR_init()
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(loaded, 1)
    assert.equal(document.getElementById('form').classList.contains('glsr-'), true) // the direction, which jsdom does not compute
});

test('Summary.update replaces the content and triggers site-reviews/summary/updated', () => {
    const { document, GLSR } = load().window;
    const summary = GLSR.Summary.find(document.getElementById('summary'));
    let updated = null;
    GLSR.Event.on('site-reviews/summary/updated', ({ summary }) => (updated = summary))
    summary.update(SUMMARY('5 stars'))
    assert.equal(document.querySelector('#summary .glsr-summary').textContent, '5 stars')
    assert.equal(updated, summary)
});

test('a submission sets up only the list and the summary it replaced, and the form', async () => {
    const { window } = load();
    const { document, GLSR } = window;
    await pageLoaded()
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
    const { window } = load();
    const { document } = window;
    await submit(window, { errors: { title: ['Too short.'] }, message: 'Please fix the errors.', success: false }, 400)
    assert.equal(document.querySelector('#reviews .glsr-hidden-text').firstChild.textContent, 'A long review.')
    assert.equal(document.querySelector('.glsr-form-message').innerHTML, 'Please fix the errors.')
    assert.equal(document.querySelector('.glsr-form-message').classList.contains('glsr-form-failed'), true)
    assert.equal(document.querySelector('input').classList.contains('glsr-is-invalid'), true)
});

test('GLSR.forms and GLSR.pagination of 8.x read the instances, and GLSR.forms can be assigned', () => {
    const { document, GLSR } = load().window;
    const container = document.createElement('div');
    container.innerHTML = '<form class="glsr-review-form"><button type="submit">Submit</button></form>';
    document.body.appendChild(container)
    const [inserted] = GLSR.Form.init(container);
    assert.equal(GLSR.forms.length, 2)
    assert.equal(GLSR.forms[0].form, GLSR.forms[0].el) // the 8.x name of the element
    assert.deepEqual([...GLSR.pagination], [...GLSR.Review.pagination.instances])
    // Review Authors 2.0.2 assigns the forms that are still on the page
    container.remove()
    GLSR.forms = GLSR.forms.filter(form => !!form.form.closest('body'));
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(inserted.isActive, false)
    assert.equal(Object.keys(GLSR).includes('forms'), false)
});

test('debug mode names a listener of site-reviews/init that no longer runs after a submission', async () => {
    const quiet = load({ debug: true });
    await submit(quiet.window, { reviews: REVIEWS(), success: true })
    assert.equal(quiet.warnings.length, 0)

    const { warnings, window } = load({ debug: true });
    window.GLSR.Event.on('site-reviews/init', () => {})
    await submit(window, { reviews: REVIEWS(), success: true })
    await submit(window, { reviews: REVIEWS(), success: true })
    assert.equal(warnings.length, 1)
    assert.match(warnings[0], /A script listens to site-reviews\/init\. Since Site Reviews 8\.4\.0 this event does not fire after a review is submitted; use site-reviews\/review\/initialized/)
});

test('debug mode logs each rule of each field, and the status of the answer', async () => {
    const { infos, window } = load({
        debug: true,
        html: PAGE.replace('value="A lovely stay"', 'value="A lovely stay" required'),
    });
    await submit(window, { success: true })
    assert.equal(infos.includes('[site-reviews] validation: site-reviews[title], required, "A lovely stay", pass'), true)
    assert.equal(infos.includes('[site-reviews] form: the submission was answered with 201'), true)
});
