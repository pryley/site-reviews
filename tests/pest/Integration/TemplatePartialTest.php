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

test('a tag no value fills is removed, unless the template has no context at all', function () {
    expect(glsr(Template::class)->interpolateContext('{{ known }}|{{ unknown }}', ['known' => 'x']))->toBe('x|')
        ->and(glsr(Template::class)->interpolateContext('{{ known }}|{{ unknown }}'))->toBe('{{ known }}|{{ unknown }}');
});

test('a template built without a tag it holds doesn\'t print the tag', function () {
    $html = glsr(Template::class)->build('templates/form/type-checkbox', [
        'context' => ['class' => 'a', 'id' => 'b', 'input' => '<input />', 'text' => 'c'],
    ]);

    expect($html)->not->toContain('{{')
        ->and($html)->toContain('<label class="" for="b">');
});

test('source() answers the text of the plugin\'s template', function () {
    expect((new Style())->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('templates/form/field.php')))
        ->and((new Style())->source('templates/not-a-template'))->toBe('');
});

test('source() follows the style, name by name', function () {
    // Each name resolves on its own: a styled file where the style has one, the
    // style's base file for a field_* wrapper, the plugin's file otherwise.
    $style = new Style('contact_form_7');

    expect($style->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field.php')))
        ->and($style->source('templates/form/field_checkbox'))->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field_checkbox.php')))
        ->and($style->source('templates/form/field_toggle'))->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field_toggle.php')))
        ->and($style->source('templates/form/field-label'))->toBe(file_get_contents(glsr()->path('templates/form/field-label.php')));
});

test('source() is the style\'s, whatever the site\'s style is', function () {
    glsr(OptionManager::class)->set('settings.general.style', 'contact_form_7');

    expect((new Style('default'))->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('templates/form/field.php')));
});

test('source() finds a candidate a filter gives without the views folder', function () {
    add_filter('site-reviews/style/views', fn () => ['styles/contact_form_7/field']);

    expect((new Style())->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('views/styles/contact_form_7/field.php')));
});

test('source() reads the base file where a theme has a copy of a field variant', function () {
    // view() counts the theme's copy, which has no file in the plugin.
    $dir = glsr()->themePath('form');
    $file = "{$dir}/field_email.php";
    $created = !is_dir($dir);
    wp_mkdir_p($dir);
    file_put_contents($file, '<div class="override">{{ field }}</div>');
    try {
        expect((new Style())->source('templates/form/field_email'))->toBe(file_get_contents(glsr()->path('templates/form/field.php')));
    } finally {
        unlink($file);
        if ($created) {
            rmdir($dir);
            @rmdir(dirname($dir));
        }
    }
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
        expect((new Style())->source('templates/form/field'))->toBe(file_get_contents(glsr()->path('templates/form/field.php')))
            ->and(glsr(Template::class)->build('templates/form/field', ['context' => ['field' => '<input />']]))->toBe('<div class="override"><input /></div>');
    } finally {
        unlink($file);
        if ($created) {
            rmdir($dir);
            @rmdir(dirname($dir));
        }
    }
});
