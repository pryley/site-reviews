<?php

use GeminiLabs\SiteReviews\Helpers\Arr;

uses()->group('plugin');

test('compare', function () {
    expect(Arr::compare(['one' => ['two']], ['one' => ['two']]))->toBeTrue();
    expect(Arr::compare(['one' => ['two']], ['one' => 'two']))->toBeFalse();
});

test('consolidate', function () {
    expect(Arr::consolidate(''))->toEqual([]);
    expect(Arr::consolidate('0'))->toEqual([]);
    expect(Arr::consolidate('1'))->toEqual([]);
    expect(Arr::consolidate('1,2'))->toEqual([]);
    expect(Arr::consolidate((object)[]))->toEqual([]);
    expect(Arr::consolidate(0))->toEqual([]);
    expect(Arr::consolidate(1))->toEqual([]);
    expect(Arr::consolidate([]))->toEqual([]);
    expect(Arr::consolidate(false))->toEqual([]);
    expect(Arr::consolidate(true))->toEqual([]);
    expect(Arr::consolidate((object)[1]))->toEqual([1]);
});

test('convert from string', function () {
    expect(Arr::convertFromString(',a,,b,c,1,,'))->toEqual(['a','b','c','1']);
});

test('flatten', function () {
    $test = ['one' => ['two' => ['three' => ['x', 'y', 'z']]]];
    expect(Arr::flatten($test))->toEqual(['one.two.three' => ['x', 'y', 'z']]);
    expect(Arr::flatten($test, true))->toEqual(['one.two.three' => '[x, y, z]']);
    expect(Arr::flatten($test, true, 'test'))->toEqual(['test.one.two.three' => '[x, y, z]']);
});

test('get', function () {
    $values1 = ['parent' => ['child' => 'toys']];
    $values2 = ['parent' => ['child' => (object) ['toys' => 123]]];
    expect(Arr::get($values1, 'parent.child'))->toEqual('toys');
    expect(Arr::get($values1, 'parent.child.toys', 'fallback'))->toEqual('fallback');
    expect(is_object(Arr::get($values2, 'parent.child')))->toBeTrue();
});

test('get as', function () {
    $values1 = ['parent' => ['child' => 'toys', 'number' => '2.3', 'version' => '2.0.0']];
    expect(Arr::getAs('array', $values1, 'parent.child'))->toEqual(['toys']);
    expect(Arr::getAs('bool', $values1, 'parent.child'))->toEqual(false);
    expect(Arr::getAs('float', $values1, 'parent.child'))->toEqual(0);
    expect(Arr::getAs('float', $values1, 'parent.number'))->toEqual(2.3);
    expect(Arr::getAs('float', $values1, 'parent.version'))->toEqual(0);
    expect(Arr::getAs('int', $values1, 'parent.child'))->toEqual(0);
    expect(Arr::getAs('int', $values1, 'parent.number'))->toEqual(2);
    expect(Arr::getAs('int', $values1, 'parent.version'))->toEqual(0);
    expect(Arr::getAs('object', $values1, 'parent.child'))->toEqual((object) ['toys']);
    expect(Arr::getAs('string', $values1, 'parent.child'))->toEqual('toys');
});

test('insert after', function () {
    $array1 = ['1','2','3'];
    $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
    expect(Arr::insertAfter(1, $array1, ['a','b']))->toEqual(['1','2','a','b','3']);
    expect(Arr::insertAfter(9, $array1, ['a','b']))->toEqual(['1','2','3','a','b']);
    expect(Arr::insertAfter('b', $array2, ['z' => 13]))->toEqual(['a' => 1, 'b' => 2, 'z' => 13, 'c' => 3]);
    expect(Arr::insertAfter('z', $array2, ['z' => 13]))->toEqual(['a' => 1, 'b' => 2, 'c' => 3, 'z' => 13]);
});

test('insert before', function () {
    $array1 = ['1','2','3'];
    $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
    expect(Arr::insertBefore(1, $array1, ['a','b']))->toEqual(['1','a','b','2','3']);
    expect(Arr::insertBefore(9, $array1, ['a','b']))->toEqual(['1','2','3','a','b']);
    expect(Arr::insertBefore('b', $array2, ['z' => 13]))->toEqual(['a' => 1, 'z' => 13, 'b' => 2, 'c' => 3]);
    expect(Arr::insertBefore('z', $array2, ['z' => 13]))->toEqual(['a' => 1, 'b' => 2, 'c' => 3, 'z' => 13]);
});

