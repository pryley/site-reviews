<?php

use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\ReviewField;
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

function styleWithClasses(array $classes): Style
{
    remove_all_filters('site-reviews/config/styles/custom');
    add_filter('site-reviews/config/styles/custom', fn () => ['classes' => $classes]);
    return new Style('custom');
}

beforeEach(fn () => resetPluginState());

afterEach(function () {
    remove_all_filters('site-reviews/config/styles/custom');
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.general.style', 'default');
    glsr()->bind(Style::class, null, true);
});

test('a fresh style reads the chosen style and its configuration', function () {
    // A site that has picked nothing gets "default": the plugin's own unstyled markup.
    $style = new Style();

    expect($style->name)->toBe('default')
        ->and($style->classes)->toBeArray()
        ->and($style->validation)->toBeArray();
});

test('a style can be built for any name, leaving the site\'s alone', function () {
    $site = glsr(Style::class);

    expect((new Style('bootstrap'))->name)->toBe('bootstrap')
        ->and((new Style('bootstrap'))->classes['button'])->toBe('btn btn-primary glsr-button')
        ->and(glsr(Style::class, ['name' => 'bootstrap'])->name)->toBe('bootstrap')
        ->and(glsr(Style::class))->toBe($site)
        ->and($site->name)->toBe('default');
});

test('a style is read-only', function () {
    $style = new Style();

    expect(fn () => $style->name = 'bootstrap')->toThrow(\Error::class);
});

test('a field\'s element classes are its style\'s and leave out the field\'s own', function () {
    // The field's own class is added by the field that builds the element.
    $style = styleWithClasses(['input' => 'tag-class']);

    expect($style->fieldElementClass(new Field(['class' => 'own', 'name' => 'x', 'type' => 'text'])))->toBe('tag-class')
        ->and($style->fieldElementClass(new Field(['class' => 'own', 'name' => 'x', 'type' => 'banana'])))->toBe(''); // an unknown type resolves to no tag
});

test('a field type\'s classes win over its tag\'s', function () {
    $style = styleWithClasses(['input' => 'tag-class', 'input_email' => 'type-class']);

    expect($style->fieldElementClass(new Field(['name' => 'x', 'type' => 'email'])))->toBe('type-class')
        ->and($style->fieldElementClass(new Field(['name' => 'x', 'type' => 'text'])))->toBe('tag-class');
});

test('a toggle or a range reads its own classes, then its input type\'s, then its tag\'s', function (string $type, string $inputType) {
    $field = new Field(['name' => 'x', 'options' => ['a' => 'A'], 'type' => $type]);
    $own = "input_{$type}";
    $resolved = "input_{$inputType}";

    expect($field->type)->toBe($inputType)
        ->and(styleWithClasses(['input' => 'tag', $resolved => 'resolved', $own => 'own'])->fieldElementClass($field))->toBe('own')
        ->and(styleWithClasses(['input' => 'tag', $resolved => 'resolved'])->fieldElementClass($field))->toBe('resolved');
})->with([
    ['range', 'radio'],
    ['toggle', 'checkbox'],
]);

test('an empty entry for a field type is the style\'s answer, not a fallback to the tag\'s', function () {
    $style = styleWithClasses(['input' => 'tag-class', 'input_email' => '']);

    expect($style->fieldElementClass(new Field(['name' => 'x', 'type' => 'email'])))->toBe('');
});

test('a config\'s classes for a field type are sanitized', function () {
    $style = styleWithClasses(['input_email' => 'good-class "><script>']);

    expect($style->classes['input_email'])->toBe('good-class script');
});

test('a style\'s classes for a field type stay out of another style', function () {
    styleWithClasses(['input_email' => 'type-class']);

    expect((new Style('bootstrap'))->classes)->not->toHaveKey('input_email')
        ->and((new Style('bootstrap'))->fieldElementClass(new Field(['name' => 'x', 'type' => 'email'])))->toBe('form-control');
});

