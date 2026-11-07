const assert = require('node:assert/strict');
const { test } = require('node:test');
const { loadAdmin } = require('./harness.js');

// jsdom has no CSS Custom Highlight API: a Highlight is a set of ranges and the registry a map
const HIGHLIGHTS = 'window.Highlight = class extends Set {}; window.CSS = { highlights: new Map() };';

const tick = () => new Promise(resolve => setTimeout(resolve, 0));

const texts = (window, category) => [...window.CSS.highlights.get(category) || []].map(range => range.toString());

test('a shortcode sample is highlighted by category, with its text untouched', async () => {
    const { window } = loadAdmin({
        before: HIGHLIGHTS,
        html: '<pre><code class="language-shortcode">[site_reviews assigned_posts="post_id" pagination="ajax"]</code></pre>',
    });
    await tick()
    assert.deepEqual(texts(window, 'tag'), ['site_reviews'])
    assert.deepEqual(texts(window, 'attribute-name'), ['assigned_posts', 'pagination'])
    assert.deepEqual(texts(window, 'string'), ['"post_id"', '"ajax"'])
    assert.deepEqual(texts(window, 'punctuation'), ['[', '=', '=', ']'])
    assert.equal(window.document.querySelector('code').children.length, 0)
});

test('a PHP sample uses the package grammar', async () => {
    const { window } = loadAdmin({
        before: HIGHLIGHTS,
        html: '<pre><code class="language-php">add_filter(\'site-reviews/foo\', fn ($value) => true); // a hook</code></pre>',
    });
    await tick()
    assert.deepEqual(texts(window, 'function'), ['add_filter'])
    assert.deepEqual(texts(window, 'string'), ["'site-reviews/foo'"])
    assert.deepEqual(texts(window, 'variable'), ['$value'])
    assert.deepEqual(texts(window, 'comment'), ['// a hook'])
});

test('without the API the samples are left alone', () => {
    const { window } = loadAdmin({
        html: '<pre><code class="language-php">echo 1;</code></pre>',
    });
    assert.equal(window.CSS, undefined)
    assert.equal(window.document.querySelector('code').textContent, 'echo 1;')
});
