/**
 * The notices of the plugin's screens: the banner in the page header, as a user sees it.
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
    equal(errors, [], 'the page logged no script error');
} finally {
    if (null !== dismissed) {
        wpCli(['eval', `update_user_meta(1, '${META}', json_decode(base64_decode('${Buffer.from(dismissed).toString('base64')}'), true));`]);
    }
    await browser.close();
}
done(`The notices behave in ${ENGINE}`);
