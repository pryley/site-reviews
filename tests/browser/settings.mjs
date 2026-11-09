/**
 * The Settings page: the tabs, a dependent field, the template-editor tags, a reset button, the
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

    // the template-editor tags
    const editor = message.locator('.glsr-template-editor');
    const textarea = editor.locator('textarea');
    const tagButton = editor.locator('input[data-tag]').first();
    const tag = await tagButton.getAttribute('data-tag');
    const before = await textarea.inputValue();
    await textarea.click();
    await textarea.evaluate(el => el.setSelectionRange(0, 0));
    await tagButton.click();
    assert((await textarea.inputValue()).startsWith(`{${tag}}`), `a tag button inserts {${tag}} at the cursor`);
    assert((await textarea.inputValue()).length === before.length + tag.length + 2, 'and nothing else changes');

    // a reset button
    const reset = page.locator('#general .glsr-setting-field:not(.hidden) button.is-reset-button').first();
    if (await reset.count()) {
        const target = reset.locator('xpath=preceding-sibling::textarea[1]');
        await target.fill('changed by the browser suite');
        await reset.click();
        equal(await target.inputValue(), await reset.getAttribute('data-default'), 'a reset button restores the textarea\'s default');
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
