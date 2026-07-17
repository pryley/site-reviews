<?php

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Tests\MockClass;

uses()->group('plugin');

test('to', function () {
    expect(is_array(Cast::to('array', '')))->toBeTrue();
    expect(is_bool(Cast::to('bool', '')))->toBeTrue();
    expect(is_float(Cast::to('float', '')))->toBeTrue();
    expect(is_int(Cast::to('int', '12.3')))->toBeTrue();
    expect(is_object(Cast::to('object', '')))->toBeTrue();
    expect(is_string(Cast::to('string', [])))->toBeTrue();
    expect(Cast::to('xyz', 'abc'))->toEqual('abc');
});

test('to array', function () {
    expect(Cast::toArray(''))->toEqual([]);
    expect(Cast::toArray('abc'))->toEqual(['abc']);
    expect(Cast::toArray('a,b,c'))->toEqual(['a', 'b', 'c']);
    expect(Cast::toArray('a,b,c', false))->toEqual(['a,b,c']);
    expect(Cast::toArray(true))->toEqual([true]);
    expect(Cast::toArray(false))->toEqual([false]);
    expect(Cast::toArray(1))->toEqual([1]);
    expect(Cast::toArray([1]))->toEqual([1]);
    expect(Cast::toArray((object) ['a' => 123]))->toEqual(['a' => 123]);
});

test('to bool', function () {
    expect(Cast::toBool(''))->toBeFalse();
    expect(Cast::toBool(0))->toBeFalse();
    expect(Cast::toBool('0'))->toBeFalse();
    expect(Cast::toBool([]))->toBeFalse();
    expect(Cast::toBool([1]))->toBeFalse();
    expect(Cast::toBool(1))->toBeTrue();
    expect(Cast::toBool('1'))->toBeTrue();
    expect(Cast::toBool('true'))->toBeTrue();
});

test('to float', function () {
    expect(Cast::toFloat(''))->toEqual(0);
    expect(Cast::toFloat([]))->toEqual(0);
    expect(Cast::toFloat('abc'))->toEqual(0);
    expect(Cast::toFloat('123.123'))->toEqual(123.123);
    expect(Cast::toFloat(123))->toEqual(123);
    expect(Cast::toFloat(123.123))->toEqual(123.123);
});

test('to int', function () {
    expect(Cast::toInt(''))->toEqual(0);
    expect(Cast::toInt([]))->toEqual(0);
    expect(Cast::toInt('abc'))->toEqual(0);
    expect(Cast::toInt('123.123'))->toEqual(123);
    expect(Cast::toInt('123'))->toEqual(123);
    expect(Cast::toInt(123.123))->toEqual(123);
});

test('to object', function () {
    expect(Cast::toObject(''))->toEqual((object) []);
    expect(Cast::toObject((object) []))->toEqual((object) []);
});

test('to string', function () {
    expect(Cast::toString([]))->toEqual('');
    expect(Cast::toString(123))->toEqual('123');
    expect(Cast::toString([123]))->toEqual('123');
    expect(Cast::toString([123], false))->toEqual('123');
    expect(Cast::toString([1,2,3], false))->toEqual('1, 2, 3');
    expect(Cast::toString(['a' => 1, 'b' => 2, 'c' => 3], false))->toEqual('a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}');
    expect(Cast::toString(new MockClass()))->toEqual('123');
});

test('a value json cannot represent degrades to an empty array, logged', function () {
    // INF survives the object walk but not json_encode; the deep cast must not
    // let a JsonException out of a helper that everything calls casually.
    expect(\GeminiLabs\SiteReviews\Helpers\Cast::toArrayDeep([INF]))->toBe([]);
});
