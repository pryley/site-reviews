/**
 * The review editor: the assigned posts and users searches, the publish box's pinned control and
 * the Edit Details toggle, as a user sees them.
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
    const probePage = await restRequest(context, 'POST', '/wp/v2/pages', { status: 'publish', title: 'Assignable probe page & Bob\'s' });
    created.pages.push(probePage.id);
    await page.goto(adminUrl(`post.php?post=${review.id}&action=edit`));

    // the assigned posts search
    const posts = page.locator('#glsr-search-posts');
    await posts.locator('.glsr-search-input').fill('Assignable probe');
    await posts.locator('.glsr-search-results span').first().waitFor();
    await posts.locator('.glsr-search-results span', { hasText: 'Assignable probe page' }).first().click();
    const assigned = posts.locator('.glsr-selected-entries input[name="post_ids[]"]');
    equal(await assigned.evaluateAll(els => els.map(el => el.value)), [String(probePage.id)], 'choosing a search result assigns the page');
    const assignedTitle = await posts.locator('.glsr-selected-entries a').first().innerText();
    assert(assignedTitle.includes('& Bob') && !assignedTitle.includes('&#'), 'the assigned page shows its title as text, with no HTML entity');
    await posts.locator('.glsr-selected-entries .glsr-remove-button').first().click();
    await page.waitForFunction(() => 0 === document.querySelectorAll('#glsr-search-posts .glsr-selected-entries input[name="post_ids[]"]').length);
    assert(true, 'the remove button unassigns it');
    await posts.locator('.glsr-search-input').fill('Assignable probe');
    await posts.locator('.glsr-search-results span').first().waitFor();
    const firstResult = posts.locator('.glsr-search-results span[data-id]').first();
    const background = () => firstResult.evaluate(el => getComputedStyle(el).backgroundColor);
    const plainBackground = await background();
    await posts.locator('.glsr-search-input').press('ArrowDown');
    assert(plainBackground !== await background(), 'the arrow keys colour the result they highlight');
    await posts.locator('.glsr-search-input').fill('');
    await page.waitForTimeout(500); // the search waits 200ms after the last key
    assert(await posts.locator('.glsr-search-results').isHidden(), 'emptying the search box closes its results');

    // the assigned users search
    const users = page.locator('#glsr-search-users');
    await users.locator('.glsr-search-input').fill('admin');
    await users.locator('.glsr-search-results span').first().waitFor();
    await users.locator('.glsr-search-results span').first().click();
    assert(1 === await users.locator('.glsr-selected-entries input[name="user_ids[]"]').count(), 'choosing a user result assigns the user');

    // the Author box is the list table's dropdown with a users search
    const author = page.locator('#post_author_override');
    await author.locator('.glsr-filter__selected').dispatchEvent('mousedown');
    await page.waitForFunction(() => 'true' === document.querySelector('#post_author_override')?.getAttribute('aria-expanded'));
    await author.locator('.glsr-filter__search').fill('admin');
    await author.locator('.glsr-filter__results > span[data-id]:not([data-id="0"])').first().waitFor();
    await author.locator('.glsr-filter__results > span[data-id]:not([data-id="0"])').first().dispatchEvent('mousedown');
    equal(await author.locator('.glsr-filter__value').inputValue(), '1', 'choosing a user in the Author box writes its id into the field the form posts');
    assert((await author.locator('.glsr-filter__selected').innerText()).trim().length > 0, 'and shows the user as the selected author');

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
