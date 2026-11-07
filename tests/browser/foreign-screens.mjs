/**
 * The screens outside Site Reviews that today's gate loads the admin bundle on: another post type's
 * editor, the posts list and the Site Editor. The assertions pin what loads there today; the gate
 * change edits them deliberately.
 *
 * Needs wp-env running. Run: npm run test:browser -- foreign-screens
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, restRequest } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const loaded = async () => ({
    config: await page.evaluate(() => window.GLSR?.config?.rating ?? null),
    script: await page.evaluate(() => !!document.querySelector('script[src*="site-reviews-admin"]')),
    style: null !== await page.$('link#site-reviews\\/admin-css'),
});
let post = null;

try {
    // the block editor of a post holding a Site Reviews block
    post = await restRequest(context, 'POST', '/wp/v2/posts', { content: '<!-- wp:site-reviews/reviews /-->', status: 'draft', title: 'Foreign screen probe' });
    await page.goto(adminUrl(`post.php?post=${post.id}&action=edit`));
    await page.locator('iframe[name="editor-canvas"], .block-editor-block-list__layout').first().waitFor({ timeout: 60000 });
    const editor = await loaded();
    assert(editor.style && editor.script, 'a post\'s block editor loads the admin bundle and stylesheet today');
    assert(editor.config && 'min' in editor.config && 'max' in editor.config, 'and the rating min and max the Site Reviews blocks read are in its config');

    // the posts list
    await page.goto(adminUrl('edit.php'));
    const list = await loaded();
    assert(!list.style && !list.script, 'the posts list loads neither');

    // the Site Editor
    await page.goto(adminUrl('site-editor.php'));
    await page.locator('.edit-site-layout, .edit-site').first().waitFor({ timeout: 60000 });
    const site = await loaded();
    assert(site.style && site.script, 'the Site Editor loads the admin bundle and stylesheet today');

    // the classic Widgets screen needs a classic theme
    await page.goto(adminUrl('widgets.php'));
    if (await page.$('#available-widgets')) {
        assert(true, 'the classic Widgets screen renders');
    } else {
        info(`widgets.php has no widget areas here (${page.url()}): a block theme has no Widgets screen`);
    }

    equal(errors, [], 'the pages logged no script error');
} finally {
    if (post) {
        await restRequest(context, 'DELETE', `/wp/v2/posts/${post.id}`, { force: true });
    }
    await browser.close();
}
done(`The foreign screens load what they load today in ${ENGINE}`);
