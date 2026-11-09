const assert = require('node:assert/strict');
const fs = require('fs');
const path = require('path');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// The search box of views/pages/settings/strings.php and of views/partials/editor/metabox-assigned-posts.php.
const STRINGS = `
<div class="glsr-strings-form">
    <div class="glsr-search-box" id="glsr-search-translations">
        <input type="search" class="glsr-search-input">
        <div class="glsr-search-results" data-prefix="site_reviews"></div>
    </div>
    <table class="glsr-strings-table"><tbody></tbody></table>
</div>
<template id="tmpl-glsr-string-single"><tr><td class="glsr-string-td2"><input name="site_reviews[settings][strings][{{ data.index }}][id]" data-id value="{{ data.id }}"></td></tr></template>`;

const ASSIGNED = `
<div class="glsr-search-box" id="glsr-search-posts">
    <input type="search" class="glsr-search-input">
    <div class="glsr-search-results"></div>
    <div class="glsr-selected-entries"></div>
    <template><div class="glsr-multibox-entry"><input type="hidden"><a></a></div></template>
</div>`;

// a fetch that answers every search with window.found
const before = (bodyClass) => `
document.body.className = '${bodyClass}';
window.fetch = async () => ({ headers: { get: () => 'application/json' }, json: async () => window.found, ok: true, status: 200, statusText: 'OK' });`;

const SCREENS = {
    'the Assigned Posts box': { bodyClass: 'post-php post-type-site-review', found: [{ id: 3, title: 'A page', url: '#' }], html: ASSIGNED },
    'the Strings search': { bodyClass: 'site-review_page_glsr-settings', found: [{ id: 'abc', s1: 'Submit', text: 'Submit' }], html: STRINGS },
};

// type the term and wait for the debounced search and its answer
const type = async (window, term) => {
    const input = window.document.querySelector('.glsr-search-input');
    input.value = term;
    input.dispatchEvent(new window.Event('input', { bubbles: true }))
    await new Promise(resolve => setTimeout(resolve, 300));
};

Object.entries(SCREENS).forEach(([name, { bodyClass, found, html }]) => {
    test(`${name} closes its results when the term is emptied`, async () => {
        const { window } = loadAdmin({ before: before(bodyClass), html });
        window.found = found;
        const results = window.document.querySelector('.glsr-search-results');
        await type(window, 'a')
        assert.equal(results.classList.contains('is-active'), true)
        assert.equal(results.children.length, 1)
        await type(window, '')
        assert.equal(results.classList.contains('is-active'), false)
        assert.equal(results.children.length, 0)
        assert.equal(window.document.body.classList.contains('glsr-focus'), false)
    });
});

// jsdom loads no stylesheet: the built one is read for the rule
test('the stylesheet colours the result the arrow keys highlight in a search box', () => {
    const css = fs.readFileSync(path.resolve(__dirname, '../../../assets/styles/admin/admin.css'), 'utf8');
    const rule = css.split('}').find(block => block.includes('.glsr-search-box .glsr-search-results>span:focus')) || '';
    assert.ok(rule.includes('.glsr-search-box .glsr-search-results>span.is-highlighted'))
    assert.ok(rule.includes('background-color:var(--glsr-theme-color)'))
});
