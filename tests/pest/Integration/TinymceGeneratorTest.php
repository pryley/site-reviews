<?php

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Tinymce\TinymceGenerator;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The TinyMCE popup generator: an addon describes its shortcode options as
 * field arrays, and this turns them into the popup's field definitions — or,
 * when a required option cannot be offered, into an error the popup shows
 * instead. The real generators are covered via TinymceController; these are
 * the validation branches only a malformed or demanding field list reaches.
 */

beforeEach(fn () => resetPluginState());

/**
 * A generator whose field list the test dictates. getFields() is exercised
 * directly (via reflection) rather than through register(), which would
 * overwrite the real popup definitions in plugin storage.
 */
function tinymceHarness(array $fields): TinymceGenerator
{
    $generator = new class extends TinymceGenerator {
        public array $testFields = [];

        public function fields(): array
        {
            return $this->testFields;
        }

        public function shortcode(): ShortcodeContract
        {
            return glsr(SiteReviewsShortcode::class);
        }
    };
    $generator->testFields = $fields;
    $generator->shortcode = $generator->shortcode();

    return $generator;
}

function tinymceProtected(TinymceGenerator $generator, string $property): array
{
    $prop = new ReflectionProperty(TinymceGenerator::class, $property);
    $prop->setAccessible(true);

    return $prop->getValue($generator);
}

test('junk fields are dropped: empty, nameless, and a nameless listbox alike', function () {
    $generator = tinymceHarness([
        [],                     // nothing at all
        ['type' => 'textbox'],  // no name to submit under
        ['type' => 'listbox'],  // ditto, via the listbox normalizer
    ]);

    $fields = protectedMethod(get_class($generator), 'getFields')->invoke($generator);

    expect($fields)->toBe([]);
});

test('a required option that cannot be rendered turns the popup into its error', function () {
    // Two declarations of the same option: one marks it required with an alert,
    // the other carries the error markup to show when the option has no field.
    // Neither survives as a field, so getFields() hands back the error instead.
    $generator = tinymceHarness([
        ['name' => 'x', 'type' => 'textbox', 'required' => ['alert' => 'Pick one']],
        ['name' => 'x', 'type' => 'textbox', 'required' => ['error' => '<p>You must configure X first.</p>']],
    ]);

    $fields = protectedMethod(get_class($generator), 'getFields')->invoke($generator);

    expect($fields)->toHaveCount(1)
        ->and($fields[0]['type'])->toBe('container')
        ->and($fields[0]['html'])->toBe('<p>You must configure X first.</p>');
});

test('a required field without its option gets an alert, worded from its label if it has one', function () {
    $generator = tinymceHarness([
        ['name' => 'unlabeled', 'type' => 'textbox', 'required' => true],
        ['name' => 'labeled', 'label' => 'The Label:', 'type' => 'textbox', 'required' => true],
        ['name' => 'ok', 'type' => 'textbox'],
    ]);

    $fields = protectedMethod(get_class($generator), 'getFields')->invoke($generator);

    // required fields are withheld from the popup; only the ordinary one renders
    expect($fields)->toHaveCount(1)
        ->and($fields[0]['name'])->toBe('ok');

    $required = tinymceProtected($generator, 'required');
    expect($required['unlabeled'])->toBe('Some of the shortcode options are required.')
        ->and($required['labeled'])->toBe('The "The Label" option is required.');
});
