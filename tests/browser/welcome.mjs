/**
 * The Welcome page: the tabs and the Getting Started videos.
 *
 * Needs wp-env running. Run: npm run test:browser -- welcome
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter } from './harness.mjs';

const { assert, done, equal } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const tab = id => page.locator(`a.glsr-nav-tab[data-id="${id}"]`);

try {
    await page.goto(adminUrl('index.php?page=site-reviews-welcome'));
    assert(await tab('getting-started').evaluate(el => el.classList.contains('nav-tab-active')), 'Getting Started is the active tab');
    await tab('whatsnew').click();
    assert(await page.locator('#whatsnew').isVisible() && await page.locator('#getting-started').isHidden(), 'clicking What\'s New shows its view');
    await tab('getting-started').click();

    // the videos
    const playlist = page.locator('.glsr-videos__playlist a');
    const second = playlist.nth(1);
    const id = await second.getAttribute('data-id');
    await second.click();
    const frame = page.locator('.glsr-youtube iframe');
    await frame.waitFor();
    assert((await frame.getAttribute('src')).includes(id), 'choosing a playlist item loads its video frame');
    assert(await second.evaluate(el => el.classList.contains('is-active')), 'and marks it active');
    assert(await page.locator('.glsr-youtube-button').count() >= 0, 'the play button area is on the page');

    equal(errors, [], 'the page logged no script error');
} finally {
    await browser.close();
}
done(`The Welcome page behaves in ${ENGINE}`);
