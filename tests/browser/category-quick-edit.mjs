/**
 * The categories screen: quick edit fills the Priority field from the row.
 *
 * Needs wp-env running. Run: npm run test:browser -- category-quick-edit
 */
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, wpCli } from './harness.mjs';

const { assert, done, equal } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const termId = wpCli(['term', 'create', 'site-review-category', 'Probe category', '--porcelain']);
wpCli(['term', 'meta', 'update', termId, 'term_priority', '7']);

try {
    await page.goto(adminUrl('edit-tags.php?taxonomy=site-review-category&post_type=site-review'));
    const row = page.locator(`#tag-${termId}`);
    equal((await row.locator('td.term_priority').innerText()).trim(), '7', 'the row shows the priority');
    await row.hover();
    await row.locator('.editinline').click({ force: true });
    const field = page.locator(`#edit-${termId} input[name="term_priority"]`);
    await field.waitFor({ state: 'visible' });
    equal(await field.inputValue(), '7', 'quick edit fills the Priority field from the row');
    await page.locator(`#edit-${termId} .cancel`).click();
    assert(await field.isHidden(), 'Cancel closes quick edit');
    equal(errors, [], 'the page logged no script error');
} finally {
    wpCli(['term', 'delete', 'site-review-category', termId]);
    await browser.close();
}
done(`The categories screen behaves in ${ENGINE}`);
