<?php

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueType;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\FieldElements\Secret;
use GeminiLabs\SiteReviews\Modules\Html\FieldElements\UnknownElement;
use GeminiLabs\SiteReviews\Modules\Html\SettingField;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewVerifiedTag;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Four small pieces of markup, each the only place its thing is shown.
 *
 *   Secret          the licence-key box on the settings page. A key is secret, and also forty
 *                   characters of gibberish to check you pasted correctly — so it is masked, with a
 *                   reveal button, as WordPress does its own password fields.
 *   UnknownElement  the form builder's fallback for an undeclared field type.
 *   ReviewVerified  the "Verified" badge under a review.
 *   ColumnValueType where a review came from, in the list table — with the platform's logo, for
 *                   sites that import from Google, Facebook, Trustpilot and the rest.
 */

beforeEach(function () {
    resetPluginState();
});

/*
 * The licence key box.
 */

test('a secret is not spellchecked, not autocompleted, and not a password field', function () {
    // `type=text`, deliberately, not `type=password`: a browser that thinks this is a password
    // offers to SAVE it, autofills it with the person's WordPress password, and prompts about it on
    // every settings save. Which is why WordPress's own "show password" fields work the same way.
    expect((new Secret(new SettingField(['name' => 'licence', 'type' => 'secret'])))->required())
        ->toBe([
            'autocomplete' => 'off',
            'spellcheck' => 'false',
            'type' => 'text',
        ]);
});

test('a licence key that has been entered gets a button to reveal it', function () {
    // Forty characters of gibberish. Somebody who has just pasted one needs to be able to check it,
    // and somebody looking at a screenshare needs it not to be on screen by default.
    $html = (new SettingField([
        'name' => 'licence',
        'type' => 'secret',
        'value' => 'a-real-licence-key',
    ]))->build();

    expect($html)->toContain('wp-pwd')          // WordPress's own wrapper, so it looks native
        ->toContain('wp-hide-pw')
        ->toContain('Show value')
        ->toContain('a-real-licence-key');
});

test('and an empty one does not, because there is nothing to reveal', function () {
    // A reveal button over an empty box is a button that does nothing, on the settings page of
    // every site that has not bought an addon — which is most of them.
    $html = (new SettingField(['name' => 'licence', 'type' => 'secret', 'value' => '']))->build();

    expect($html)->not->toContain('wp-pwd')
        ->not->toContain('Show value');
});

/*
 * A field type nobody declared.
 */

test('a field of a type the plugin knows nothing about is still rendered, if a browser knows it', function () {
    // There is no Color class, no Range class, no Date class. UnknownElement is what catches them:
    // if the type is one of the HTML input types (Attributes::INPUT_TYPES) it is an <input> with
    // that type, and the browser does the rest. An addon adding a `color` field needs no class in
    // the parent at all.
    $field = new Field(['name' => 'colour', 'type' => 'color', 'value' => '#ff0000']);
    $element = new UnknownElement($field);

    expect($element->tag())->toBe('input');
    expect($element->build())->toContain('type="color"');
});

test('and a type that is not an input type at all renders nothing, rather than nonsense', function () {
    // `<banana>` is not an element, and no browser will do anything useful with one. The field
    // marks itself INVALID, which is how a typo in an addon's field config costs it that one field
    // rather than the whole settings page.
    $field = new Field(['name' => 'nonsense', 'type' => 'banana']);
    $element = new UnknownElement($field);

    expect($element->tag())->toBe('');
    expect($element->build())->toBe('');

    $element->merge();

    expect($field->is_valid)->toBeFalse();
});

/*
 * The verified badge.
 */

test('a verified review wears the badge', function () {
    // The whole point of the verification email: this badge, and the trust it is meant to carry.
    $review = createReview(['is_verified' => true]);

    $rendered = (new ReviewVerifiedTag('verified'))->handleFor('review', '', $review);

    expect($rendered)->toContain('Verified')
        ->toContain('<svg');
});

test('and a review that was never verified does not', function () {
    // A badge shown on an unverified review is a lie told to every visitor who reads it.
    $review = createReview();
    expect($review->is_verified)->toBeFalse();

    expect((new ReviewVerifiedTag('verified'))->handleFor('review', '', $review))->toBe('');
});

/*
 * Where a review came from.
 */

test('a review the site wrote itself just says so', function () {
    // No logo. Every review on nearly every site is local, and a column full of identical icons is
    // a column of noise.
    $review = createReview();

    expect(glsr(ColumnValueType::class)->handle($review))->toBe($review->type());
});

/**
 * A review that came from somewhere else.
 *
 * NOT `$review->type = 'google'` — Review is an Arguments, and assigning to it does not change the
 * stored row, so the model goes on reporting `local` and the test asserts nothing. The type lives
 * in the ratings table, and the way to change it is the way the import addons change it.
 */
function reviewOfType(string $type, string $label): \GeminiLabs\SiteReviews\Review
{
    glsr()->store('review_types', ['local' => 'Local Review', $type => $label]);
    $review = createReview();
    glsr(ReviewManager::class)->updateRating($review->ID, ['type' => $type]);

    return glsr_get_review($review->ID);
}

test('an imported review is shown with the logo of the place it came from', function () {
    // Which is the entire value of the column: a site with reviews from Google, Facebook and
    // Trustpilot in one table needs to tell them apart at a glance, and the names are long.
    $review = reviewOfType('google', 'Google Review');

    $rendered = glsr(ColumnValueType::class)->handle($review);

    expect($rendered)->toContain('<svg')
        ->toContain('Google Review');
});

test('and a platform with no logo on disk falls back to its name', function () {
    // There are logos for the platforms the import addons support, and an addon can add a review
    // type without adding a logo for it. A missing file is a name, not a broken image.
    $review = reviewOfType('nowhere', 'Nowhere Reviews');

    expect(glsr(ColumnValueType::class)->handle($review))->toBe('Nowhere Reviews');
});
