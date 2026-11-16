const assert = require('node:assert/strict');
const { test } = require('node:test');
const { POPOVER, loadAdmin } = require('./harness.js');

// views/partials/settings/premium-license.php with the key field of Secret.php.
const ROW = `
<div id="glsr-premium-license" class="glsr-premium-license" data-state="1">
    <table><tbody><tr class="glsr-setting-field">
        <td>
            <span class="wp-pwd">
                <input type="password" value="a-key">
                <button type="button" class="button wp-hide-pw" data-hide="Hide value" data-show="Show value" data-tooltip="key-tooltip"><span class="dashicons dashicons-visibility"></span></button>
                <span id="key-tooltip" class="glsr-tooltip" popover="manual" role="tooltip">Show value</span>
            </span>
            <button type="button" data-action="verify">Verify License Key</button>
            <div class="glsr-premium-license__notice notice inline" hidden></div>
        </td>
    </tr></tbody></table>
</div>`;

// the Settings page's body class, and a fetch that answers Verify with the row drawn again
const BEFORE = `
document.body.className = 'site-review_page_glsr-settings';
window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => ({ html: window.row }), ok: true, status: 200, statusText: 'OK' });`;

test('the key field\'s show/hide button has its tooltip after the row is drawn again', async () => {
    const { window } = loadAdmin({ before: POPOVER + BEFORE, html: ROW });
    window.row = ROW;
    const toggle = () => window.document.querySelector('#glsr-premium-license .wp-pwd button');
    const tooltip = () => window.document.getElementById('key-tooltip');
    const focus = () => toggle().dispatchEvent(new window.Event('focus'));
    const first = toggle();
    focus()
    assert.ok('open' in tooltip().dataset)
    window.document.querySelector('[data-action="verify"]').click()
    await new Promise(resolve => setTimeout(resolve, 50));
    assert.notEqual(toggle(), first)
    assert.equal('open' in tooltip().dataset, false)
    focus()
    assert.ok('open' in tooltip().dataset)
});

test('the key field\'s show/hide button changes its name and its tooltip together', () => {
    const { window } = loadAdmin({ before: POPOVER + BEFORE, html: ROW });
    const toggle = window.document.querySelector('#glsr-premium-license .wp-pwd button');
    toggle.click()
    assert.equal(toggle.getAttribute('aria-label'), 'Hide value')
    assert.equal(window.document.getElementById('key-tooltip').textContent, 'Hide value')
});
