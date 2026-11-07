/**
 * The review editor: the assigned posts and users searches, the content textarea's height, the
 * publish box's pinned control and the Edit Details toggle, as a user sees them.
 *
 * Needs wp-env running. Run: npm run test:browser -- review-editor
 */
import { ENGINE, adminUrl, createReview, deleteReview, launch, login, pageErrorsOf, reporter, restRequest } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const created = { pages: [], reviews: [] };

try {
    const review = await createReview(context, { content: 'A review to edit in the editor.', title: 'Editor probe' });
    created.reviews.push(review.id);
    const probePage = await restRequest(context, 'POST', '/wp/v2/pages', { status: 'publish', title: 'Assignable probe page' });
    created.pages.push(probePage.id);
    await page.goto(adminUrl(`post.php?post=${review.id}&action=edit`));

    // the assigned posts search
    const posts = page.locator('#glsr-search-posts');
    await posts.locator('.glsr-search-input').fill('Assignable probe');
    await posts.locator('.glsr-search-results span').first().waitFor();
    await posts.locator('.glsr-search-results span', { hasText: 'Assignable probe page' }).first().click();
    const assigned = posts.locator('.glsr-selected-entries input[name="post_ids[]"]');
    equal(await assigned.evaluateAll(els => els.map(el => el.value)), [String(probePage.id)], 'choosing a search result assigns the page');
    await posts.locator('.glsr-selected-entries .glsr-remove-button').first().click();
    await page.waitForFunction(() => 0 === document.querySelectorAll('#glsr-search-posts .glsr-selected-entries input[name="post_ids[]"]').length);
    assert(true, 'the remove button unassigns it');

    // the assigned users search
    const users = page.locator('#glsr-search-users');
    await users.locator('.glsr-search-input').fill('admin');
    await users.locator('.glsr-search-results span').first().waitFor();
    await users.locator('.glsr-search-results span').first().click();
    assert(1 === await users.locator('.glsr-selected-entries input[name="user_ids[]"]').count(), 'choosing a user result assigns the user');

    // the content textarea grows to its minimum height, where the editor has one
    const content = page.locator('#contentdiv > textarea');
    if (await content.count()) {
        const height = await content.evaluate(el => el.getBoundingClientRect().height);
        assert(height >= 320, `the content textarea is at least 320px tall (${Math.round(height)}px)`);
    } else {
        info('no #contentdiv textarea on the review editor');
    }

    // the pinned control in the publish box
    const pinned = page.locator('#misc-pub-pinned');
    assert(await pinned.locator('#pinned-status-select').isHidden(), 'the pinned select starts hidden');
    await pinned.locator('a[data-click="edit"]').click();
    await pinned.locator('#pinned-status-select').waitFor({ state: 'visible' });
    await pinned.locator('#pinned-status').selectOption('1');
    await pinned.locator('a[data-click="save"]').click();
    await pinned.locator('#pinned-status-select').waitFor({ state: 'hidden' });
    equal((await pinned.locator('#pinned-status-text').innerText()).trim(), 'Yes', 'OK writes the choice into the text');
    equal(await pinned.locator('input[name="site-reviews[is_pinned]"]').inputValue(), '1', 'and into the hidden field the form posts');
    await pinned.locator('a[data-click="edit"]').click();
    await pinned.locator('#pinned-status').selectOption('0');
    await pinned.locator('a[data-click="cancel"]').click();
    await pinned.locator('#pinned-status-select').waitFor({ state: 'hidden' });
    equal(await pinned.locator('#pinned-status').inputValue(), '1', 'Cancel restores the select to the saved value');

    // the Edit Details toggle
    const toggle = page.locator('input[data-edit-review]');
    const field = page.locator('.glsr-input-value').first();
    assert(await field.isDisabled(), 'the detail fields start disabled');
    await toggle.check();
    assert(await field.isEnabled(), 'ticking Edit Details enables them');
    await toggle.uncheck();
    assert(await field.isDisabled(), 'unticking disables them again');

    // the verification request button, where the setting shows it
    if (await page.locator('#glsr-request-verification').count()) {
        assert(true, 'the verification request button is on the page');
    } else {
        info('no verification request button: the Request Verification setting is off, or the review is verified');
    }

    equal(errors, [], 'the page logged no script error');
} finally {
    for (const id of created.reviews) {
        await deleteReview(context, id);
    }
    for (const id of created.pages) {
        await restRequest(context, 'DELETE', `/wp/v2/pages/${id}`, { force: true });
    }
    await browser.close();
}
done(`The review editor behaves in ${ENGINE}`);
