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
