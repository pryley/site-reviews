<?php

use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Style;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The style module: the layer that lets a site swap the plugin's markup and classes for a CSS
 * framework's (Bootstrap, Bulma, and so on) without touching a template. It reads which style the
 * site chose out of the settings, loads that style's config, and every template and field element
 * asks it what classes to wear and which view to render.
 *
 * Style is a container singleton built once at boot, so its constructor is exercised here directly.
 */

beforeEach(fn () => resetPluginState());

test('a fresh style reads the chosen style and its configuration', function () {
    // A site that has picked nothing gets "default": the plugin's own unstyled markup.
    $style = new Style();

    expect($style->style)->toBe('default')
        ->and($style->classes)->toBeArray()
        ->and($style->pagination)->toBeArray()
        ->and($style->validation)->toBeArray();
});

test('the default validation class for a key is readable, with an empty fallback', function () {
    $style = new Style();

    expect($style->defaultValidation('field_error'))->toBe('glsr-field-is-invalid')
        ->and($style->defaultValidation('not-a-real-key'))->toBe(''); // unknown key, no class
});

test('a field whose tag the style does not dress is handed back its own class', function () {
    // fieldElementClass merges styled classes only for the tags a style knows about. An unrecognised
    // field type has no tag at all, so there is nothing to look up — it keeps the class it arrived with.
    $field = new Field(['name' => 'x', 'type' => 'banana']); // an unknown type resolves to no tag

    expect((new Style())->fieldElementClass($field))->toBe($field->class);
});

test('the stylesheet url is built for a suffixed asset, and ends at a css file', function () {
    // The suffixed form (e.g. the admin stylesheet): assets/styles/{suffix}/{style}-{suffix}.css,
    // falling back to the default style's copy when the chosen style ships none.
    $url = (new Style())->stylesheetUrl('admin');

    expect($url)->toContain('assets/styles/')
        ->and($url)->toEndWith('.css');
});

test('a styled framework serves its own view when it ships one', function () {
    // The bootstrap style ships its own pagination and choice-input views; a view
    // it does not ship falls back to the plugin's own under templates/.
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.general.style', 'bootstrap');
    $style = new Style();

    expect($style->view('templates/pagination'))->toBe('styles/bootstrap/pagination')
        // the base view is matched from the variant's name (before the underscore)
        ->and($style->view('templates/form/type-checkbox_rating'))->toBe('styles/bootstrap/type-checkbox')
        ->and($style->view('templates/form/field'))->toBe('templates/form/field');
});

test('a view that is not in the styleable allow-list is never rewritten', function () {
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.general.style', 'bootstrap');

    expect((new Style())->view('pages/settings/general'))->toBe('pages/settings/general');
});

test('when no candidate file exists at all, the view is served as asked', function () {
    // The style/views filter is the seam a theme uses to relocate candidates; one
    // that returns none must not blank the view out.
    add_filter('site-reviews/style/views', '__return_empty_array');

    expect((new Style())->view('templates/pagination'))->toBe('templates/pagination');
});
