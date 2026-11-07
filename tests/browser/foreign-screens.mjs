/**
 * The screens outside Site Reviews: another post type's editor, the posts list and the Site Editor
 * load no admin bundle, and the blocks' controls read the rating range from the public config.
 *
 * Needs wp-env running. Run: npm run test:browser -- foreign-screens
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, restRequest, wpCli } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const loaded = async () => ({
    config: await page.evaluate(() => window.GLSR?.config?.rating ?? null),
    script: await page.evaluate(() => !!document.querySelector('script[src*="scripts/admin/admin"]')),
    style: null !== await page.$('link#site-reviews\\/admin-css'),
});
let post = null;

try {
    // the block editor of a post holding a Site Reviews block
    post = await restRequest(context, 'POST', '/wp/v2/posts', { content: '<!-- wp:site-reviews/reviews /-->', status: 'draft', title: 'Foreign screen probe' });
    await page.goto(adminUrl(`post.php?post=${post.id}&action=edit`));
    await page.locator('iframe[name="editor-canvas"], .block-editor-block-list__layout').first().waitFor({ timeout: 60000 });
    const editor = await loaded();
    assert(!editor.style && !editor.script, 'a post\'s block editor loads neither the admin bundle nor its stylesheet');
    assert(editor.config && 'min' in editor.config && 'max' in editor.config, 'and the rating min and max the Site Reviews blocks read are in the public config');

    // the posts list
    await page.goto(adminUrl('edit.php'));
    const list = await loaded();
    assert(!list.style && !list.script, 'the posts list loads neither');

    // the Site Editor
    await page.goto(adminUrl('site-editor.php'));
    await page.locator('.edit-site-layout, .edit-site').first().waitFor({ timeout: 60000 });
    const site = await loaded();
    assert(!site.style && !site.script, 'the Site Editor loads neither');

    // the classic Widgets screen and the Customizer, which exist on a classic theme only
    let classic = null;
    try {
        wpCli(['theme', 'is-installed', 'twentytwentyone']); // exits non-zero when it is not
        classic = 'twentytwentyone';
    } catch {
        info('no classic theme is installed, so the Widgets screen and the Customizer are not covered: wp theme install twentytwentyone');
    }
    if (classic) {
        const active = wpCli(['theme', 'list', '--status=active', '--field=name']);
        try {
            wpCli(['theme', 'activate', classic]);
            await page.goto(adminUrl('widgets.php'));
            await page.locator('#widgets-editor, #available-widgets').first().waitFor({ timeout: 60000 }); // the block or the classic widgets editor
            assert(true, 'the Widgets screen renders on a classic theme');
            assert(null !== await page.$('link#site-reviews\\/widgets-css'), 'and loads the widgets stylesheet');
            const widgets = await loaded();
            assert(!widgets.style && !widgets.script, 'and nothing else of ours');
            await page.goto(adminUrl('customize.php'));
            await page.locator('#customize-controls').waitFor({ timeout: 60000 });
            assert(null !== await page.$('link#site-reviews\\/widgets-css'), 'the Customizer loads the widgets stylesheet');
            const customizer = await loaded();
            assert(!customizer.style && !customizer.script, 'and nothing else of ours');
        } finally {
            wpCli(['theme', 'activate', active]);
        }
    }

    equal(errors, [], 'the pages logged no script error');
} finally {
    if (post) {
        await restRequest(context, 'DELETE', `/wp/v2/posts/${post.id}`, { force: true });
    }
    await browser.close();
}
done(`The foreign screens load nothing of the admin in ${ENGINE}`);
