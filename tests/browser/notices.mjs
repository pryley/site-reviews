/**
 * The notices of the plugin's screens: the banner in the page header, and the popup beside the
 * message of WordPress, as a user sees them.
 *
 * Needs wp-env running. Run: npm run test:browser -- notices
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, wpCli } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const META = '_glsr_dismissed_notices'; // AbstractNotice::USER_META_KEY

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
let dismissed = null;

try {
    // nothing is dismissed, so every notice that is due shows
    dismissed = wpCli(['eval', `echo json_encode(get_user_meta(1, '${META}', true));`]);
    wpCli(['user', 'meta', 'delete', '1', META]);

    // the banner
    await page.goto(adminUrl('edit.php?post_type=site-review'));
    const banner = page.locator('#glsr-page-header > .glsr-notice-banner');
    if (await banner.count()) {
        await banner.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        assert(await banner.isVisible(), 'the banner shows at the top of the page header');
    } else {
        info('no banner is due on this site (a license is saved and valid)');
    }
    // the premium notice's install link, which only a site with a key that includes premium shows
    const install = page.locator('.glsr-notice-banner a[data-glsr-premium-install]');
    if (await install.count()) {
        let asked = false;
        await page.route(url => decodeURIComponent(url.href).includes('/site-reviews/v1/premium/connect'), route => {
            asked = true; // answered here, so the store is never reached
            return route.fulfill({ json: { code: 'glsr_probe', message: 'A probe refusal.' }, status: 403 });
        });
        await Promise.all([page.waitForNavigation(), install.click()]);
        assert(asked, 'the install link of the premium notice asks the store to connect');
        assert(page.url().includes('page=glsr-settings'), 'and opens Settings when the route refuses');
        await page.unrouteAll({ behavior: 'ignoreErrors' });
    } else {
        info('no premium notice on this site (no saved key includes Site Reviews Premium)');
    }

    // the popup, on a screen that prints WordPress's own message
    await page.goto(adminUrl('edit-tags.php?taxonomy=site-review-category&post_type=site-review&message=3'));
    if (await page.locator('.glsr-notice-popup').count()) {
        equal((await page.locator('#message').innerText().catch(() => 'no message')).trim().split('\n')[0], 'Item updated.', 'a popup leaves the message of WordPress on the categories screen');
    } else {
        info('no popup is due on this site');
    }
    equal(errors, [], 'the page logged no script error');
} finally {
    if (null !== dismissed) {
        wpCli(['eval', `update_user_meta(1, '${META}', json_decode(base64_decode('${Buffer.from(dismissed).toString('base64')}'), true));`]);
    }
    await browser.close();
}
done(`The notices behave in ${ENGINE}`);