test('is indexed and flat', function () {
    expect(Arr::isIndexedAndFlat('not an array'))->toBeFalse();
    expect(Arr::isIndexedAndFlat([[]]))->toBeFalse();
    expect(Arr::isIndexedAndFlat([]))->toBeTrue();
    expect(Arr::isIndexedAndFlat([1, 2, 3]))->toBeTrue();
});

test('prefix keys', function () {
    $array = ['_a' => '', 'b' => ''];
    expect(Arr::prefixKeys($array))->toEqual(['_a' => '', '_b' => '']);
});

test('prepend', function () {
    $array1 = ['1','2','3'];
    $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
    expect(Arr::prepend($array1, 13))->toEqual(['13','1','2','3']);
    expect(Arr::prepend($array2, 13, 'z'))->toEqual(['z' => 13, 'a' => 1, 'b' => 2, 'c' => 3]);
});

test('reindex', function () {
    $array = ['1','2','3','4','5'];
    unset($array[3]);
    expect(Arr::reindex($array))->toEqual(['1','2','3','5']);
});

test('remove', function () {
    $array = [
        'indexed',
        'emptyString' => '',
        'array' => ['string' => 'string'],
    ];
    expect(Arr::remove($array))->toEqual($array);
    expect(Arr::remove($array, ''))->toEqual($array);
    expect(Arr::remove($array, '0'))->toEqual(['emptyString' => '', 'array' => ['string' => 'string']]);
    expect(Arr::remove($array, 0))->toEqual(['emptyString' => '', 'array' => ['string' => 'string']]);
    expect(Arr::remove($array, 'array'))->toEqual(['indexed', 'emptyString' => '']);
    expect(Arr::remove($array, 'array.string'))->toEqual(['indexed', 'emptyString' => '', 'array' => []]);
});

test('remove empty values', function () {
    $array = [
        'emptyString' => '',
        'emptyArray' => [],
        'array' => [
            'string' => 'string',
            'emptyString' => [],
        ],
    ];
    expect(Arr::removeEmptyValues($array))->toEqual(['array' => ['string' => 'string']]);
});

test('remove value', function () {
    // ensure it only works on indexed arrays
    expect(Arr::removeValue('a', ['a', 'b' => 'c']))->toEqual(['a', 'b' => 'c']);
    // ensure it works with non scalar items
    expect(Arr::removeValue('a', ['a', []]))->toEqual([[]]);
    expect(Arr::removeValue([], ['a', []]))->toEqual(['a']);
    expect(Arr::removeValue([1], ['a', [1]]))->toEqual(['a']);
    // ensure it doesn't work for object items
    expect(Arr::removeValue((object)[], ['a', (object)[]]))->toEqual(['a', (object)[]]);
    // ensure that it reindexes the array
    expect(Arr::removeValue('a', [1 => 'a', 2 => 'b']))->toEqual([0 => 'b']);
    expect(Arr::removeValue('a', [1 => 'a', 5 => 'b']))->toEqual([0 => 'b']);
});

test('restrict keys', function () {
    $array = [
        'user_a' => ['id' => 1, 'name' => 'Alice'],
        0 => ['id' => 2, 'name' => 'Bob'],
        '' => ['id' => 5, 'name' => 'Grace'],
        'string_id' => ['id' => '001', 'name' => 'Frank'],
        1 => ['id' => 3, 'name' => 'Charlie'],
    ];
    // Test case 1: Restrict to non-existent keys
    $result = Arr::restrictKeys($array, ['non_existent']);
    expect($result)->toEqual([]);
    // Test case 2: Empty allowed keys
    $result = Arr::restrictKeys($array, []);
    expect($result)->toEqual([]);
    // Test case 3: Empty array
    $result = Arr::restrictKeys([], ['user_a']);
    expect($result)->toEqual([]);
    // Test case 4: Empty string key
    $result = Arr::restrictKeys($array, ['']);
    expect($result)->toEqual(['' => ['id' => 5, 'name' => 'Grace']]);
    // Test case 5: Restrict to existing keys
    $result = Arr::restrictKeys($array, ['user_a', 'string_id']);
    expect($result)->toEqual([
        'user_a' => ['id' => 1, 'name' => 'Alice'],
        'string_id' => ['id' => '001', 'name' => 'Frank'],
    ]);
    // Test case 6: Mixed existing and non-existent keys
    $result = Arr::restrictKeys($array, ['user_a', 0, 'missing']);
    expect($result)->toEqual([ 'user_a' => ['id' => 1, 'name' => 'Alice'], 0 => ['id' => 2, 'name' => 'Bob'], ]);
    // Test case 7: Numeric keys
    $result = Arr::restrictKeys($array, [0, 1]);
    expect($result)->toEqual([ 0 => ['id' => 2, 'name' => 'Bob'], 1 => ['id' => 3, 'name' => 'Charlie'], ]);
});

