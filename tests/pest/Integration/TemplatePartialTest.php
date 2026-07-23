<?php

use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Template;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Template (the strtr interpolator behind every rendered view) and Partial
 * (the class dispatcher behind {{ pagination }} and friends) — the corners the
 * rendering tests never reach.
 */

beforeEach(fn () => resetPluginState());

test('a partial that does not exist is logged and renders as nothing', function () {
    expect(glsr(Partial::class)->build('not-a-real-partial'))->toBe('');

    ob_start();
    glsr(Partial::class)->render('not-a-real-partial');
    expect(ob_get_clean())->toBe('');
});

test('a real partial can be echoed as well as returned', function () {
    ob_start();
    glsr(Partial::class)->render('pagination', ['type' => 'loadmore', 'total' => 3, 'current' => 1]);

    expect((string) ob_get_clean())->toContain('glsr-button-loadmore');
});

test('minified output loses the whitespace between tags and nothing else', function () {
    // :empty in the plugin's CSS only matches when the markup carries no stray
    // whitespace, which is the entire reason minify() exists.
    $html = glsr(Template::class)->build('templates/form/field', [
        'context' => ['class' => 'glsr-field', 'field' => '<input type="text" />'],
    ], true);

    expect($html)->not->toMatch('/>\s+</')
        ->and($html)->toContain('<input type="text" />');
});

test('a list of templates renders each entry, skipping anything that is not one', function () {
    // renderMultiple() drives the review-list templates: one data array per entry, echoed in
    // order. A non-array entry (a filter gone wrong) is skipped rather than fatal.
    ob_start();
    glsr(Template::class)->renderMultiple('templates/form/field', [
        ['context' => ['class' => 'first-field', 'field' => '<input />']],
        'not-an-array',
        ['context' => ['class' => 'second-field', 'field' => '<input />']],
    ]);
    $html = (string) ob_get_clean();

    expect($html)->toContain('first-field')
        ->toContain('second-field')
        ->and($html)->not->toContain('not-an-array');
});

test('template data that is not an array is discarded, not fatal', function () {
    // A filter that mangles the context into a string must not take the page down.
    $html = glsr(Template::class)->build('templates/form/field', [
        'context' => 'mangled-by-a-filter',
    ]);

    expect($html)->toContain('data-field'); // rendered, with nothing interpolated
});
