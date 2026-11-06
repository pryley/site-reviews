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

const PAGE = `
    <div class="glsr glsr-default" id="reviews" data-glsr-modules="reviews" data-shortcode="site_reviews">${REVIEWS()}</div>
    <div class="glsr glsr-default" id="summary" data-glsr-modules="reviews" data-shortcode="site_reviews_summary"><div class="glsr-summary-wrap"><div class="glsr-summary">4.5 stars</div></div></div>
    <div class="glsr glsr-default" id="form" data-glsr-modules="form" data-shortcode="site_reviews_form" data-reviews_id="reviews" data-summary_id="summary">
        <form class="glsr-review-form glsr-form">
            <div class="glsr-field"><input type="text" name="site-reviews[title]" value="A lovely stay"></div>
            <div class="glsr-form-message"></div>
            <button type="submit">Submit</button>
        </form>
    </div>`;

// jsdom has no layout: the validation takes offsetParent as "the field is visible"
const VISIBLE = 'Object.defineProperty(HTMLElement.prototype, "offsetParent", { get () { return this.parentNode } });';

// jsdom's showModal() is missing; this one opens the dialog and counts the calls.
const SHOW_MODAL = 'window.shown = 0; HTMLDialogElement.prototype.showModal = function () { window.shown++; this.setAttribute("open", "") };';

const CURRENT = [
    'site-reviews/init',
    'site-reviews/initialized',
    'site-reviews/form/initialized',
    'site-reviews/form/submitted',
    'site-reviews/modal/closed',
    'site-reviews/modal/opened',
    'site-reviews/review/initialized',
    'site-reviews/review/paginated',
    'site-reviews/summary/updated',
];

const OLD = [
    'block:site-reviews/form',
    'block:site-reviews/review',
    'block:site-reviews/reviews',
    'block:site-reviews/summary',
    'site-reviews/excerpts/init',
    'site-reviews/form/handle',
    'site-reviews/forms/init',
    'site-reviews/loaded',
    'site-reviews/modal/close',
    'site-reviews/modal/init',
    'site-reviews/modal/open',
    'site-reviews/pagination/handle',
    'site-reviews/pagination/init',
    'site-reviews/pagination/popstate',
];

// What the pagination and the modal call, and jsdom does not have.
const BROWSER = 'window.requestAnimationFrame = (fn) => setTimeout(fn, 0); window.scroll = () => {}; window.ResizeObserver = class { disconnect () {} observe () {} unobserve () {} };';

// the page before the bootstrap has scanned it
const start = (options = {}) => loadPage({ html: PAGE, ...options, before: BROWSER + VISIBLE + SHOW_MODAL + (options.before || '') });

// the page, scanned and with its modules loaded
const load = async (options = {}) => {
    const loaded = start(options);
    await loaded.ready
    return loaded;
};

const record = (window, names = [...CURRENT, ...OLD]) => {
    const fired = [];
    names.forEach(name => window.GLSR.Event.on(name, (...args) => fired.push([name, ...args])))
    return fired;
};

const names = (fired) => fired.map(([name]) => name);

const answer = (window, json, status = 200) => {
    window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => json, ok: status < 300, status });
};

const submit = async (window, json, status = 201) => {
    answer(window, json, status)
    window.document.querySelector('form').dispatchEvent(new window.Event('submit', { cancelable: true }))
    await settle(10)
};

test('the page is set up once, and each event says what was set up', async () => {
    const { ready, window } = start({ compat: false });
    const fired = record(window);
    await ready
    assert.deepEqual(names(fired), [
        'site-reviews/init',
        'site-reviews/review/initialized',
        'site-reviews/review/initialized',
        'site-reviews/form/initialized',
        'site-reviews/initialized',
    ])
    // the reviews module sets each root up as its own: the list and the summary are two roots
    const [, list, summary, form, plugin] = fired.map(([, payload]) => payload);
    assert.equal(list.root, window.document.getElementById('reviews'))
    assert.equal(summary.root, window.document.getElementById('summary'))
    assert.deepEqual([...list.instances, ...summary.instances], [...window.GLSR.Review.instances])
    assert.equal(form.root, window.document.getElementById('form'))
    assert.deepEqual([...form.instances], [...window.GLSR.Form.instances])
    assert.deepEqual(Object.keys(plugin), ['root'])
    assert.equal(plugin.root, window.document)
});

