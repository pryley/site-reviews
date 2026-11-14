/**
 * The block editors: the Review Form block's preview in the post editor and the Site Editor, and
 * the multi select and the single select of a block's search controls.
 *
 * Needs wp-env running. Run: npm run test:browser -- block-editor
 */
import { readFileSync } from 'node:fs';
import { ENGINE, ROOT, adminUrl, createReview, deleteReview, launch, login, pageErrorsOf, reporter, restRequest, wpCli } from './harness.mjs';

const { done, equal } = reporter();

const OPTION = 'GeminiLabs\\SiteReviews\\Database\\OptionManager::class';
const browser = await launch();
const context = await browser.newContext({ viewport: { height: 900, width: 1280 } });
const page = await context.newPage();
const errors = pageErrorsOf(page);
page.on('dialog', dialog => dialog.accept()); // a page with a picked item is left unsaved
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
        persists: await canvas.locator('.glsr-persist-data').count(),
        stars: await canvas.locator('.glsr-star-rating').count(),
    };
};
const sidebar = page.locator('.interface-interface-skeleton__sidebar');
// Select the block and return its control.
// The input is the search of a multi select or the button of a single select.
const control = async (blockName, label) => {
    await page.evaluate((name) => {
        const block = window.wp.data.select('core/block-editor').getBlocks().find(candidate => name === candidate.name);
        window.wp.data.dispatch('core/block-editor').selectBlock(block.clientId);
        window.wp.data.dispatch('core/interface').enableComplementaryArea('core', 'edit-post/block');
    }, blockName);
    const input = sidebar.getByRole('combobox', { name: label });
    await input.waitFor({ timeout: 15000 });
    // the control is disabled while it loads its first list
    await page.waitForFunction(element => !element.disabled, await input.elementHandle());
    const root = sidebar.locator('.glsr-searchable').filter({ has: page.getByRole('combobox', { name: label }) });
    const suggestions = async () => {
        await page.waitForTimeout(300);
        return root.evaluate((element) => {
            const list = element.querySelector('.glsr-searchable__popup');
            const box = list?.getBoundingClientRect();
            // The element at nine points of the list: a control that shows through it is not the list.
            const isOnTop = !!list && [0.1, 0.5, 0.9].every(x => [0.1, 0.5, 0.9].every(
                y => list.contains(element.ownerDocument.elementFromPoint(box.left + box.width * x, box.top + box.height * y))
            ));
            return {
                // a list inside the control would add to its height
                height: Math.round(element.getBoundingClientRect().height),
                isOnTop,
                open: !!list,
                position: list ? getComputedStyle(list).position : null,
            };
        });
    };
    return { input, root, suggestions };
};
// Whether the block's control sends a search of one digit to its route.
const searches = async (blockName, label, route) => {
    const { input, root } = await control(blockName, label);
    // Wait for the answer because a page left while a request is out logs its failure.
    const answered = page.waitForResponse((response) => {
        const url = decodeURIComponent(response.url());
        return url.includes(route) && /[?&]search=5(&|$)/.test(url);
    }, { timeout: 10000 }).then(() => true, () => false);
    if ('BUTTON' === await input.evaluate(element => element.tagName)) {
        await input.click(); // a single select has its search in its list
    }
    await root.locator('.glsr-searchable__input').fill('5');
    const isAnswered = await answered;
    await page.keyboard.press('Escape');
    return isAnswered;
};
let post = null;
let review = null;
let sessionStorage = null;

