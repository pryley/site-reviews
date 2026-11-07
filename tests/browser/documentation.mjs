/**
 * The Help & Support page: the tabs, a highlighted code sample and copying one as plain text.
 *
 * Needs wp-env running. Run: npm run test:browser -- documentation
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
if ('chromium' === ENGINE) {
    await context.grantPermissions(['clipboard-read', 'clipboard-write']);
}
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const DOCS = adminUrl('edit.php?post_type=site-review&page=glsr-documentation');
const tab = id => page.locator(`a.glsr-nav-tab[data-id="${id}"]`);

try {
    await page.goto(`${DOCS}&tab=shortcodes`);
    assert(await tab('shortcodes').evaluate(el => el.classList.contains('nav-tab-active')), 'Shortcodes is the active tab');
    await tab('faq').click();
    assert(await tab('faq').evaluate(el => el.classList.contains('nav-tab-active')) && await page.locator('#faq').isVisible(), 'clicking FAQ shows its view');
    await tab('shortcodes').click();

    // a highlighted code sample: the highlights are registered by category, the markup is untouched
    const sample = page.locator('#shortcodes pre code[class*="language-"]').first();
    assert(await sample.evaluate(el => 0 === el.children.length), 'a code sample holds only text');
    assert(await page.evaluate(() => CSS.highlights.get('tag')?.size > 0 && CSS.highlights.get('string')?.size > 0), 'the shortcode samples are highlighted by category');

    // copying a code sample gives plain text
    const example = page.locator('#shortcodes .shortcode-example').first();
    if (await example.count() && 'chromium' === ENGINE) {
        const holder = example.locator('xpath=ancestor::div[contains(@class,"glsr-card")][1]');
        if (await holder.count() && await holder.evaluate(el => el.classList.contains('closed'))) {
            await holder.locator('.glsr-card-heading').click();
        }
        await example.scrollIntoViewIfNeeded();
        await example.evaluate(el => {
            const range = document.createRange();
            range.selectNodeContents(el);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        });
        await page.keyboard.press(process.platform === 'darwin' ? 'Meta+C' : 'Control+C');
        const copied = await page.evaluate(() => navigator.clipboard.readText());
        const shown = (await example.innerText()).trim();
        assert(copied.trim() === shown, 'the copied text is the sample as shown, without markup');
    } else {
        info('clipboard read is checked in Chromium only');
    }

    equal(errors, [], 'the page logged no script error');
} finally {
    await browser.close();
}
done(`The Help & Support page behaves in ${ENGINE}`);
