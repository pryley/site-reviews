const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// A row of the review list with its Approve action and its pin icon.
const HTML = `
<div class="wrap">
    <hr class="wp-header-end">
    <table><tbody id="the-list">
        <tr id="post-7" class="status-pending">
            <td class="column-title"><strong>Probe</strong>
                <a class="glsr-toggle-status" href="post.php?post=7&action=approve">Approve</a>
            </td>
            <td class="column-is_pinned"><i class="dashicons dashicons-sticky pin-review" data-id="7"></i></td>
        </tr>
    </tbody></table>
</div>`;

// the review list's body classes, and a fetch that answers with window.answer
const BEFORE = `
document.body.className = 'edit-php post-type-site-review';
window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => window.answer.json, ok: window.answer.status < 400, status: window.answer.status, statusText: '' });`;

const click = async (selector, answer) => {
    const { window } = loadAdmin({ before: BEFORE, html: HTML });
    window.answer = answer;
    window.scrollTo = () => {};
    window.document.querySelector(selector).click()
    await new Promise(resolve => setTimeout(resolve, 50));
    return window.document;
};

test('the pin icon shows the notices of its answer', async () => {
    const document = await click('i.pin-review', {
        json: { notices: '<div class="notice notice-success"><p>Review pinned.</p></div>', value: 1 },
        status: 200,
    });
    assert.equal(document.querySelector('i.pin-review').classList.contains('pinned'), true)
    assert.equal(document.querySelector('#glsr-notices .notice-success').textContent, 'Review pinned.')
});

test('the pin icon shows the message of a refusal', async () => {
    const document = await click('i.pin-review', {
        json: { code: 'glsr_invalid_review', message: 'Invalid review ID.' },
        status: 404,
    });
    assert.equal(document.querySelector('i.pin-review').classList.contains('pinned'), false)
    assert.equal(document.querySelector('#glsr-notices .notice-error').textContent, 'Invalid review ID.')
});

test('the Approve action shows the message of a failure', async () => {
    const document = await click('a.glsr-toggle-status', {
        json: { code: 'glsr_status_failed', message: 'The status could not be changed.' },
        status: 500,
    });
    assert.equal(document.querySelector('#post-7').classList.contains('status-pending'), true)
    assert.equal(document.querySelector('#glsr-notices .notice-error').textContent, 'The status could not be changed.')
});

// views/partials/listtable/filter.php, beside the list table's search box.
const FILTER = `
<div class="wrap">
    <input type="search" id="post-search-input">
    <div id="glsr-filter-by-author" class="glsr-filter" data-options='[{"id":"","title":"All authors"}]' data-search="search/users">
        <input type="hidden" class="glsr-filter__value" value="">
        <span class="glsr-filter__selected" tabindex="0">All authors</span>
        <div class="glsr-filter__dropdown">
            <input type="search" class="glsr-filter__search">
            <div class="glsr-filter__results"></div>
        </div>
    </div>
</div>`;

// open the filter, then press the mouse on `target`, which a browser focuses
const pressOutside = async (target) => {
    const { window } = loadAdmin({ before: BEFORE, html: FILTER });
    const { document } = window;
    const filter = document.querySelector('.glsr-filter');
    filter.querySelector('.glsr-filter__selected').dispatchEvent(new window.MouseEvent('mousedown', { bubbles: true }))
    await new Promise(resolve => setTimeout(resolve, 30));
    assert.equal(filter.classList.contains('is-active'), true)
    const el = document.querySelector(target);
    el.focus?.()
    el.dispatchEvent(new window.MouseEvent('mousedown', { bubbles: true }))
    await new Promise(resolve => setTimeout(resolve, 30));
    return { document, filter };
};

test('an open filter closes on a click in another field and leaves the focus there', async () => {
    const { document, filter } = await pressOutside('#post-search-input');
    assert.equal(filter.classList.contains('is-active'), false)
    assert.equal(document.activeElement.id, 'post-search-input')
});

test('an open filter closes on a click in the space around it and takes the focus back', async () => {
    const { document, filter } = await pressOutside('.wrap');
    assert.equal(filter.classList.contains('is-active'), false)
    assert.equal(document.activeElement.className, 'glsr-filter__selected')
});

// The review list with WordPress's Quick Edit row. WordPress binds Enter to Save on the row's cells
// when the page is ready (wp-admin/js/inline-edit-post.js), which here is 100ms after the bundle starts.
const QUICK_EDIT = `
<table class="post-type-site-review"><tbody id="the-list"></tbody></table>
<table class="post-type-site-review"><tbody><tr id="inline-edit"><td><textarea name="_response"></textarea></td></tr></tbody></table>`;

const QUICK_EDIT_BEFORE = `
document.body.className = 'edit-php post-type-site-review';
window.inlineEditPost = {};
jQuery.holdReady(true);
jQuery(function () {
    jQuery('td', '#inline-edit').on('keydown', function (e) { if (13 === e.which) { window.saved = true; } });
});
setTimeout(function () { jQuery.holdReady(false); }, 100);`;

test('Enter in the response field of Quick Edit does not save, however late the page is ready', async () => {
    const { window } = loadAdmin({ before: QUICK_EDIT_BEFORE, html: QUICK_EDIT });
    await new Promise(resolve => setTimeout(resolve, 250));
    window.jQuery('[name="_response"]').trigger(window.jQuery.Event('keydown', { which: 13 }))
    assert.equal(window.saved, undefined)
});
