<?php

use GeminiLabs\SiteReviews\Modules\Validator;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The validation rules themselves — the ones a review form is checked against.
 *
 * The plugin's validator (a cut-down port of Laravel's, nine rules) decides whether a review is
 * accepted or handed back with a red box round a field, so both directions cost: too strict turns
 * away a person who had something to say, too loose lets a bot fill the reviews table.
 *
 * The one non-obvious piece is what `size` means. `min:3` on a name means THREE CHARACTERS; on a
 * rating, THREE STARS. Same rule, same syntax, two questions — decided by whether the field also
 * carries the `number` rule (Validator::$numericRules). Wrong, and `min:1` on a rating accepts any
 * rating, because "1" is one character long.
 */

beforeEach(function () {
    resetPluginState();
});

/**
 * The errors, keyed by field. An empty array is a submission that passed.
 */
function validationErrors(array $data, array $rules): array
{
    return glsr(Validator::class)->validate($data, $rules);
}

function passes(array $data, array $rules): bool
{
    return empty(validationErrors($data, $rules));
}

/*
 * Required.
 */

test('a field that was left blank is required to be filled in', function () {
    expect(passes(['name' => 'Jane'], ['name' => 'required']))->toBeTrue();

    expect(passes(['name' => ''], ['name' => 'required']))->toBeFalse();
    expect(passes(['name' => '   '], ['name' => 'required']))->toBeFalse();  // spaces are not a name
    expect(passes(['name' => null], ['name' => 'required']))->toBeFalse();
    expect(passes(['name' => []], ['name' => 'required']))->toBeFalse();
});

test('the string "[]" is empty, because that is what an empty form field posts', function () {
    // The multi-value fields (categories, assigned posts) post a JSON array, and an EMPTY one
    // arrives as the two-character string "[]". Without this it is a non-empty string, and a
    // required field would be satisfied by the person having chosen nothing.
    expect(passes(['terms' => '[]'], ['terms' => 'required']))->toBeFalse();
});

test('a zero is a real answer, and is not blank', function () {
    // "0" is falsy in PHP and is not empty here, deliberately: a rating of 0, a count of 0, an
    // assigned post id of 0 are all things somebody meant to say.
    expect(passes(['rating' => '0'], ['rating' => 'required']))->toBeTrue();
    expect(passes(['rating' => 0], ['rating' => 'required']))->toBeTrue();
});

/*
 * Accepted — the terms checkbox.
 */

test('the terms are accepted only by the values a checkbox can actually send', function () {
    foreach (['yes', 'on', '1', 1, true, 'true'] as $accepted) {
        expect(passes(['terms' => $accepted], ['terms' => 'accepted']))->toBeTrue();
    }
});

test('and an unticked box is not acceptance, however it arrives', function () {
    // This is a record of CONSENT. It is a strict comparison against a known list precisely so that
    // nothing can be mistaken for a yes — not '0', not 'no', not an empty string, and not the
    // string "false", which is truthy in PHP and would otherwise sail through.
    foreach (['0', 0, 'no', 'off', '', 'false', false, null] as $notAccepted) {
        expect(passes(['terms' => $notAccepted], ['terms' => 'accepted']))
            ->toBeFalse("[{$notAccepted}] must not count as accepting the terms");
    }
});

/*
 * Size — which means two different things.
 */

test('min and max on a NUMBER are about its value', function () {
    // A rating of 1 to 5. `number` is what makes `min:1|max:5` mean "between one star and five"
    // rather than "between one character and five characters".
    $rules = ['rating' => 'number|min:1|max:5'];

    expect(passes(['rating' => '3'], $rules))->toBeTrue();
    expect(passes(['rating' => '0'], $rules))->toBeFalse();
    expect(passes(['rating' => '6'], $rules))->toBeFalse();
});

test('and min and max on a STRING are about its length', function () {
    // The same words, the same syntax, a different question. `min:5` on a review's content means
    // five characters — and if `number` were ever added to that field by mistake, a review of "9"
    // would pass, because 9 is greater than 5.
    $rules = ['content' => 'min:5|max:10'];

    expect(passes(['content' => 'Lovely!'], $rules))->toBeTrue();
    expect(passes(['content' => 'No'], $rules))->toBeFalse();
    expect(passes(['content' => 'Far too long to fit'], $rules))->toBeFalse();
});

