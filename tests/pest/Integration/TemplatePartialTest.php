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

test('template data that is not an array is discarded, not fatal', function () {
    // A filter that mangles the context into a string must not take the page down.
    $html = glsr(Template::class)->build('templates/form/field', [
        'context' => 'mangled-by-a-filter',
    ]);

    expect($html)->toContain('data-field'); // rendered, with nothing interpolated
});
