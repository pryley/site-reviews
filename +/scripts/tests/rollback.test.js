const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// Values that cross the jsdom realm are compared as JSON.
const plain = (value) => JSON.parse(JSON.stringify(value));

// The markup of views/pages/tools/general/rollback-plugin.php that the module reads.
const HTML = `
<div id="wpwrap">
    <hr class="wp-header-end">
    <form id="rollback-plugin">
        <select id="rollback_version" name="version">
            <option value="8.4.0" data-stops="[]">Site Reviews 8.4.0</option>
            <option value="7.9.0" data-stops="[&quot;Test Addon&quot;]">Site Reviews 7.9.0</option>
            <option value="7.0.0" data-stops="[&quot;Site Reviews Premium&quot;,&quot;Test Addon&quot;]" data-premium="1">Site Reviews 7.0.0</option>
            <option value="6.0.0" data-stops="[&quot;&lt;img src=x&gt;&quot;]">Site Reviews 6.0.0</option>
        </select>
        <button type="submit" data-loading="Rolling back to %s, please wait...">Rollback Plugin</button>
    </form>
    <div id="rollback-dialog" class="notification-dialog-wrap hidden">
        <div class="notification-dialog-background"></div>
        <div class="notification-dialog" role="dialog" aria-modal="true" aria-labelledby="rollback-dialog-title">
            <div class="glsr-rollback-dialog">
                <h1 id="rollback-dialog-title">Roll back Site Reviews to version <span data-version></span>?</h1>
                <p>You are rolling back from version 8.4.0. The plugin files will be replaced with version <span data-version></span> from wordpress.org.</p>
                <div class="notice notice-warning inline hidden" data-warning>
                    <p><strong>These plugins will stop working until Site Reviews is updated again:</strong></p>
                    <ul class="ul-disc"></ul>
                    <p class="hidden" data-premium>The addons that Site Reviews Premium deactivated stay inactive until you activate them yourself.</p>
                </div>
                <p>
                    <button type="button" class="button" data-cancel>Cancel and go back</button>
                    <button type="button" class="button button-primary" data-confirm>Rollback Plugin</button>
                </p>
            </div>
        </div>
    </div>
</div>`;

// the Tools page's body class, and a fetch that keeps each request and answers from `responses`
const BEFORE = `
document.body.className = 'site-review_page_glsr-tools';
window.sent = [];
window.responses = [];
window.fetch = async (url, options = {}) => {
    window.sent.push({ url: String(url), options });
    const { json = {}, status = 200 } = window.responses.shift() || {};
    return { headers: { get: () => 'application/json' }, json: async () => json, ok: status < 300, status, statusText: 'Status' };
};`;

const page = (version = '8.4.0') => {
    const { window } = loadAdmin({ before: BEFORE, html: HTML });
    const { document } = window;
    const dialog = document.querySelector('#rollback-dialog');
    const form = document.querySelector('#rollback-plugin');
    form.version = form.elements.namedItem('version'); // browsers give a form its fields as properties, jsdom does not
    form.version.value = version;
    return {
        cancel: dialog.querySelector('[data-cancel]'),
        confirm: dialog.querySelector('[data-confirm]'),
        dialog,
        document,
        form,
        key: (key, target, shiftKey = false) => {
            const event = new window.KeyboardEvent('keydown', { bubbles: true, cancelable: true, key, shiftKey });
            target.dispatchEvent(event)
            return event;
        },
        names: () => [...dialog.querySelectorAll('[data-warning] li')].map(item => item.textContent),
        settle: () => new Promise(resolve => setTimeout(resolve, 10)),
        submit: () => form.dispatchEvent(new window.Event('submit', { bubbles: true, cancelable: true })),
        submitButton: form.querySelector('button'),
        warning: dialog.querySelector('[data-warning]'),
        window,
    };
};

test('the Rollback Plugin button opens the popup and sends nothing', () => {
    const { cancel, dialog, document, submit, window } = page('7.9.0');
    submit()
    assert.equal(dialog.classList.contains('hidden'), false)
    assert.equal(dialog.parentNode, document.body)
    assert.equal(document.body.classList.contains('modal-open'), true)
    assert.equal(document.querySelector('#wpwrap').getAttribute('aria-hidden'), 'true')
    assert.equal(document.activeElement, cancel)
    assert.deepEqual([...dialog.querySelectorAll('[data-version]')].map(el => el.textContent), ['7.9.0', '7.9.0'])
    assert.equal(window.sent.length, 0)
});

