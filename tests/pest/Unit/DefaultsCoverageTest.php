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
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
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
 * The abstract machinery itself, driven through real subclasses.
 */

test('a method that is not part of the contract is logged and changes nothing', function () {
    $values = ['id' => 'kept-exactly'];

    expect(glsr(AddonDefaults::class)->nonsense($values))->toBe($values);
});

test('asking for a property that is not public gets nothing, not a leak', function () {
    expect(glsr(AddonDefaults::class)->property('defaults'))->toBe([]) // protected
        ->and(glsr(AddonDefaults::class)->property('no_such_property'))->toBe([]); // missing, logged
    expect(glsr(AddonDefaults::class)->property('sanitize'))->not->toBeEmpty(); // public: filtered through
});

test('a concatenated key concatenates strings and leaves everything else alone', function () {
    // StyleClassesDefaults concatenates `form`: a given class is ADDED to the default classes,
    // not substituted for them. A non-string value cannot be concatenated and passes through.
    $defaults = glsr(\GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults::class)->defaults();

    $merged = glsr(\GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults::class)->merge(['form' => 'my-form']);
    expect($merged['form'])->toBe(trim($defaults['form'].' my-form'));

    // a non-string skips concatenation, and the attr-class sanitizer then scrubs it
    $merged = glsr(\GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults::class)->merge(['form' => 123]);
    expect($merged['form'])->toBe('');
});

test('unmapKeys puts the old key names back', function () {
    // The inverse of mapKeys, for code that must hand values back in the shape it received
    // them (ReviewDefaults maps ID onto rating_id on the way in).
    $mapped = glsr(\GeminiLabs\SiteReviews\Defaults\ReviewDefaults::class)->property('mapped');
    [$old, $new] = [array_key_first($mapped), $mapped[array_key_first($mapped)]];

    $unmapped = protectedMethod(\GeminiLabs\SiteReviews\Defaults\ReviewDefaults::class, 'unmapKeys')
        ->invoke(glsr(\GeminiLabs\SiteReviews\Defaults\ReviewDefaults::class), [$new => 'the-value']);

    expect($unmapped)->toHaveKey($old)
        ->and($unmapped[$old])->toBe('the-value')
        ->and($unmapped)->not->toHaveKey($new);
});

/*
 * The two subclasses with logic of their own.
 */

test('an addon card labels itself by its beta flag', function () {
    $beta = glsr(AddonDefaults::class)->merge(['beta' => true, 'title' => 'My Addon']);
    expect($beta['link_text'])->toBe('Premium members only')
        ->and($beta['title'])->toBe('My Addon (beta)');

    $stable = glsr(AddonDefaults::class)->merge(['title' => 'My Addon']);
    expect($stable['link_text'])->toBe('View addon')
        ->and($stable['title'])->toBe('My Addon');

    $custom = glsr(AddonDefaults::class)->merge(['link_text' => 'Buy now', 'title' => 'My Addon']);
    expect($custom['link_text'])->toBe('Buy now'); // an explicit label is not overwritten
});

test('a lifetime licence gets an expiry date far enough away', function () {
    $deactivated = glsr(\GeminiLabs\SiteReviews\Defaults\Updater\DeactivateLicenseDefaults::class)
        ->merge(['expires' => 'lifetime', 'license' => 'abc123', 'success' => '1']);

    expect($deactivated['success'])->toBeTrue()
        ->and(strtotime($deactivated['expires']))->toBeGreaterThan(strtotime('+9 years'));

    $dated = glsr(\GeminiLabs\SiteReviews\Defaults\Updater\DeactivateLicenseDefaults::class)
        ->merge(['expires' => '2030-01-01 00:00:00']);
    expect($dated['expires'])->toBe('2030-01-01 00:00:00'); // a real date passes through
});

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

/*
 * The finalize/normalize branches of the specific Defaults nothing else reaches.
 */

test('a discord embed color that does not parse is sent as no color', function () {
    $valid = glsr(\GeminiLabs\SiteReviews\Defaults\DiscordDefaults::class)->restrict(['color' => '#FAF089']);
    expect($valid['color'])->toBe(hexdec('FAF089'));

    $invalid = glsr(\GeminiLabs\SiteReviews\Defaults\DiscordDefaults::class)->restrict(['color' => 'not-a-color']);
    expect($invalid['color'])->toBe('');
});

