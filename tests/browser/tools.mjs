/**
 * The Tools page: the console, System Info, a tool button and its notice, a card accordion and
 * expand link, the CSV import through its progress bar, and the rollback dialog, as a user sees them.
 *
 * Needs wp-env running. Run: npm run test:browser -- tools
 */
import { mkdtempSync, writeFileSync } from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { ENGINE, adminUrl, launch, login, pageErrorsOf, reporter, wpCli } from './harness.mjs';

const { assert, done, equal, info } = reporter();

const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
await login(page);
const TOOLS = adminUrl('edit.php?post_type=site-review&page=glsr-tools');
const tab = id => page.locator(`a.glsr-nav-tab[data-id="${id}"]`);
// a request to one of our routes: PUT and DELETE travel as a POST with the X-HTTP-Method-Override header
const rest = (method, path) => page.waitForResponse(response => decodeURIComponent(response.url()).includes(`/site-reviews/v1/${path}`) && method === (response.request().headers()['x-http-method-override'] || response.request().method()).toUpperCase());
const TITLE = 'Browser suite import probe';

try {
    // the console
    await page.goto(`${TOOLS}&tab=console`);
    await Promise.all([rest('GET', 'plugin/console'), page.click('#glsr-fetch-console')]);
    assert((await page.inputValue('#glsr-log-file')).length > 0, 'Reload fills the console textarea');
    const level = page.locator('select[name$="[level]"]');
    const levelBefore = await level.inputValue();
    await level.selectOption('1' === levelBefore ? '2' : '1');
    await Promise.all([rest('PUT', 'plugin/console/level'), page.click('#glsr-console-level')]);
    assert(await page.locator('#glsr-notices .notice, .glsr-notice').count() > 0, 'changing the level shows a notice');
    await level.selectOption(levelBefore);
    await Promise.all([rest('PUT', 'plugin/console/level'), page.click('#glsr-console-level')]);
    await Promise.all([rest('DELETE', 'plugin/console'), page.click('#glsr-clear-console')]);
    assert((await page.inputValue('#glsr-log-file')).includes('Console is empty'), 'Clear empties the console');

    // System Info
    await tab('system-info').click();
    const info_ = page.locator('#system-info textarea');
    await page.waitForFunction(() => {
        const el = document.querySelector('#system-info textarea');
        return el && !el.value.startsWith('Loading');
    });
    assert((await info_.inputValue()).includes('WordPress'), 'the System Info tab loads the report into its textarea');
    assert(await page.locator('#glsr-download-system-info').isEnabled(), 'and enables its download button');

    // a tool button, a card accordion and an expand link
    await tab('general').click();
    const card = page.locator('#tools-reset-assigned-meta').locator('xpath=ancestor::div[contains(@class,"glsr-card")][1]');
    const wasClosed = await card.evaluate(el => el.classList.contains('closed'));
    await card.locator('.glsr-card-heading').click();
    assert(wasClosed !== await card.evaluate(el => el.classList.contains('closed')), 'clicking a card heading toggles it');
    if (await card.evaluate(el => el.classList.contains('closed'))) {
        await card.locator('.glsr-card-heading').click();
    }
    await Promise.all([rest('POST', 'reviews/counts'), card.locator('button[data-ajax-click]').click()]);
    await page.locator('#glsr-notices .notice').first().waitFor();
    assert(await page.locator('#glsr-notices .notice-success').count() > 0, 'running a tool shows its success notice');
    const expand = page.locator('a[data-expand]').first();
    if (await expand.count()) {
        const holder = expand.locator('xpath=ancestor::div[contains(@class,"glsr-card")][1]');
        if (await holder.evaluate(el => el.classList.contains('closed'))) {
            await holder.locator('.glsr-card-heading').click(); // the link sits inside a closed card
        }
        const targetId = await expand.getAttribute('data-expand');
        const target = page.locator(targetId).locator('xpath=ancestor::div[contains(@class,"glsr-card")][1]');
        await target.evaluate(el => el.classList.add('closed'));
        await expand.click();
        await page.waitForFunction(id => !document.querySelector(id)?.closest('.glsr-card')?.classList.contains('closed'), targetId);
        assert(true, 'an expand link opens the card it points at');
    } else {
        info('no expand link on the General tab');
    }

    // the CSV import
    const dir = mkdtempSync(path.join(os.tmpdir(), 'glsr-import-'));
    const csv = path.join(dir, 'reviews.csv');
    writeFileSync(csv, [
        'date,rating,title,content,name,email',
        `2026-01-02 10:00:00,5,${TITLE} one,The first imported probe review.,Probe One,probe-one@example.org`,
        `2026-01-03 10:00:00,4,${TITLE} two,The second imported probe review.,Probe Two,probe-two@example.org`,
    ].join('\n'));
    const importCard = page.locator('#csv_import_file').locator('xpath=ancestor::div[contains(@class,"glsr-card")][1]');
    if (await importCard.evaluate(el => el.classList.contains('closed'))) {
        await importCard.locator('.glsr-card-heading').click();
    }
    await page.setInputFiles('#csv_import_file', csv);
    await page.selectOption('#csv_date_format', 'Y-m-d H:i:s');
    const importButton = importCard.locator('button[data-ajax-import]');
    assert(await importButton.isEnabled(), 'choosing a file enables the Import button');
    await importButton.click();
    await page.locator('.glsr-progress').waitFor();
    assert(true, 'the import shows its progress bar');
    await page.waitForFunction(title => [...document.querySelectorAll('#glsr-notices .notice')].some(el => /imported/i.test(el.textContent)), TITLE, { timeout: 60000 });
    assert(true, 'and ends with a notice that reviews were imported');
    equal(wpCli(['post', 'list', '--post_type=site-review', '--post_status=any', `--s=${TITLE}`, '--format=count']), '2', 'the two rows are reviews now');

    // the rollback dialog
    const rollback = page.locator('#rollback-plugin');
    if (await rollback.locator('#rollback_version option').count() > 1) {
        await rollback.locator('button[type="submit"]').click();
        const dialog = page.locator('#rollback-dialog');
        await dialog.waitFor({ state: 'visible' });
        assert(await dialog.locator('[data-cancel]').evaluate(el => el === document.activeElement), 'the rollback dialog opens with Cancel focused');
        await page.keyboard.press('Escape');
        await dialog.waitFor({ state: 'hidden' });
        assert(true, 'Escape closes it without rolling back');
    } else {
        info('no rollback versions listed on this site');
    }

    equal(errors, [], 'the page logged no script error');
} finally {
    for (const id of wpCli(['post', 'list', '--post_type=site-review', '--post_status=any', `--s=${TITLE}`, '--field=ID']).split(/\s+/).filter(Boolean)) {
        wpCli(['post', 'delete', id, '--force']);
    }
    await browser.close();
}
done(`The Tools page behaves in ${ENGINE}`);
