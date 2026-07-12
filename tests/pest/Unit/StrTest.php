<?php

use GeminiLabs\SiteReviews\Helpers\Str;

uses()->group('plugin');

test('camel case', function () {
    expect(Str::camelCase('a-b_cDE'))->toEqual('ABCDE');
    expect(Str::camelCase('aaa-bbb_cde'))->toEqual('AaaBbbCde');
});

test('contains', function () {
    expect(Str::contains('abcdef', ['cd']))->toBeTrue();
    expect(Str::contains('abcdef', ['abcdef']))->toBeTrue();
    expect(Str::contains('abcdef', ['']))->toBeFalse();
    expect(Str::contains('abcdef', ['z']))->toBeFalse();
    expect(Str::contains('abcdef', []))->toBeFalse();
});

test('convert name to id', function () {
    expect(Str::convertNameToId('a--b'))->toEqual('a-b');
    expect(Str::convertNameToId('a_b_c'))->toEqual('a_b_c');
    expect(Str::convertNameToId('a.b.c'))->toEqual('a-b-c');
    expect(Str::convertNameToId('a[b][c]'))->toEqual('a-b-c');
    expect(Str::convertNameToId('a[b][c][]'))->toEqual('a-b-c');
    expect(Str::convertNameToId('b.c', 'a'))->toEqual('a-b-c');
    expect(Str::convertNameToId('b[c]', 'a'))->toEqual('a-b-c');
    expect(Str::convertNameToId('b[c][]', 'a'))->toEqual('a-b-c');
    expect(Str::convertNameToId('[b][c][]', 'a'))->toEqual('a-b-c');
    expect(Str::convertNameToId('[b][c-d]', 'a'))->toEqual('a-b-c-d');
});

test('convert name to path', function () {
    expect(Str::convertNameToPath('a-b'))->toEqual('a-b');
    expect(Str::convertNameToPath('a_b_c'))->toEqual('a_b_c');
    expect(Str::convertNameToPath('a.b.c'))->toEqual('a.b.c');
    expect(Str::convertNameToPath('a[b][c]'))->toEqual('a.b.c');
    expect(Str::convertNameToPath('a[b][c][]'))->toEqual('a.b.c');
    expect(Str::convertNameToPath('b.c'))->toEqual('b.c');
    expect(Str::convertNameToPath('b[c]'))->toEqual('b.c');
    expect(Str::convertNameToPath('b[c][]'))->toEqual('b.c');
    expect(Str::convertNameToPath('[b][c][]'))->toEqual('b.c');
    expect(Str::convertNameToPath('[b][c-d]'))->toEqual('b.c-d');
});

test('convert path to name', function () {
    expect(Str::convertPathToName('a'))->toEqual('a');
    expect(Str::convertPathToName('a.b.c'))->toEqual('a[b][c]');
    expect(Str::convertPathToName('b.c', 'a'))->toEqual('a[b][c]');
    expect(Str::convertPathToName('b.c.', 'a'))->toEqual('a[b][c]');
    expect(Str::convertPathToName('.b.c', 'a'))->toEqual('a[b][c]');
    expect(Str::convertPathToName('.b.c.', 'a'))->toEqual('a[b][c]');
});

test('dash case', function () {
    expect(Str::dashCase('a-b_cDE'))->toEqual('a-b-c-d-e');
    expect(Str::dashCase('GeminiLabs\SiteReviews\Helpers\Str'))->toEqual('gemini-labs\site-reviews\helpers\str');
});

test('ends with', function () {
    expect(Str::endsWith('abcdefg', ['efg']))->toBeTrue();
    expect(Str::endsWith('ABCDEFG', ['efg']))->toBeFalse();
    expect(Str::endsWith('ABCDEFG', ['']))->toBeFalse();
    expect(Str::endsWith('ABCDEFG', []))->toBeFalse();
});

test('fallback', function () {
    expect(Str::fallback('1', '2'))->toEqual('1');
    expect(Str::fallback('', '2'))->toEqual('2');
    expect(Str::fallback(1, '2'))->toEqual('1');
    expect(Str::fallback([], '2'))->toEqual('2');
});

test('hash', function () {
    require_once ABSPATH.WPINC.'/pluggable.php';
    $hash = wp_hash('123', 'nonce');
    expect(Str::hash('123'))->toEqual($hash);
    expect(Str::hash('123', 0))->toEqual(substr($hash, 0, 8));
    expect(Str::hash('123', 8))->toEqual(substr($hash, 0, 8));
    expect(Str::hash('123', 12))->toEqual(substr($hash, 0, 12));
});

test('join', function () {
    expect(Str::join(['1']))->toEqual('1');
    expect(Str::join(['1'], true))->toEqual("'1'");
    expect(Str::join(['1', '2']))->toEqual('1, 2');
    expect(Str::join(['1', '2'], true))->toEqual("'1','2'");
});