test('updating a review maps is_approved onto the post status', function () {
    $approved = glsr(\GeminiLabs\SiteReviews\Defaults\UpdateReviewDefaults::class)->restrict(['is_approved' => true]);
    expect($approved['status'])->toBe('publish');

    $unapproved = glsr(\GeminiLabs\SiteReviews\Defaults\UpdateReviewDefaults::class)->restrict(['is_approved' => false]);
    expect($unapproved['status'])->toBe('pending');
});

test('an addon tested up to the same minor as this wordpress reads as tested on it', function (string $class) {
    // '6.8' claimed against 6.8.2 running: the claim is widened to the exact
    // version so the plugins screen does not warn about a compatible addon.
    global $wp_version;
    $minor = \GeminiLabs\SiteReviews\Helper::version($wp_version, 'minor');

    $values = glsr($class)->restrict(['tested' => $minor]);
    expect($values['tested'])->toBe($wp_version);

    // and when the global is momentarily empty, WordPress is asked directly —
    // which reads the same (emptied) global, so the claim survives as given
    $realVersion = $wp_version;
    $wp_version = '';
    try {
        $values = glsr($class)->restrict(['tested' => $minor]);
        expect($values['tested'])->toBe($minor);
    } finally {
        $wp_version = $realVersion;
    }
})->with([
    \GeminiLabs\SiteReviews\Defaults\Updater\VersionDetailsDefaults::class,
    \GeminiLabs\SiteReviews\Defaults\Updater\VersionUpdateDefaults::class,
    \GeminiLabs\SiteReviews\Defaults\Updater\VersionDefaults::class,
]);

test('an activated or checked lifetime licence gets a far-away expiry too', function (string $class) {
    $values = glsr($class)->merge(['expires' => 'lifetime', 'license' => 'abc123', 'success' => '1']);

    expect(strtotime($values['expires']))->toBeGreaterThan(strtotime('+9 years'));
})->with([
    \GeminiLabs\SiteReviews\Defaults\Updater\ActivateLicenseDefaults::class,
    \GeminiLabs\SiteReviews\Defaults\Updater\CheckLicenseDefaults::class,
]);

test('the deprecated assignment keys map onto the real ones, new winning over old', function (string $class) {
    // assigned_to is the old name for assigned_posts: alone it maps across, and
    // when both are given the new key wins and the old is discarded.
    $postId = createPost();

    $mapped = glsr($class)->restrict(['assigned_to' => $postId]);
    expect($mapped)->not->toHaveKey('assigned_to');
    if (\GeminiLabs\SiteReviews\Defaults\SiteReviewsFormDefaults::class !== $class) {
        // the form shortcode sanitizes assignments differently (ids resolve at submission)
        expect((array) $mapped['assigned_posts'])->toBe([$postId]);
    }
})->with([
    \GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults::class,
    \GeminiLabs\SiteReviews\Defaults\SiteReviewsSummaryDefaults::class,
    \GeminiLabs\SiteReviews\Defaults\SiteReviewsFormDefaults::class,
]);

test('an assigned post that is really a post type becomes a type filter', function () {
    $values = glsr(\GeminiLabs\SiteReviews\Defaults\ReviewsDefaults::class)->restrict([
        'assigned_posts' => ['page'],
    ]);

    expect($values['assigned_posts'])->toBe([])
        ->and($values['assigned_posts_types'])->toBe(['page']);
});

test('dataAttributes json-encodes a changed value that is not scalar', function () {
    // The data-attribute values feed strtr()/HTML attributes, so anything not scalar is JSON
    // encoded. Arrays are flattened to comma-strings before the diff, which leaves objects as
    // the non-scalar case — and the object must be diffable, hence __toString.
    $defaults = new class extends GeminiLabs\SiteReviews\Defaults\DefaultsAbstract {
        protected function defaults(): array
        {
            return ['options' => ''];
        }
    };
    $value = new class {
        public $a = 1;

        public function __toString(): string
        {
            return 'changed';
        }
    };

    expect($defaults->dataAttributes(['options' => $value]))->toBe(['data-options' => '{"a":1}']);
});