test('a listener of site-reviews/init runs before the page is set up, and triggering it sets the page up', async () => {
    const { ready, window } = start({ compat: false });
    let formsBefore = null;
    window.GLSR.Event.on('site-reviews/init', () => (formsBefore = window.GLSR.Form?.instances.length ?? 0))
    await ready
    assert.equal(formsBefore, 0)
    assert.equal(window.GLSR.Form.instances.length, 1)
    window.document.querySelector('form').insertAdjacentHTML('afterend', '<form class="glsr-review-form"><button type="submit">Submit</button></form>')
    window.GLSR.Event.trigger('site-reviews/init')
    await settle()
    assert.equal(window.GLSR.Form.instances.length, 2)
});

test('GLSR_init(el) sets up what is inside el, and only that', async () => {
    const { ready, window } = start({ compat: false, html: PAGE.replace('data-glsr-modules="form"', '') }); // nothing names the form
    await ready
    const { document, GLSR } = window;
    assert.equal('Form' in GLSR, false)
    document.getElementById('form').dataset.glsrModules = 'form';
    const fired = record(window);
    const form = document.getElementById('form');
    await window.GLSR_init(form)
    assert.deepEqual(names(fired), ['site-reviews/form/initialized', 'site-reviews/initialized'])
    assert.equal(fired[1][1].root, form)
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(GLSR.Review.instances.length, 1) // the list; the summary root holds no review
    assert.equal(form.classList.contains('glsr-'), true) // the direction, which jsdom does not compute
});

test('a form fires one event after a submission, for either answer, before the page is updated', async () => {
    const { window } = await load({ compat: false });
    const { document, GLSR } = window;
    const form = GLSR.Form.instances[0];
    const fired = [];
    GLSR.Event.on('site-reviews/form/submitted', (payload) => {
        fired.push([Object.keys(payload).sort(), payload.form === form, payload.success, payload.response.message, document.querySelector('#reviews .glsr-hidden-text').firstChild.textContent])
    })
    await submit(window, { errors: { title: ['Too short.'] }, message: 'Please fix the errors.', success: false }, 400)
    await submit(window, { message: 'Thanks!', reviews: REVIEWS('The new review.'), success: true })
    assert.deepEqual(fired, [
        [['form', 'response', 'success'], true, false, 'Please fix the errors.', 'A long review.'],
        [['form', 'response', 'success'], true, true, 'Thanks!', 'A long review.'],
    ])
    assert.equal(document.querySelector('#reviews .glsr-hidden-text').firstChild.textContent, 'The new review.')
});

test('an accepted review announces the list and the summary that it replaced', async () => {
    const { window } = await load({ compat: false });
    const { document, GLSR } = window;
    const fired = record(window);
    await submit(window, { reviews: REVIEWS('The new review.'), success: true, summary: '<div class="glsr-summary-wrap">5 stars</div>' })
    assert.deepEqual(names(fired), ['site-reviews/form/submitted', 'site-reviews/review/initialized', 'site-reviews/summary/updated'])
    assert.equal(fired[1][1].root, document.getElementById('reviews'))
    assert.equal(fired[2][1].summary, GLSR.Summary.find(document.getElementById('summary')))
});

