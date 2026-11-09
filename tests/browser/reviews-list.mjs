/**
 * The review list: the status, pin and verify toggles, the filter dropdown, the Screen Options
 * filter checkboxes and quick edit, as a user sees them.
 *
 * Needs wp-env running. Run: npm run test:browser -- reviews-list
 */
import { ENGINE, adminUrl, createReview, deleteReview, launch, login, pageErrorsOf, reporter } from './harness.mjs';

const { assert, done, equal, info } = reporter();


const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const LIST = adminUrl('edit.php?post_type=site-review');
const row = id => page.locator(`#post-${id}`);
const toggleFilters = () => page.waitForResponse(response => decodeURIComponent(response.url()).includes('/wp/v2/users/me')); // the preference is saved through WordPress's users route
const created = [];

try {
    const approved = await createReview(context, { content: 'An approved probe review.', title: 'Approved probe' });
    created.push(approved.id);
    const pending = await createReview(context, { content: 'A pending probe review.', pending: true, title: 'Pending probe' });
    created.push(pending.id);
    await page.goto(LIST);

    // the status toggle
    assert(await row(pending.id).evaluate(el => el.classList.contains('status-pending')), 'the pending review\'s row carries status-pending');
    const countsBefore = await page.locator('ul.subsubsub').innerHTML();
    await row(pending.id).hover();
    await row(pending.id).locator('a.glsr-toggle-status', { hasText: 'Approve' }).first().click({ force: true });
    await page.waitForFunction(id => document.getElementById(`post-${id}`)?.classList.contains('status-publish'), pending.id);
    assert(true, 'approving from the row actions turns the row into status-publish');
    assert(await page.locator('ul.subsubsub').innerHTML() !== countsBefore, 'the status counts above the table are redrawn');

    // the pin and verify toggles
    const pin = row(approved.id).locator('td.column-is_pinned i.pin-review');
    const wasPinned = await pin.evaluate(el => el.classList.contains('pinned'));
    await pin.click();
    await page.waitForFunction(([id, before]) => {
        const el = document.querySelector(`#post-${id} td.column-is_pinned i.pin-review`);
        return el && !el.classList.contains('spinner') && el.classList.contains('pinned') !== before;
    }, [approved.id, wasPinned]);
    assert(true, 'clicking the pin icon toggles the pinned class');
    const pinNotice = await page.locator('#glsr-notices .notice').first().innerText({ timeout: 3000 }).catch(() => '');
    assert(pinNotice.includes(wasPinned ? 'unpinned' : 'pinned'), 'and shows its notice');

    // a status change the server refuses
    await page.route(url => decodeURIComponent(url.href).includes(`reviews/${approved.id}/status`), route => route.fulfill({
        body: JSON.stringify({ code: 'glsr_status_failed', message: 'The status could not be changed.' }),
        contentType: 'application/json',
        status: 500,
    }));
    await row(approved.id).hover();
    await row(approved.id).locator('a.glsr-toggle-status', { hasText: 'Unapprove' }).first().click({ force: true });
    const statusNotice = await page.locator('#glsr-notices .notice-error').first().innerText({ timeout: 3000 }).catch(() => '');
    assert(statusNotice.includes('The status could not be changed.'), 'a status change that fails shows the error');
    assert(await row(approved.id).evaluate(el => el.classList.contains('status-publish')), 'and leaves the row as it was');
    await page.unrouteAll();
    const verify = row(approved.id).locator('td.column-is_verified i.verify-review');
    if (await verify.count()) {
        const wasVerified = await verify.evaluate(el => el.classList.contains('verified'));
        await verify.click();
        await page.waitForFunction(([id, before]) => {
            const el = document.querySelector(`#post-${id} td.column-is_verified i.verify-review`);
            return el && !el.classList.contains('spinner') && el.classList.contains('verified') !== before;
        }, [approved.id, wasVerified]);
        assert(true, 'clicking the verify icon toggles the verified class');
    } else {
        // the site-reviews/verification/enabled filter is off until a snippet or addon turns it on
        info('the verify icon is not clickable on this site');
    }

    // the author filter: a searchable dropdown of ours, where rating and category are native selects
    const filterName = 'author';
    const filter = page.locator(`#glsr-filter-by-${filterName}`);
    await filter.locator('.glsr-filter__selected').dispatchEvent('mousedown');
    await page.waitForFunction(name => 'true' === document.querySelector(`#glsr-filter-by-${name}`)?.getAttribute('aria-expanded'), filterName);
    const options = filter.locator('.glsr-filter__results > span');
    assert(await options.count() >= 1, `opening the ${filterName} filter lists its options`);
    const chosen = options.last();
    const chosenId = await chosen.getAttribute('data-id');
    await chosen.dispatchEvent('mousedown');
    equal(await filter.locator('.glsr-filter__value').inputValue(), chosenId, 'choosing an option sets the filter\'s value');
    assert('false' === await filter.getAttribute('aria-expanded'), 'and closes the dropdown');
    await filter.locator('.glsr-filter__selected').dispatchEvent('mousedown');
    await page.waitForFunction(name => 'true' === document.querySelector(`#glsr-filter-by-${name}`)?.getAttribute('aria-expanded'), filterName);
    await page.click('#post-search-input');
    await page.waitForTimeout(100); // the filter moves the focus 10ms after the click
    assert('false' === await filter.getAttribute('aria-expanded'), 'a click in the search box closes the open dropdown');
    equal(await page.evaluate(() => document.activeElement.id), 'post-search-input', 'and the focus stays in the search box');

    // the Screen Options filter checkboxes
    await page.click('.glsr-screen-meta-toggle[aria-controls="screen-options-wrap"]'); // our header holds the Screen Options toggle
    const checkbox = page.locator(`.enable-filter-tog[value="${filterName}"]`);
    assert(await checkbox.isChecked(), `the ${filterName} filter is enabled in Screen Options`);
    await Promise.all([toggleFilters(), checkbox.uncheck()]);
    assert(await filter.evaluate(el => el.classList.contains('hidden')), 'unticking it hides the dropdown');
    await page.reload();
    assert(0 === await page.locator(`#glsr-filter-by-${filterName}:visible`).count(), 'the dropdown stays hidden after a reload');
    await page.click('.glsr-screen-meta-toggle[aria-controls="screen-options-wrap"]');
    await Promise.all([toggleFilters(), page.locator(`.enable-filter-tog[value="${filterName}"]`).check()]);
    await page.reload();
    assert(1 === await page.locator(`#glsr-filter-by-${filterName}:visible`).count(), 'ticking it again brings the dropdown back');

    // quick edit
    await row(approved.id).hover();
    await row(approved.id).locator('.editinline').click({ force: true });
    await page.waitForFunction(() => document.querySelector(':is(#inline-edit, .inline-editor) [data-name="post_content"]')?.value?.length > 0);
    equal(await page.inputValue('.inline-editor [data-name="post_content"]'), 'An approved probe review.', 'quick edit fills the content field from the row');
    assert(null !== await page.$('.inline-editor [name="_response"]'), 'quick edit has the response field');
    await page.click('.inline-editor .cancel');

    equal(errors, [], 'the page logged no script error');
} finally {
    for (const id of created) {
        await deleteReview(context, id);
    }
    await browser.close();
}
done(`The review list behaves in ${ENGINE}`);
