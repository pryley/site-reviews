const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// views/partials/settings/premium-license.php with the key field of Secret.php.
const ROW = `
<div id="glsr-premium-license" class="glsr-premium-license" data-state="1">
    <table><tbody><tr class="glsr-setting-field">
        <td>
            <span class="wp-pwd">
                <input type="password" value="a-key">
                <button type="button" class="button wp-hide-pw" data-hide="Hide value" data-show="Show value" data-tippy-content="Show value"><span class="dashicons dashicons-visibility"></span></button>
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
    const { window } = loadAdmin({ before: BEFORE, html: ROW });
    window.row = ROW;
    const toggle = () => window.document.querySelector('#glsr-premium-license .wp-pwd button');
    const first = toggle();
    assert.ok(first._tippy)
    window.document.querySelector('[data-action="verify"]').click()
    await new Promise(resolve => setTimeout(resolve, 50));
    assert.notEqual(toggle(), first)
    assert.ok(toggle()._tippy)
});