test('mask', function () {
    $string = 'abcdefghijklmnopqrstuvwxyz';
    expect(Str::mask($string))->toEqual('*************');
    expect(Str::mask($string, 4))->toEqual('abcd*********');
    expect(Str::mask($string, 0, 4))->toEqual('*********wxyz');
    expect(Str::mask($string, 4, 4))->toEqual('abcd*****wxyz');
    expect(Str::mask($string, 4, 4, 20))->toEqual('abcd************wxyz');
    expect(Str::mask($string, 4, 4, 2))->toEqual($string);
    expect(Str::mask($string, 20, 20))->toEqual($string);
    expect(Str::mask($string, 40, 0))->toEqual($string);
    expect(Str::mask($string, 0, 40))->toEqual($string);
    expect(Str::mask($string, -10, 40))->toEqual($string);
});

test('natural join', function () {
    expect(Str::naturalJoin(['1']))->toEqual('1');
    expect(Str::naturalJoin(['1', '2']))->toEqual('1 and 2');
    expect(Str::naturalJoin(['1', '2', '3']))->toEqual('1, 2 and 3');
    expect(Str::naturalJoin(['1', '2', '3', '4']))->toEqual('1, 2, 3 and 4');
});

test('prefix', function () {
    expect(Str::prefix(' bob ', 'hello_'))->toEqual('hello_bob');
});

test('random', function () {
    expect(strlen(Str::random(4)))->toEqual(4);
    expect(strlen(Str::random(8)))->toEqual(8);
});

test('remove prefix', function () {
    expect(Str::removePrefix('_abc', '_'))->toEqual('abc');
    expect(Str::removePrefix('_abc', ''))->toEqual('_abc');
});

test('replace first', function () {
    expect(Str::replaceFirst('', 'xyz', 'abcabc'))->toEqual('abcabc');
    expect(Str::replaceFirst('zzx', 'xyz', 'abcabc'))->toEqual('abcabc');
    expect(Str::replaceFirst('abc', 'xyz', 'abcabc'))->toEqual('xyzabc');
});

test('replace last', function () {
    expect(Str::replaceLast('', 'xyz', 'abcabc'))->toEqual('abcabc');
    expect(Str::replaceLast('abc', 'xyz', 'abcabc'))->toEqual('abcxyz');
    expect(Str::replaceLast('zzz', 'xyz', 'abcabc'))->toEqual('abcabc');
});

test('restrict to', function () {
    expect(Str::restrictTo('asc,desc,', 'ASC', 'DESC'))->toEqual('ASC');
    expect(Str::restrictTo('asc,desc,', 'ASC', 'DESC', true))->toEqual('DESC');
});

test('snake case', function () {
    expect(Str::snakeCase('a-b_cDE'))->toEqual('a_b_c_d_e');
    expect(Str::snakeCase('GeminiLabs\SiteReviews\Helpers\Str'))->toEqual('gemini_labs\site_reviews\helpers\str');
    expect(Str::snakeCase('_GeminiLabs\_SiteReviews'))->toEqual('_gemini_labs\_site_reviews');
});

test('starts with', function () {
    expect(Str::startsWith('abcdefg', ['abc']))->toBeTrue();
    expect(Str::startsWith('defg', ['abc', 'def']))->toBeTrue();
    expect(Str::startsWith('ABCDEFG', ['abc']))->toBeFalse();
    expect(Str::startsWith('ABCDEFG', ['']))->toBeFalse();
    expect(Str::startsWith('ABCDEFG', []))->toBeFalse();
});

test('suffix', function () {
    expect(Str::suffix('bob', '_goodbye'))->toEqual('bob_goodbye');
    expect(Str::suffix('bob_goodbye', '_goodbye'))->toEqual('bob_goodbye');
    expect(Str::suffix(' bob ', '_goodbye'))->toEqual(' bob _goodbye');
    expect(Str::suffix(' bob ', ' _goodbye '))->toEqual(' bob  _goodbye ');
});

test('truncate', function () {
    expect(Str::truncate('abc', 1))->toEqual('a');
    expect(Str::truncate('abc', 2))->toEqual('ab');
    expect(Str::truncate('abc', 3))->toEqual('abc');
    expect(Str::truncate('abc', 4))->toEqual('abc');
});

test('wp case', function () {
    expect(Str::wpCase('a-b_cde'))->toEqual('A_B_Cde');
    expect(Str::wpCase('a-b_cDE'))->toEqual('A_B_C_D_E');
    expect(Str::wpCase('a-b_cdE'))->toEqual('A_B_Cd_E');
    expect(Str::wpCase('aaa-bbb_cde'))->toEqual('Aaa_Bbb_Cde');
});
