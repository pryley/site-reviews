/**
 * The Settings page: the tabs, a dependent field, a textarea's tag select and reset button, the
 * Strings tab's search and rows, and the License Key field's show/hide toggle, as a user sees them.
 * Nothing is saved.
 *
 * Needs wp-env running. Run: npm run test:browser -- settings
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const SETTINGS = adminUrl('edit.php?post_type=site-review&page=glsr-settings');
const tab = id => page.locator(`a.glsr-nav-tab[data-id="${id}"]`);
const radio = (field, value) => page.locator(`input[type="radio"][name$="[${field}]"][value="${value}"]`);
const rowOf = field => page.locator('.glsr-setting-field').filter({ has: page.locator(`[name$="[${field}]"]`) }).first();

try {
    await page.goto(`${SETTINGS}&tab=general`);

    // the tabs
    assert(await tab('general').evaluate(el => el.classList.contains('nav-tab-active')), 'General is the active tab');
    await tab('reviews').click();
    assert(await tab('reviews').evaluate(el => el.classList.contains('nav-tab-active')), 'clicking Reviews makes it the active tab');
    assert(await page.locator('#reviews').isVisible() && await page.locator('#general').isHidden(), 'and shows its view in place of General\'s');
    assert(page.url().includes('tab=reviews'), 'the address carries the tab');
    await page.reload();
    assert(await tab('reviews').evaluate(el => el.classList.contains('nav-tab-active')), 'the tab survives a reload');
    await tab('general').click();

    // a dependent field
    const message = rowOf('request_verification_message');
    const wasYes = await radio('request_verification', 'yes').isChecked();
    await radio('request_verification', 'no').check();
    assert(await message.evaluate(el => el.classList.contains('hidden')), 'the verification template hides while Request Verification is No');
    await radio('request_verification', 'yes').check();
    assert(!await message.evaluate(el => el.classList.contains('hidden')), 'and shows when it is Yes');

    // the tag select
    const editor = message.locator('.glsr-textarea-editor');
    const textarea = editor.locator('textarea');
    const inserter = editor.locator('.glsr-textarea-editor__toolbar select');
    const tag = await inserter.locator('option').nth(1).getAttribute('value');
    const before = await textarea.inputValue();
    await textarea.click();
    await textarea.evaluate(el => el.setSelectionRange(5, 5));
    await inserter.focus(); // the textarea loses the focus as it does on a click
    await inserter.selectOption(tag);
    equal(await textarea.inputValue(), `${before.slice(0, 5)}{${tag}}${before.slice(5)}`, `the tag select inserts {${tag}} at the cursor`);
    equal(await inserter.inputValue(), '', 'and shows its first option again');
    assert(await textarea.evaluate(el => el === document.activeElement), 'and the textarea has the focus');
    await inserter.selectOption(tag);
    assert((await textarea.inputValue()).includes(`{${tag}}{${tag}}`), 'the same tag inserts a second time');

    // a reset button
    const reset = page.locator('#general .glsr-setting-field:not(.hidden) button.is-reset-button').first();
    if (await reset.count()) {
        const target = reset.locator('xpath=ancestor::div[contains(concat(" ", @class, " "), " glsr-textarea-editor ")]').locator('textarea');
        await target.fill('changed by the browser suite');
        await reset.click();
        equal(await target.inputValue(), await reset.getAttribute('data-default'), 'a reset button restores the textarea\'s default');
        if ('chromium' === ENGINE) { // Playwright's WebKit undoes the text typed before the reset in the same step
            await page.keyboard.press('ControlOrMeta+z');
            equal(await target.inputValue(), 'changed by the browser suite', 'and undo brings the changed text back');
        }
    } else {
        info('no reset button in a visible row of the General tab');
    }
    if (!wasYes) {
        await radio('request_verification', 'no').check(); // the form is never submitted; this keeps the page as it was
    }

    // the Strings tab
    await tab('strings').click();
    const strings = page.locator('#glsr-search-translations');
    const rows = page.locator('.glsr-strings-table tbody tr');
    const rowsBefore = await rows.count();
    await strings.locator('.glsr-search-input').fill('Submit');
    await strings.locator('.glsr-search-results span').first().waitFor();
    await strings.locator('.glsr-search-results span').first().click();
    equal(await rows.count(), rowsBefore + 1, 'choosing a search result adds a row to the strings table');
    equal(await strings.locator('.glsr-search-input').inputValue(), 'Submit', 'and leaves the term in the search box');
    await rows.last().locator('a.delete').dispatchEvent('click');
    await page.waitForFunction(count => document.querySelectorAll('.glsr-strings-table tbody tr').length === count, rowsBefore);
    equal(await rows.count(), rowsBefore, 'its delete link removes the row');
    await strings.locator('.glsr-search-input').fill('Submit');
    await strings.locator('.glsr-search-results span').first().waitFor();
    await strings.locator('.glsr-search-input').fill('');
    await page.waitForTimeout(500); // the search waits 200ms after the last key
    assert(await strings.locator('.glsr-search-results').isHidden(), 'emptying the search box closes its results');
    assert(await page.evaluate(() => !document.body.classList.contains('glsr-focus')), 'and the strings table is not dimmed');

    // the License Key field's show/hide toggle
    await tab('general').click();
    const license = page.locator('#glsr-premium-license');
    if (await license.locator('.wp-pwd button').count()) {
        const icon = license.locator('.wp-pwd button .dashicons');
        const hiddenBefore = await icon.evaluate(el => el.classList.contains('dashicons-hidden'));
        await license.locator('.wp-pwd button').click();
        assert(hiddenBefore !== await icon.evaluate(el => el.classList.contains('dashicons-hidden')), 'the show/hide button swaps its icon');
        // Verify draws the row again; the route is answered here with the row as it is, so no key is checked
        if (await license.locator('[data-action="verify"]').count()) {
            const html = await license.evaluate(el => el.outerHTML);
            await page.route(url => decodeURIComponent(url.href).includes('/site-reviews/v1/premium/license'), route => route.fulfill({ json: { html } }));
            await license.evaluate(el => (el.dataset.probe = 'before'));
            await license.locator('[data-action="verify"]').click();
            await page.waitForFunction(() => !document.querySelector('#glsr-premium-license').dataset.probe);
            await page.unrouteAll({ behavior: 'ignoreErrors' });
            await page.locator('#glsr-premium-license .wp-pwd button').hover();
            const tooltip = await page.locator('#glsr-premium-license .glsr-tooltip:popover-open').innerText({ timeout: 3000 }).catch(() => '');
            assert(tooltip.length > 0, 'the show/hide button has its tooltip after the row is drawn again');
        } else {
            info('the License Key row has no Verify button on this site');
        }
    } else {
        info('no License Key field on this site');
    }

    // a colour picker: core has no colour setting of its own
    info(`${await page.locator('.glsr-setting-field input.color-picker-hex').count()} colour fields on the Settings page`);

    equal(errors, [], 'the page logged no script error');
} finally {
    await browser.close();
}
done(`The Settings page behaves in ${ENGINE}`);