test('a page change fires review/paginated and then review/initialized for that list', async () => {
    const { window } = await load({ compat: false });
    const { document, GLSR } = window;
    const review = GLSR.Review.find(document.getElementById('reviews'));
    const fired = record(window);
    const response = { pagination: '<a data-page="1" href="?reviews-page=1">1</a>', reviews: '<div class="glsr-review" id="review-2"><div class="glsr-tag-value"><p class="glsr-hidden-text" data-show-more="Show more" data-trigger="expand">Page two.</p></div></div>' };
    answer(window, response)
    document.querySelector('.glsr-pagination a').dispatchEvent(new window.Event('click', { cancelable: true }))
    await settle(10)
    assert.deepEqual(names(fired), ['site-reviews/review/paginated', 'site-reviews/review/initialized'])
    assert.equal(fired[0][1].review, review)
    assert.equal(fired[0][1].response.reviews, response.reviews)
    assert.deepEqual([...fired[1][1].instances], [review])
    assert.equal(fired[1][1].root, review.el)
    // the new reviews are set up before either event fires
    assert.equal(document.querySelectorAll('#review-2 .glsr-read-more a').length, 1)
    // the browser's back button no longer has an event of Site Reviews
    window.dispatchEvent(new window.PopStateEvent('popstate', { state: null }))
    assert.deepEqual(names(fired).length, 2)
});

for (const [status, level] of [[404, 'warn'], [500, 'error']]) {
    test(`a ${status} answer to a review or a back to a page logs with console.${level}`, async () => {
        const { window } = await load({ compat: false });
        const lines = [];
        window.console.error = () => lines.push('error');
        window.console.warn = () => lines.push('warn');
        answer(window, { code: 'glsr_review_not_found', message: 'That review was not found.' }, status)
        await window.GLSR.Review.open(1)
        assert.deepEqual(lines, [level])
        window.dispatchEvent(new window.PopStateEvent('popstate', { state: { _action: 'fetch-paged-reviews', page: 2 } }))
        await settle(10)
        assert.deepEqual(lines, [level, level])
    });
}

test('a page change keeps the interactivity runtime\'s id on the history entry', async () => {
    const { window } = await load({ compat: false });
    const { document } = window;
    // WordPress 7.1 stamps the entry it loads on and reloads the page on a back to an entry without the stamp
    window.history.replaceState({ wpInteractivityId: 'runtime' }, '')
    answer(window, { pagination: '<a data-page="1" href="?reviews-page=1">1</a>', reviews: '<div class="glsr-review" id="review-2"></div>' })
    document.querySelector('.glsr-pagination a').dispatchEvent(new window.Event('click', { cancelable: true }))
    await settle(10)
    assert.equal(window.history.state.wpInteractivityId, 'runtime')
    assert.equal(window.history.state.page, '2')
    assert.equal(window.location.search, '?reviews-page=2')
});

test('a modal fires opened once the dialog is shown, and closed', async () => {
    const { window } = await load({ compat: false });
    const { GLSR } = window;
    await GLSR.load('modal')
    const fired = [];
    GLSR.Event.on('site-reviews/modal/opened', ({ event, modal }) => fired.push(['opened', window.shown, modal.id, typeof event]))
    GLSR.Event.on('site-reviews/modal/closed', ({ modal }) => fired.push(['closed', modal.id]))
    GLSR.Modal.open('glsr-modal-probe', {})
    const modal = GLSR.Modal.get('glsr-modal-probe');
    assert.deepEqual(fired, [['opened', 1, 'glsr-modal-probe', 'undefined']])
    modal._dialog.dispatchEvent(new window.Event('close'))
    assert.deepEqual(fired[1], ['closed', 'glsr-modal-probe'])
});

test('the review modal is set up only for a page that holds a trigger', async () => {
    const plain = await load({ compat: false });
    assert.equal('Modal' in plain.window.GLSR, false)

    const { window } = await load({ compat: false, html: PAGE + '<a data-glsr-trigger="glsr-modal-review" href="#">Open</a>' });
    await settle(10)
    assert.notEqual(window.GLSR.Modal.get('glsr-modal-review'), null)
});