test('search by key', function () {
    $haystack = [
        '' => ['id' => 5, 'name' => 'Grace'],
        'string_id' => ['id' => '001', 'name' => 'Frank'],
        'user_a' => ['id' => 1, 'name' => 'Alice'],
        0 => ['id' => 2, 'name' => 'Bob'],
        1 => ['id' => 3, 'name' => 'Charlie'],
    ];
    // Test case 1: Basic search with valid needle and key
    $result = Arr::searchByKey(2, $haystack, 'id');
    expect($result)->toEqual(['id' => 2, 'name' => 'Bob']);
    // Test case 2: String needle with strict comparison
    $result = Arr::searchByKey('001', $haystack, 'id');
    expect($result)->toEqual(['id' => '001', 'name' => 'Frank']);
    // Test case 3: Haystack with empty string key
    $result = Arr::searchByKey(5, $haystack, 'id');
    expect($result)->toEqual(['id' => 5, 'name' => 'Grace']);
    // Test case 4: Haystack with non-auto-incrementing keys
    $result = Arr::searchByKey(3, $haystack, 'id');
    expect($result)->toEqual(['id' => 3, 'name' => 'Charlie']);
    // Test case 5: Search with non-existent needle
    $result = Arr::searchByKey(6, $haystack, 'id');
    expect($result)->toBeFalse();
    // Test case 6: Search with non-existent key
    $result = Arr::searchByKey('Alice', $haystack, 'email');
    expect($result)->toBeFalse();
    // Test case 7: Empty haystack
    $result = Arr::searchByKey(1, [], 'id');
    expect($result)->toBeFalse();
    // Test case 8: Non-array haystack
    $result = Arr::searchByKey(1, 'not an array', 'id');
    expect($result)->toBeFalse();
    // Test case 9: Haystack with mismatched keys and values count
    $result = Arr::searchByKey(1, array_merge($haystack, ['incomplete' => ['name' => 'Eve']]), 'id');
    expect($result)->toBeFalse();
});

test('set', function () {
    expect(Arr::set([], 'number.thirteen', '13'))->toEqual(['number' => ['thirteen' => '13']]);
    expect(Arr::set([], '', '13'))->toEqual([]);
});

test('unflatten', function () {
    $original = ['parent.child' => 'toys'];
    $converted = ['parent' => ['child' => 'toys']];
    expect(Arr::unflatten($original))->toEqual($converted);
});

test('unique', function () {
    // Keys and order are preserved on purpose ("we do not want to reindex the array!") — keyed
    // defaults depend on it. A sort() here would hide a reindexing regression.
    expect(Arr::unique(['1', '3', '2', '3', '4', '3', '5']))
        ->toBe([0 => '1', 1 => '3', 2 => '2', 4 => '4', 6 => '5']);
});

test('unique int', function () {
    $array = ['1','3','2','a','4','3','5','0', '-1'];
    $test1 = Arr::uniqueInt($array);
    sort($test1);
    expect($test1)->toEqual([1,2,3,4,5]);
    $test2 = Arr::uniqueInt($array, false);
    sort($test2);
    expect($test2)->toEqual([-1,0,1,2,3,4,5]);
});

test('unique string', function () {
    $array = [1,'1','3',[23],'2','a','4','3','5','0', '-1'];
    $test1 = Arr::uniqueString($array);
    sort($test1);
    expect($test1)->toEqual(['-1','0','1','2','3','4','5','a']);
});

test('unprefix keys', function () {
    $array = ['_a' => '', 'b' => ''];
    expect(Arr::unprefixKeys($array))->toEqual(['a' => '', 'b' => '']);
});

test('consolidating an arguments object keeps its array values intact', function () {
    $arguments = glsr()->args(['list' => [1, 2], 'name' => 'x']);

    expect(\GeminiLabs\SiteReviews\Helpers\Arr::consolidate($arguments))
        ->toBe(['list' => [1, 2], 'name' => 'x']);
});

test('a dot-path can be set through an object in the middle', function () {
    $data = ['wrapper' => (object) ['inner' => 'old']];

    $result = \GeminiLabs\SiteReviews\Helpers\Arr::set($data, 'wrapper.inner', 'new');

    expect($result['wrapper']->inner)->toBe('new');
});