['the Cancel button', 'a click on the background', 'the Escape key'].forEach((name, index) => {
    test(`${name} closes the popup and sends nothing`, () => {
        const { cancel, dialog, document, key, submit, submitButton, window } = page();
        submit()
        const actions = [
            () => cancel.click(),
            () => dialog.querySelector('.notification-dialog-background').click(),
            () => key('Escape', document.activeElement),
        ];
        actions[index]()
        assert.equal(dialog.classList.contains('hidden'), true)
        assert.equal(document.body.classList.contains('modal-open'), false)
        assert.equal(document.querySelector('#wpwrap').hasAttribute('aria-hidden'), false)
        assert.equal(document.activeElement, submitButton)
        assert.equal(submitButton.disabled, false)
        assert.equal(window.sent.length, 0)
    });
});

test('the Escape key does nothing after the popup is closed', () => {
    const { cancel, document, form, key, submit } = page();
    submit()
    cancel.click()
    form.version.focus()
    key('Escape', document.body)
    assert.equal(document.activeElement, form.version)
});

test('the confirm button closes the popup, stores the version through the route and posts the update request WordPress answered with', async () => {
    const { confirm, dialog, document, form, settle, submit, submitButton, window } = page('7.9.0');
    const update = { _ajax_nonce: 'updates-nonce', action: 'update-plugin', plugin: 'site-reviews/site-reviews.php', slug: 'site-reviews' };
    window.responses.push({ json: { data: update, url: 'https://example.org/wp-admin/edit.php?page=glsr-welcome' } })
    window.responses.push({ status: 500, json: { success: false, data: { errorMessage: 'Update failed.' } } }) // so the page stays
    submit()
    confirm.click()
    assert.equal(dialog.classList.contains('hidden'), true)
    assert.equal(document.body.classList.contains('modal-open'), false)
    assert.equal(submitButton.disabled, true)
    assert.equal(submitButton.getAttribute('aria-busy'), 'true')
    assert.equal(submitButton.textContent, 'Rolling back to v7.9.0, please wait...')
    // The disabled button cannot hold the focus.
    assert.equal(document.activeElement, form.version)
    await settle()
    assert.equal(window.sent.length, 2)
    assert.equal(window.sent[0].url, 'https://example.org/wp-json/site-reviews/v1/plugin/rollback')
    assert.equal(window.sent[0].options.method, 'POST')
    assert.deepEqual(JSON.parse(window.sent[0].options.body), { version: '7.9.0' })
    assert.equal(window.sent[1].url, 'https://example.org/wp-admin/admin-ajax.php')
    assert.deepEqual(plain(Object.fromEntries(window.sent[1].options.body)), update)
    assert.equal(submitButton.disabled, false) // the update failed, so the button is back
    assert.match(document.querySelector('#glsr-notices')?.textContent || '', /Rollback failed: Update failed\./)
});

test('the popup lists the plugins that the version stops', () => {
    const { cancel, dialog, form, names, submit, warning } = page();
    const premium = dialog.querySelector('[data-premium]');
    submit()
    assert.equal(warning.classList.contains('hidden'), true)
    assert.deepEqual(names(), [])
    cancel.click()

    form.version.value = '7.9.0';
    submit()
    assert.equal(warning.classList.contains('hidden'), false)
    assert.deepEqual(names(), ['Test Addon'])
    assert.equal(premium.classList.contains('hidden'), true)
    cancel.click()

    form.version.value = '7.0.0';
    submit()
    assert.deepEqual(names(), ['Site Reviews Premium', 'Test Addon'])
    assert.equal(premium.classList.contains('hidden'), false)
    cancel.click()

    // The list of the earlier version does not stay.
    form.version.value = '8.4.0';
    submit()
    assert.equal(warning.classList.contains('hidden'), true)
    assert.deepEqual(names(), [])
});

test('a plugin name is written as text', () => {
    const { dialog, names, submit } = page('6.0.0');
    submit()
    assert.deepEqual(names(), ['<img src=x>'])
    assert.equal(dialog.querySelector('img'), null)
});

test('the Tab key moves the focus between the buttons of the popup', () => {
    const { cancel, confirm, document, key, submit } = page();
    submit()
    assert.equal(key('Tab', cancel).defaultPrevented, true)
    assert.equal(document.activeElement, confirm)
    key('Tab', confirm)
    assert.equal(document.activeElement, cancel)

    key('Tab', cancel, true)
    assert.equal(document.activeElement, confirm)
    key('Tab', confirm, true)
    assert.equal(document.activeElement, cancel)

    // A click on the text of the popup leaves the focus on the body.
    cancel.blur()
    key('Tab', document.body)
    assert.equal(document.activeElement, cancel)
    cancel.blur()
    key('Tab', document.body, true)
    assert.equal(document.activeElement, confirm)
});

test('the Tab key is left alone after the popup is closed', () => {
    const { cancel, form, key, submit } = page();
    submit()
    cancel.click()
    assert.equal(key('Tab', form.version).defaultPrevented, false)
});