test('a Contact Form 7 input takes the classes the plugin prints for its type', function (string $type, string $classes) {
    $field = new Field(['name' => 'x', 'type' => $type]);

    expect((new Style('contact_form_7'))->fieldElementClass($field))->toBe($classes);
})->with([
    ['checkbox', ''],
    ['date', 'wpcf7-date wpcf7-form-control'],
    ['email', 'wpcf7-email wpcf7-form-control wpcf7-text'],
    ['number', 'wpcf7-form-control wpcf7-number'],
    ['password', 'wpcf7-form-control'],
    ['tel', 'wpcf7-form-control wpcf7-tel wpcf7-text'],
    ['text', 'wpcf7-form-control wpcf7-text'],
    ['url', 'wpcf7-form-control wpcf7-text wpcf7-url'],
]);

test('an Elementor date input takes its classes and another input does not', function () {
    $style = new Style('elementor');

    expect($style->fieldElementClass(new Field(['name' => 'x', 'type' => 'date'])))->toContain('elementor-use-native')
        ->and($style->fieldElementClass(new Field(['name' => 'x', 'type' => 'text'])))->not->toContain('elementor-use-native');
});

test('an option\'s label takes its style\'s classes for its type, then its input type\'s', function (string $style, string $type, string $classes) {
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.general.style', $style);
    glsr()->bind(Style::class, null, true);
    $field = new ReviewField(['name' => 'x', 'options' => ['a' => 'A'], 'type' => $type]);

    expect($field->buildField())->toContain("class=\"{$classes}\"");
})->with([
    ['bootstrap', 'range', 'form-check-label'],
    ['bootstrap', 'toggle', 'form-check-label'],
    ['breakdance', 'checkbox', 'breakdance-form-checkbox__text'],
    ['breakdance', 'radio', 'breakdance-form-radio__text'],
    ['breakdance', 'range', 'breakdance-form-radio__text'],
    ['breakdance', 'toggle', 'breakdance-form-checkbox__text'],
    ['contact_form_7', 'checkbox', 'wpcf7-list-item-label'],
    ['default', 'radio', ''],
    ['ninja_forms', 'radio', 'nf-checked-label'],
    ['wpforms', 'checkbox', 'wpforms-field-label-inline'],
    ['wpforms', 'radio', 'wpforms-field-label-inline'],
]);

test('a style can give an option\'s label its own classes for a toggle or a range', function () {
    $style = styleWithClasses(['label_checkbox' => 'resolved', 'label_toggle' => 'own']);

    expect($style->fieldElementLabelClass(new Field(['name' => 'x', 'options' => ['a' => 'A'], 'type' => 'toggle'])))->toBe('own')
        ->and($style->fieldElementLabelClass(new Field(['name' => 'x', 'options' => ['a' => 'A'], 'type' => 'checkbox'])))->toBe('resolved')
        ->and($style->fieldElementLabelClass(new Field(['name' => 'x', 'options' => ['a' => 'A'], 'type' => 'radio'])))->toBe('');
});

test('a style with no stylesheet of its own falls back to the default one', function () {
    expect((new Style('bootstrap'))->stylesheetUrl())->toEndWith('assets/styles/bootstrap.css')
        ->and((new Style('not-a-style'))->stylesheetUrl())->toEndWith('assets/styles/default.css');
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

test('the styleable allow-list can be filtered, and is memoized per instance', function () {
    // the memo is an instance property, not a process-wide static: a new Style
    // (after a settings change, or a container rebind) re-asks the filter
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.general.style', 'bootstrap');
    add_filter('site-reviews/style/templates', fn (array $views) => array_diff($views, ['templates/pagination']));

    $style = new Style();

    expect($style->view('templates/pagination'))->toBe('templates/pagination') // no longer styleable
        ->and($style->view('templates/form/type-checkbox'))->toBe('styles/bootstrap/type-checkbox');
});

test('when no candidate file exists at all, the view is served as asked', function () {
    // The style/views filter is the seam a theme uses to relocate candidates; one
    // that returns none must not blank the view out.
    add_filter('site-reviews/style/views', '__return_empty_array');

    expect((new Style())->view('templates/pagination'))->toBe('templates/pagination');
});
