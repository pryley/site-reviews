<?php

use GeminiLabs\SiteReviews\Modules\Validator;

/*
 * The size rules (min, max, between) measure a number by its value, a list by its count
 * and anything else by its length.
 */

function sizeErrors(string $rule, $value): array
{
    return glsr(Validator::class)->validate(['field' => $value], ['field' => $rule]);
}

test('a number is compared with a limit as a decimal', function () {
    expect(sizeErrors('number|max:1', '1.9'))->toHaveKey('field')
        ->and(sizeErrors('number|max:1', '1'))->toBe([])
        ->and(sizeErrors('number|min:1', '0.5'))->toHaveKey('field')
        ->and(sizeErrors('number|min:0.5', '0.5'))->toBe([])
        ->and(sizeErrors('number|between:1,2', '2.5'))->toHaveKey('field')
        ->and(sizeErrors('number|between:1,2', '1.5'))->toBe([])
        ->and(sizeErrors('number|min:-2', '-2.5'))->toHaveKey('field');
});

test('a list is measured by its count and text by its length', function () {
    expect(sizeErrors('max:2', ['a', 'b', 'c']))->toHaveKey('field')
        ->and(sizeErrors('max:2', ['a', 'b']))->toBe([])
        ->and(sizeErrors('max:3', 'abcd'))->toHaveKey('field')
        ->and(sizeErrors('max:3', 'abc'))->toBe([])
        ->and(sizeErrors('max:3', '1234'))->toHaveKey('field'); // no number rule: its length
});
