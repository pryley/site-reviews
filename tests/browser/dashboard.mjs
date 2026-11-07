/**
 * The Dashboard: the Site Reviews widget renders with its own stylesheet, the admin bundle stays off it,
 * and a Gatekeeper notice shows and is dismissed there through the notice module built on its own.
 *
 * Needs wp-env running. Run: npm run test:browser -- dashboard
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, wpCli } from './harness.mjs';

const { assert, done, equal } = reporter();

const TRANSIENT = 'glsr_gatekeeper'; // where Gatekeeper leaves the errors the notice reads once

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);

try {
    // one untested plugin, in the shape Gatekeeper records
    wpCli(['eval', `set_transient('${TRANSIENT}', ['probe/probe.php' => ['error' => 'not_tested', 'name' => 'Probe Plugin', 'plugin_uri' => '', 'textdomain' => 'probe']], 60);`]);
    await page.goto(adminUrl('index.php'));
    assert(await page.locator('.glsr-dashboard__items .glsr-dashboard__item').count() > 0, 'the Site Reviews widget lists its items');
    assert(null !== await page.$('link#site-reviews\\/dashboard-css'), 'the widget\'s stylesheet is loaded on the dashboard');
    assert(null === await page.$('link#site-reviews\\/admin-css'), 'the admin stylesheet is not');
    assert(await page.evaluate(() => !document.querySelector('script[src*="scripts/admin/admin"]')), 'nor is the admin script');

    const gatekeeper = page.locator('.glsr-notice[data-notice="gatekeeper"]');
    assert(1 === await gatekeeper.count(), 'the Gatekeeper notice shows on the dashboard');
    assert((await gatekeeper.innerText()).includes('Probe Plugin'), 'and names the untested plugin');
    const dismissed = page.waitForResponse(response => decodeURIComponent(response.url()).includes('plugin/notices/gatekeeper'));
    await gatekeeper.locator('.notice-dismiss').click();
    equal((await dismissed).status(), 200, 'its X sends the dismiss request through the notice module');
    await gatekeeper.waitFor({ state: 'detached' });
    assert(0 === await gatekeeper.count(), 'and removes the notice');
    await page.reload();
    assert(0 === await page.locator('.glsr-notice[data-notice="gatekeeper"]').count(), 'the notice does not come back');
    equal(errors, [], 'the page logged no script error');
} finally {
    wpCli(['transient', 'delete', TRANSIENT]);
    await browser.close();
}
done(`The Dashboard behaves in ${ENGINE}`);
