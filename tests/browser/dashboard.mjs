/**
 * The Dashboard: the Site Reviews widget renders, and today's gate loads the admin bundle here.
 *
 * Needs wp-env running. Run: npm run test:browser -- dashboard
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);

try {
    await page.goto(adminUrl('index.php'));
    assert(await page.locator('.glsr-dashboard__items .glsr-dashboard__item').count() > 0, 'the Site Reviews widget lists its items');
    assert(null !== await page.$('link#site-reviews\\/admin-css'), 'the admin stylesheet is loaded on the dashboard');
    assert(await page.evaluate(() => 'object' === typeof window.GLSR?.config), 'the admin script and its config are loaded on the dashboard');
    const gatekeeper = page.locator('.glsr-notice[data-notice="gatekeeper"]');
    if (await gatekeeper.count()) {
        await gatekeeper.locator('.notice-dismiss').click();
        await page.reload();
        assert(0 === await page.locator('.glsr-notice[data-notice="gatekeeper"]').count(), 'a dismissed Gatekeeper notice stays dismissed');
    } else {
        info('no Gatekeeper notice on this site: nothing is missing');
    }
    equal(errors, [], 'the page logged no script error');
} finally {
    await browser.close();
}
done(`The Dashboard behaves in ${ENGINE}`);