try {
    sessionStorage = wpCli(['eval', 'echo glsr_get_option("forms.session_storage");']) || 'no';
    wpCli(['eval', `glsr(${OPTION})->set('settings.forms.session_storage', 'yes');`]);
    post = await restRequest(context, 'POST', '/wp/v2/pages', { content: '<!-- wp:site-reviews/form /-->\n\n<!-- wp:site-reviews/review /-->', status: 'publish', title: 'Block editor probe' });

    await page.goto(adminUrl(`post.php?post=${post.id}&action=edit`));
    equal(await preview(), { persists: 0, stars: 1 }, 'the post editor draws the rating field of a Review Form block as stars, and its preview keeps no field values');
    await page.keyboard.press('Escape'); // the welcome guide

    // a multi select's list
    const pages = await control('site-reviews/form', 'Assign Reviews to Pages');
    await pages.input.focus();
    const focused = await pages.suggestions();
    await page.keyboard.press('ArrowDown');
    const arrowed = await pages.suggestions();
    await page.keyboard.press('Escape');
    const escaped = await pages.suggestions();
    await pages.input.click();
    const pressed = await pages.suggestions();
    await page.keyboard.press('Escape');
    equal(
        [focused.open, arrowed, escaped.open, pressed.open],
        [false, { height: focused.height, isOnTop: true, open: true, position: 'absolute' }, false, true],
        'a multi select takes the focus with its list closed; the Down Arrow or a press opens it over the controls below'
    );
    // its picked items
    const picker = () => pages.root.evaluate((element) => {
        const field = element.querySelector('.glsr-searchable__field');
        const input = field.querySelector('input');
        const items = [...field.querySelectorAll('.glsr-searchable__chip')];
        return {
            focus: document.activeElement === input ? 'search' : document.activeElement.closest('.glsr-searchable__chip') ? 'item' : document.activeElement.tagName,
            isSearchLast: field.lastElementChild === input,
            isSearchUnder: !items.length || input.getBoundingClientRect().top >= Math.max(...items.map(item => item.getBoundingClientRect().bottom)) - 1,
            items: items.length,
            sizes: [Math.round(field.getBoundingClientRect().height), Math.round(input.getBoundingClientRect().height), !!input.placeholder],
        };
    });
    const empty = await picker();
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
    await page.keyboard.press('Escape');
    await page.waitForTimeout(300);
    const picked = await picker();
    // The item's height with its text on one line: a long title wraps and adds lines to both.
    const itemHeight = await pages.root.locator('.glsr-searchable__chip-label').evaluate(label => Math.round(label.parentElement.getBoundingClientRect().height) - Math.round(label.getBoundingClientRect().height) + 24);
    await page.keyboard.press('ArrowLeft');
    await page.waitForTimeout(300);
    const moved = [(await picker()).focus, (await pages.suggestions()).open];
    await page.keyboard.press('Enter');
    await page.waitForTimeout(300);
    const returned = [(await picker()).focus, (await picker()).items];
    await page.keyboard.press('ArrowLeft');
    await page.keyboard.press('Backspace');
    await page.waitForTimeout(300);
    const removed = await picker();
    equal(
        [empty.sizes, { ...picked, sizes: picked.sizes.slice(1) }, itemHeight, moved, returned, [removed.focus, removed.items]],
        [
            [40, 38, true],
            { focus: 'search', isSearchLast: true, isSearchUnder: true, items: 1, sizes: [38, true] },
            26,
            ['item', false],
            ['search', 1],
            ['search', 0],
        ],
        'an empty multi select is 40px; its search keeps its placeholder on a line under a 26px picked item; the Left Arrow moves the focus to the item with the list closed, Enter brings it back and removes nothing, and Backspace removes the item'
    );
    // its list while a search loads and when it finds nothing
    const isProbeSearch = url => /[?&]search=(one|slow)probe/.test(decodeURIComponent(url.href));
    await page.route(isProbeSearch, async (route) => {
        const isSlow = decodeURIComponent(route.request().url()).includes('search=slowprobe');
        if (isSlow) {
            await new Promise(resolve => setTimeout(resolve, 1500));
        }
        await route.fulfill({ json: isSlow ? [] : [{ id: 1, title: 'One "probe"' }] });
    });
    const firstRow = () => pages.root.locator('.glsr-searchable__popup').evaluate((popup) => {
    const box = popup.getBoundingClientRect();
    const row = popup.querySelector('.glsr-searchable__option, .glsr-searchable__empty, .glsr-searchable__loading');
    const range = document.createRange();
    range.selectNodeContents(row.querySelector('.glsr-searchable__option-label') ?? row);
    const text = [...range.getClientRects()].at(-1); // the text, after a spinner
    const spinner = row.querySelector('.components-spinner')?.getBoundingClientRect();
    return {
        isSpinnerLevel: spinner ? Math.abs(spinner.top + spinner.height / 2 - (text.top + text.height / 2)) <= 1 : null,
        kind: row.className.split(' ')[0].replace('glsr-searchable__', ''),
        popup: Math.round(box.height),
        rows: popup.querySelectorAll('.glsr-searchable__option, .glsr-searchable__empty, .glsr-searchable__loading').length,
        spinner: spinner ? Math.round(spinner.width) : null,
        textLeft: Math.round(text.left - box.left),
    };
    });
    await pages.input.fill('oneprobe');
    await page.waitForTimeout(900);
    const oneItem = await firstRow();
    const slowAnswer = page.waitForResponse(response => decodeURIComponent(response.url()).includes('search=slowprobe'), { timeout: 8000 }).catch(() => {});
    await pages.input.fill('slowprobe');
    await page.waitForTimeout(900);
    const whileLoading = await firstRow();
    await slowAnswer;
    await page.waitForTimeout(300);
    equal(
        [oneItem.kind, whileLoading, await firstRow()],
        [
            'option',
            { isSpinnerLevel: true, kind: 'loading', popup: oneItem.popup, rows: 1, spinner: 12, textLeft: oneItem.textLeft },
            // textLeft is 12px from the list's border because the line has no check to leave room for
            { isSpinnerLevel: null, kind: 'empty', popup: oneItem.popup, rows: 1, spinner: null, textLeft: 13 },
        ],
        'while a search loads the list is as tall as one item, with a 12px spinner where an item has its check and the text level with it; when it finds nothing the list is as tall, with the line at its start'
    );
    await page.unroute(isProbeSearch);
    await pages.input.fill('');
    await page.keyboard.press('Escape');
    // a single select
    review = await createReview(context, { title: 'Suggestions probe' });
    const reviews = await control('site-reviews/review', 'Review Post ID');
    const resting = await reviews.suggestions();
    const placeholder = await reviews.input.textContent();
    await reviews.input.click();
    const search = reviews.root.locator('.glsr-searchable__input');
    const isSearching = await search.evaluate(element => element === document.activeElement);
    const found = page.waitForResponse(response => decodeURIComponent(response.url()).includes('search=Suggestions probe'), { timeout: 10000 }).catch(() => {});
    await search.fill('Suggestions probe');
    await found;
    const opened = await reviews.suggestions();
    await page.keyboard.press('Enter');
    await page.waitForTimeout(300);
    const chosen = [await reviews.input.textContent(), (await reviews.suggestions()).open, await reviews.input.evaluate(element => element === document.activeElement)];
    await reviews.root.getByRole('button', { name: 'Clear' }).click();
    await page.waitForTimeout(300);
    equal(
        [resting.open, isSearching, opened, chosen, await reviews.input.textContent(), await reviews.input.evaluate(element => element === document.activeElement)],
        [
            false,
            true,
            { height: resting.height, isOnTop: true, open: true, position: 'absolute' },
            [`${review.id}: Suggestions probe`, false, true],
            placeholder,
            true,
        ],
        'a single select opens its search and what it finds over the controls below; Enter picks the item found, which the button then names with the focus back on it; Clear empties it'
    );
    // the Review block of the next previews shows the latest review
    await deleteReview(context, review.id);
    review = null;
    // Each rule is under the controls' classes, so no rule reaches a WordPress field.
    const rules = readFileSync(`${ROOT}/assets/blocks/site_reviews/index.css`, 'utf8').replace(/@media[^{]+\{/g, '').match(/[^{}]+(?=\{)/g) ?? [];
    equal(
        rules.flatMap(rule => rule.split(',')).filter(selector => !selector.trim().startsWith('.glsr-searchable')),
        [],
        'and the stylesheet reaches no other field'
    );

    equal(
        [
            await searches('site-reviews/form', 'Assign Reviews to Pages', '/shortcodes/site_reviews_form/options/assigned_posts'),
            await searches('site-reviews/review', 'Review Post ID', '/shortcodes/site_review/options/post_id'),
        ],
        [true, true],
        'a multi select and a single select each send a search of one digit'
    );

    await page.goto(adminUrl(`site-editor.php?postType=page&postId=${post.id}&canvas=edit`));
    equal(await preview(), { persists: 0, stars: 1 }, 'and so does the Site Editor');

    await page.goto(post.link);
    equal(await page.locator('.glsr-persist-data').count(), 1, 'the form on the published page keeps them, as Enable Session Storage says');

    equal(moduleErrors, [], 'every module the previews name loaded');
    equal(errors, [], 'the pages logged no script error');
} finally {
    if (post) {
        await restRequest(context, 'DELETE', `/wp/v2/pages/${post.id}`, { force: true }).catch(() => {});
    }
    if (review) {
        await deleteReview(context, review.id).catch(() => {});
    }
    if (null !== sessionStorage) {
        wpCli(['eval', `glsr(${OPTION})->set('settings.forms.session_storage', '${sessionStorage}');`]);
    }
    await browser.close();
}
done(`The Review Form block previews in ${ENGINE}`);