test('without compat mode no old name fires, and a block name sets nothing up', async () => {
    const { window } = await load({ compat: false });
    await window.GLSR.load('modal')
    const fired = record(window, OLD);
    await window.GLSR_init()
    window.GLSR_init('block:site-reviews/form', window.document.getElementById('form'), {})
    await submit(window, { reviews: REVIEWS(), success: true, summary: '<div class="glsr-summary-wrap">5 stars</div>' })
    window.GLSR.Modal.open('glsr-modal-probe', {})
    // only the listener itself hears the name that GLSR_init triggered
    assert.deepEqual(names(fired), ['block:site-reviews/form'])
});

test('with compat mode each old name fires beside its replacement, with the arguments it had', async () => {
    const { ready, window } = start();
    const { document, GLSR } = window;
    const fired = record(window);
    await ready
    assert.deepEqual(names(fired), [
        'site-reviews/init',
        'site-reviews/excerpts/init',
        'site-reviews/modal/init',
        'site-reviews/pagination/init',
        'site-reviews/review/initialized',
        'site-reviews/excerpts/init',
        'site-reviews/modal/init',
        'site-reviews/pagination/init',
        'site-reviews/review/initialized',
        'site-reviews/forms/init',
        'site-reviews/form/initialized',
        'site-reviews/loaded',
        'site-reviews/initialized',
    ])
    assert.equal(fired[1][1], document.getElementById('reviews'))

    fired.length = 0;
    const formEl = document.querySelector('form');
    await submit(window, { message: 'Thanks!', reviews: REVIEWS('The new review.'), success: true })
    assert.deepEqual(names(fired), [
        'site-reviews/form/handle',
        'site-reviews/form/submitted',
        'site-reviews/excerpts/init',
        'site-reviews/modal/init',
        'site-reviews/pagination/init',
        'site-reviews/review/initialized',
    ])
    assert.equal(fired[0][1].message, 'Thanks!')
    assert.equal(fired[0][2], formEl)
    assert.equal(fired[2][1], document.getElementById('reviews'))

    fired.length = 0;
    const review = GLSR.Review.find(document.getElementById('reviews'));
    const response = { pagination: '', reviews: '<div class="glsr-review" id="review-2"></div>' };
    answer(window, response)
    document.querySelector('.glsr-pagination a').dispatchEvent(new window.Event('click', { cancelable: true }))
    await settle(10)
    // a page change never fired site-reviews/pagination/init
    assert.deepEqual(names(fired), [
        'site-reviews/pagination/handle',
        'site-reviews/review/paginated',
        'site-reviews/excerpts/init',
        'site-reviews/modal/init',
        'site-reviews/review/initialized',
    ])
    assert.equal(fired[0][1].reviews, response.reviews)
    assert.equal(fired[0][2], review.pagination)
    assert.equal(fired[2][1], review.el)
});

test('with compat mode site-reviews/modal/open still fires before the dialog is shown', async () => {
    const { window } = await load();
    const { GLSR } = window;
    await GLSR.load('modal')
    const fired = [];
    GLSR.Event.on('site-reviews/modal/open', (modal, event) => fired.push(['open', window.shown, modal.id, event]))
    GLSR.Event.on('site-reviews/modal/opened', () => fired.push(['opened', window.shown]))
    GLSR.Event.on('site-reviews/modal/close', (modal) => fired.push(['close', modal.id]))
    GLSR.Modal.open('glsr-modal-probe', {})
    const modal = GLSR.Modal.get('glsr-modal-probe');
    modal._dialog.dispatchEvent(new window.Event('close'))
    assert.deepEqual(fired, [['open', 0, 'glsr-modal-probe', undefined], ['opened', 1], ['close', 'glsr-modal-probe']])
});

test('with compat mode a block name sets the block up, and GLSR_init(el) tells the listeners of that name', async () => {
    const { ready, window } = start({ html: PAGE.replace('data-glsr-modules="form"', '') });
    await ready
    const { document, GLSR } = window;
    const form = document.getElementById('form');
    form.dataset.glsrModules = 'form';
    const fired = record(window, ['block:site-reviews/form', 'block:site-reviews/reviews']);

    window.GLSR_init('block:site-reviews/form', form, { hide: [] })
    await settle(10)
    assert.equal(GLSR.Form.instances.length, 1)
    assert.equal(GLSR.Review.instances.length, 1)
    assert.deepEqual(fired.map(([name, el]) => [name, el.id]), [['block:site-reviews/form', 'form']])

    fired.length = 0;
    await window.GLSR_init(document.getElementById('reviews'))
    assert.deepEqual(fired.map(([name, el, attributes]) => [name, el.id, JSON.stringify(attributes)]), [['block:site-reviews/reviews', 'reviews', '{}']])
});

