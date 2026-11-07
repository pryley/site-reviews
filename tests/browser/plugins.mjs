/**
 * The Plugins screen: the deactivation dialog opens from the plugin's Deactivate link, takes a
 * reason, and closes without deactivating.
 *
 * Needs wp-env running. Run: npm run test:browser -- plugins
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter } from './harness.mjs';

const { assert, done, equal } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);

try {
    await page.goto(adminUrl('plugins.php'));
    const row = page.locator('tr[data-slug="site-reviews"]');
    assert(await row.evaluate(el => el.classList.contains('active')), 'Site Reviews is active');
    await row.locator('a[data-deactivate="site-reviews"]').click();
    const overlay = page.locator('#glsr-dp-overlay');
    await overlay.locator('.glsr-dp-reasons').waitFor({ state: 'visible' });
    assert(true, 'Deactivate opens the dialog');
    const reason = overlay.locator('label.glsr-dp-reason').first();
    await reason.click();
    assert(await reason.evaluate(el => el.classList.contains('is-selected')), 'choosing a reason marks it');
    await overlay.locator('button.close').click();
    await page.waitForFunction(() => 0 === document.querySelector('#glsr-dp-overlay').children.length);
    assert(true, 'the close button removes the dialog');
    assert(await row.evaluate(el => el.classList.contains('active')), 'and the plugin is still active');
    equal(errors, [], 'the page logged no script error');
} finally {
    await browser.close();
}
done(`The Plugins screen behaves in ${ENGINE}`);