test('a length is counted in CHARACTERS, not in bytes', function () {
    // mb_strlen. A review written in Japanese, or a name with an accent in it, must not be refused
    // for being too long because its characters take more than one byte each.
    expect(passes(['name' => 'Łukasz'], ['name' => 'max:6']))->toBeTrue();
    expect(passes(['name' => 'おはよう'], ['name' => 'min:4|max:4']))->toBeTrue();
});

test('between is inclusive at both ends', function () {
    $rules = ['rating' => 'number|between:1,5'];

    expect(passes(['rating' => 1], $rules))->toBeTrue()
        ->and(passes(['rating' => 5], $rules))->toBeTrue()
        ->and(passes(['rating' => 6], $rules))->toBeFalse();
});

/*
 * The rest of the rules.
 */

test('an email address has to look like one', function () {
    expect(passes(['email' => 'jane@example.org'], ['email' => 'email']))->toBeTrue();
    expect(passes(['email' => 'jane@'], ['email' => 'email']))->toBeFalse();
    expect(passes(['email' => 'not an email'], ['email' => 'email']))->toBeFalse();
});

test('a telephone number is anything a person might plausibly type', function () {
    // Four to fifteen digits, and the punctuation people actually use. Phone numbers are written a
    // hundred different ways around the world, and a form that refuses a real one is a form that
    // loses a review.
    foreach (['+44 (0)20 7946 0958', '555-1234', '(02) 9876 5432', '+1 555 123 4567'] as $number) {
        expect(passes(['phone' => $number], ['phone' => 'tel']))->toBeTrue("[{$number}] is a phone number");
    }
});

test('and is not a sentence, or three digits, or twenty', function () {
    expect(passes(['phone' => 'call me'], ['phone' => 'tel']))->toBeFalse()
        ->and(passes(['phone' => '123'], ['phone' => 'tel']))->toBeFalse()          // too few digits
        ->and(passes(['phone' => '1234567890123456'], ['phone' => 'tel']))->toBeFalse(); // too many
});

test('a url has to have a protocol on it', function () {
    expect(passes(['url' => 'https://example.org/a/page?q=1#top'], ['url' => 'url']))->toBeTrue()
        ->and(passes(['url' => 'http://192.168.0.1:8080/'], ['url' => 'url']))->toBeTrue()
        ->and(passes(['url' => 'example.org'], ['url' => 'url']))->toBeFalse()
        ->and(passes(['url' => 'javascript:alert(1)'], ['url' => 'url']))->toBeFalse();
});

test('a number is a number', function () {
    expect(passes(['rating' => '4'], ['rating' => 'number']))->toBeTrue()
        ->and(passes(['rating' => '4.5'], ['rating' => 'number']))->toBeTrue()
        ->and(passes(['rating' => 'four'], ['rating' => 'number']))->toBeFalse();
});

test('a regex rule matches a pattern', function () {
    expect(passes(['code' => 'ABC-123'], ['code' => 'regex:/^[A-Z]{3}-\d{3}$/']))->toBeTrue()
        ->and(passes(['code' => 'nope'], ['code' => 'regex:/^[A-Z]{3}-\d{3}$/']))->toBeFalse();
});

test('a rule that needs a value and was not given one is a bug, and says so', function () {
    // `min` with no number after it is a mistake in the plugin's own config, not a mistake by the
    // person filling in the form. It throws rather than quietly passing everything — because a
    // `min` that always passed is a validation rule that is not there.
    expect(fn () => validationErrors(['name' => 'Jane'], ['name' => 'min']))
        ->toThrow(InvalidArgumentException::class);

    expect(fn () => validationErrors(['rating' => 3], ['rating' => 'between:1']))
        ->toThrow(InvalidArgumentException::class);
});

/*
 * What the person is told.
 */

test('the error message names the limit it was given', function () {
    // "This field is too long" is not an error message. The number has to be in it, or the person
    // is left guessing how much to cut.
    $errors = validationErrors(['content' => 'No'], ['content' => 'min:5']);

    expect($errors)->toHaveKey('content');
    expect(implode(' ', (array) $errors['content']))->toContain('5');
});