test('with compat mode an old name that a script triggers reaches each listener once', async () => {
    const { window } = await load();
    const { GLSR } = window;
    let heard = 0;
    GLSR.Event.on('site-reviews/forms/init', () => heard++)
    window.document.querySelector('form').insertAdjacentHTML('afterend', '<form class="glsr-review-form"><button type="submit">Submit</button></form>')
    GLSR.Event.trigger('site-reviews/forms/init')
    await settle()
    assert.equal(GLSR.Form.instances.length, 2)
    assert.equal(heard, 1)
});

test('debug mode is silent on a page that only uses the current names', async () => {
    const { warnings, window } = await load({ debug: true });
    CURRENT.forEach(name => window.GLSR.Event.on(name, () => {}))
    window.GLSR.Event.on('site-reviews/images/initialized', () => {})
    window.GLSR.Event.on('my-plugin/ready', () => {})
    await window.GLSR_init()
    await submit(window, { success: true })
    assert.deepEqual(warnings, [])
});

test('debug mode names the replacement of an old name, of a removed name, and of an old name that a script triggers', async () => {
    const { warnings, window } = await load({ debug: true });
    const { GLSR } = window;
    GLSR.Event.on('site-reviews/form/handle', () => {})
    GLSR.Event.on('site-reviews/form/handle', () => {})
    GLSR.Event.once('site-reviews/loaded', () => {})
    GLSR.Event.on('site-reviews/pagination/popstate', () => {})
    GLSR.Event.trigger('site-reviews/forms/init')
    assert.deepEqual(warnings, [
        '[site-reviews] A script listens to site-reviews/form/handle, which is deprecated; use site-reviews/form/submitted instead.',
        '[site-reviews] A script listens to site-reviews/loaded, which is deprecated; use site-reviews/initialized instead.',
        "[site-reviews] A script listens to site-reviews/pagination/popstate, which Site Reviews 9.0.0 removed; use window.addEventListener('popstate', …) instead.",
        '[site-reviews] Triggering site-reviews/forms/init is deprecated; use GLSR.Form.init(root) instead.',
    ])
});

test('debug mode says that nothing fires an old name when compat mode is off', async () => {
    const { warnings, window } = await load({ compat: false, debug: true });
    window.GLSR.Event.on('site-reviews/form/handle', () => {})
    assert.deepEqual(warnings, ['[site-reviews] A script listens to site-reviews/form/handle, which is deprecated; use site-reviews/form/submitted instead. Compat mode is off, so nothing fires it.'])
});

test('debug mode lists the events of a module when a script listens to a name that nothing fires', async () => {
    const { warnings, window } = await load({ debug: true });
    const { GLSR } = window;
    GLSR.Event.on('site-reviews/form/submited', () => {})
    GLSR.Event.on('site-reviews/initialised', () => {})
    GLSR.Event.on('site-reviews/filters/applied', () => {}) // a feature's name is not checked
    assert.deepEqual(warnings, [
        '[site-reviews] A script listens to site-reviews/form/submited, which nothing fires. The events of GLSR.Form are site-reviews/form/initialized, site-reviews/form/submitted.',
        '[site-reviews] A script listens to site-reviews/initialised, which nothing fires. The events of Site Reviews are site-reviews/init, site-reviews/initialized.',
    ])
});

test('without debug mode a misspelt name is not reported', async () => {
    const { warnings, window } = await load();
    window.GLSR.Event.on('site-reviews/form/submited', () => {})
    assert.deepEqual(warnings, [])
});
