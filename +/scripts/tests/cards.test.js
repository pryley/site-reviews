const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// A card of the Tools page with the two things that expand it: a link, and the submit button of
// views/pages/tools/general/import-settings.php, whose form reloads the page.
const HTML = `
<div class="glsr-nav-view" id="general">
    <a href="#" data-expand="#tools-import-plugin-settings">Import Settings</a>
    <div class="glsr-card postbox closed">
        <div class="glsr-card-heading"><button type="button" class="glsr-accordion-trigger" aria-expanded="false"></button></div>
        <div id="tools-import-plugin-settings">
            <form onsubmit="return false"><button type="submit" data-expand="#tools-import-plugin-settings">Import Settings</button></form>
        </div>
    </div>
</div>`;

const click = async (selector) => {
    const { window } = loadAdmin({ before: 'Element.prototype.scrollIntoView = function () {};', html: HTML });
    window.document.querySelector(selector).click()
    await new Promise(resolve => setTimeout(resolve, 50));
    return window;
};

test('an expand link opens its card and forgets it', async () => {
    const window = await click('a[data-expand]');
    assert.equal(window.document.querySelector('.glsr-card').classList.contains('closed'), false)
    assert.equal(window.localStorage.getItem('glsr-expand'), null)
});

test('a submit button with data-expand keeps its card for the page its form loads', async () => {
    const window = await click('button[data-expand]');
    assert.equal(window.localStorage.getItem('glsr-expand'), '#tools-import-plugin-settings')
});
