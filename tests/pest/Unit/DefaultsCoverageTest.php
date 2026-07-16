<?php

use GeminiLabs\SiteReviews\Defaults\AddonDefaults;
use GeminiLabs\SiteReviews\Defaults\FeatureDefaults;
use GeminiLabs\SiteReviews\Defaults\FieldRuleDefaults;
use GeminiLabs\SiteReviews\Defaults\FlagDefaults;
use GeminiLabs\SiteReviews\Defaults\PointerDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Defaults\TutorialDefaults;
use GeminiLabs\SiteReviews\Defaults\VideoDefaults;
use GeminiLabs\SiteReviews\Modules\Html\FieldElements\Tel;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Defaults classes that nothing else reaches.
 *
 * A Defaults class is the shape of a data structure written down — what keys exist, what they
 * default to, and (through restrict()) that nothing else may be in it. They sit on the boundary
 * between the plugin and whatever handed it an array (shortcode attributes, block JSON, an addon's
 * registration, an API response), so the test that matters at a boundary is the same for each:
 *
 *   restrict()  keeps the keys it knows and DROPS the rest.
 *   merge()     keeps the keys it knows and KEEPS the rest.
 *
 * Getting those two the wrong way round is how an unfiltered value ends up where it was never
 * meant to be. The rest — that a default is what the source says — catches the typo in a key
 * nobody reads.
 */

beforeEach(function () {
    resetPluginState();
});

/**
 * Each Defaults class: the class, a key that is its own, and a value that key accepts.
 *
 * The sample value is load-bearing: several keys are constrained (an enum, a sanitizer, or — for
 * TutorialDefaults — a nested Defaults that maps VideoDefaults over each entry, so `videos` must
 * be an array OF ARRAYS or it TypeErrors). A generic "given" string would not survive them.
 */
dataset('defaults', [
    'addon' => [AddonDefaults::class, 'slug', 'an-addon-slug'],
    'feature' => [FeatureDefaults::class, 'feature', 'a feature'],
    'field rule' => [FieldRuleDefaults::class, 'rule', 'required'],
    'flag' => [FlagDefaults::class, 'data-gradient', 'linear'],
    'pointer' => [PointerDefaults::class, 'target', '#a-target'],
    'style validation' => [StyleValidationDefaults::class, 'field_error', 'my-error-class'],
    'tutorial' => [TutorialDefaults::class, 'videos', [['id' => 'abc123']]],
    'video' => [VideoDefaults::class, 'id', 'abc123'],
]);

test('restrict() drops anything it does not know about', function (string $class, string $key, $value) {
    // The boundary: these arrays come from shortcode attributes, block JSON, addon registrations
    // and API responses. An unknown key that survives restrict() is an unfiltered value loose in
    // the plugin.
    $restricted = glsr($class)->restrict([
        $key => $value,
        'not_a_real_key' => '<script>alert(1)</script>',
    ]);

    expect($restricted)->toHaveKey($key)
        ->and($restricted)->not->toHaveKey('not_a_real_key');
    expect((string) wp_json_encode($restricted))->not->toContain('alert(1)');
})->with('defaults');

test('restrict() fills in every key the caller did not give it', function (string $class, string $key, $value) {
    // The other half of the contract: the output has every key, so nothing downstream has to
    // check. Half a Defaults class's value is the `??` it saves in fifty other files.
    $defaults = glsr($class)->defaults();
    $restricted = glsr($class)->restrict([]);

    expect(array_keys($restricted))->toEqualCanonicalizing(array_keys($defaults));
})->with('defaults');

test('merge() keeps what it does not know about', function (string $class, string $key, $value) {
    // The deliberate opposite of restrict(): merge() extends a structure the plugin OWNS (an addon
    // adding a key to a pointer); restrict() filters one it received.
    $merged = glsr($class)->merge(['an_extra_key' => 'kept']);

    expect($merged)->toHaveKey('an_extra_key')
        ->and($merged)->toHaveKey($key); // …and the defaults are still there
})->with('defaults');

/*
 * Two worth stating outright.
 */

test('a pointer points at the review screen unless it is told otherwise', function () {
    // The admin pointers are the "this is new" bubbles. One defaulting to no screen would appear
    // nowhere, or worse, on every admin screen.
    expect(glsr(PointerDefaults::class)->defaults()['screen'])->toBe(glsr()->post_type);
    expect(glsr(PointerDefaults::class)->defaults()['position'])
        ->toBe(['edge' => 'right', 'align' => 'middle']);
});

test('the validation classes are the ones the frontend javascript looks for', function () {
    // These names are a contract with the compiled JS: renaming one here but not there breaks the
    // form's error display silently.
    $classes = glsr(StyleValidationDefaults::class)->defaults();

    expect($classes['field_error'])->toBe('glsr-field-is-invalid')
        ->and($classes['form_message_failed'])->toBe('glsr-form-failed')
        ->and($classes['input_valid'])->toBe('glsr-is-valid');
});

/*
 * Two one-liners nothing else covers.
 */

test('a telephone field asks to be validated as a telephone number', function () {
    // The `validation` key tells the frontend JS which validator to run. A tel field asking for
    // none would accept anything.
    $field = new \GeminiLabs\SiteReviews\Modules\Html\Field(['name' => 'phone', 'type' => 'tel']);

    expect((new Tel($field))->required())->toBe(['validation' => 'tel']);
});

test('a post id can be given as an id, a slug, or a post', function () {
    // SanitizePostId turns `assigned_posts="my-page"` into an id — a shortcode is written by hand
    // by someone who does not know the id.
    $postId = createPost(['post_name' => 'the-reviewed-page', 'post_type' => 'page']);

    expect(glsr(Sanitizer::class)->sanitizePostId($postId))->toBe($postId)
        ->and(glsr(Sanitizer::class)->sanitizePostId('page:the-reviewed-page'))->toBe($postId)
        ->and(glsr(Sanitizer::class)->sanitizePostId('not-a-post'))->toBe(0)
        ->and(glsr(Sanitizer::class)->sanitizePostId(''))->toBe(0);
});
