<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

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

test('a substituted value is not scanned for tags', function () {
    // Review content is a visitor's text. A tag written in it must stay text, in
    // either grammar, whatever the order of the context keys.
    $text = glsr(Template::class)->interpolateContext('{{ content }} / {secret}', [
        'content' => '{secret} and {{ secret }}',
        'secret' => 'expanded',
    ]);

    expect($text)->toBe('{secret} and {{ secret }} / expanded');
});

test('source() answers the text of the plugin\'s template', function () {
    expect(glsr()->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('templates/form/field.php')))
        ->and(glsr()->source('templates/not-a-template'))->toBe('');
});

test('source() follows the plugin style, name by name', function () {
    // Through PublicController's render/view filter, Style::view() resolves each
    // name on its own: a styled file where the style has one, the style's base
    // file for a field_* wrapper, the plugin's file otherwise.
    glsr(OptionManager::class)->set('settings.general.style', 'contact_form_7');
    glsr()->bind(Style::class, null, true); // the style is read once per request: a fresh instance reads the option
    $filter = fn (string $view) => glsr(Style::class)->view($view);
    add_filter('site-reviews/render/view', $filter);
    $field = glsr()->source('templates/form/field');
    $checkbox = glsr()->source('templates/form/field_checkbox');
    $toggle = glsr()->source('templates/form/field_toggle');
    $label = glsr()->source('templates/form/field-label');
    remove_filter('site-reviews/render/view', $filter);
    glsr(OptionManager::class)->set('settings.general.style', 'default');
    glsr()->bind(Style::class, null, true);

    expect($field)->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field.php')))
        ->and($checkbox)->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field_checkbox.php')))
        ->and($toggle)->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field_toggle.php')))
        ->and($label)->toBe(file_get_contents(glsr()->path('templates/form/field-label.php')));
});

test('source() reads the plugin\'s file where render() would include a theme override', function () {
    // The override may run PHP and follow any grammar; a consumer that fills
    // the template itself gets the plugin's, and the published form the theme's.
    $dir = glsr()->themePath('form');
    $file = "{$dir}/field.php";
    $created = !is_dir($dir);
    wp_mkdir_p($dir);
    file_put_contents($file, '<div class="override">{{ field }}</div>');
    try {
        expect(glsr()->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('templates/form/field.php')))
            ->and(glsr(Template::class)->build('templates/form/field', ['context' => ['field' => '<input />']]))->toBe('<div class="override"><input /></div>');
    } finally {
        unlink($file);
        if ($created) {
            rmdir($dir);
            @rmdir(dirname($dir));
        }
    }
});
