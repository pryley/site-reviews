/**
 * The block editors: the Review Form block's preview in the post editor and the Site Editor.
 *
 * Needs wp-env running. Run: npm run test:browser -- block-editor
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, restRequest } from './harness.mjs';

const { done, equal } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
// A module the canvas cannot load is logged, not thrown.
const moduleErrors = [];
page.on('console', message => {
    if ('error' === message.type() && message.text().includes('[site-reviews]')) {
        moduleErrors.push(message.text().split('\n')[0]);
    }
});
await login(page);
const preview = async () => {
    const canvas = page.frameLocator('iframe[name="editor-canvas"]');
    await canvas.locator('.glsr-field-rating select').first().waitFor({ state: 'attached', timeout: 60000 });
    await canvas.locator('.glsr-star-rating').first().waitFor({ state: 'attached', timeout: 15000 }).catch(() => {});
    return {
        stars: await canvas.locator('.glsr-star-rating').count(),
    };
};
let post = null;

try {
    post = await restRequest(context, 'POST', '/wp/v2/pages', { content: '<!-- wp:site-reviews/form /-->', status: 'publish', title: 'Block editor probe' });

    await page.goto(adminUrl(`post.php?post=${post.id}&action=edit`));
    equal(await preview(), { stars: 1 }, 'the post editor draws the rating field of a Review Form block as stars');

    await page.goto(adminUrl(`site-editor.php?postType=page&postId=${post.id}&canvas=edit`));
    equal(await preview(), { stars: 1 }, 'and so does the Site Editor');

    equal(moduleErrors, [], 'every module the previews name loaded');
    equal(errors, [], 'the pages logged no script error');
} finally {
    if (post) {
        await restRequest(context, 'DELETE', `/wp/v2/pages/${post.id}`, { force: true }).catch(() => {});
    }
    await browser.close();
}
done(`The Review Form block previews in ${ENGINE}`);
