<?php

use GeminiLabs\SiteReviews\Helper;

use function GeminiLabs\SiteReviews\Tests\createUser;

uses()->group('plugin');

test('build class name', function () {
    expect(Helper::buildClassName('hello-doll'))->toEqual('HelloDoll');
    expect(Helper::buildClassName('Doll', 'Hello'))->toEqual('GeminiLabs\SiteReviews\Hello\Doll');
});

test('build method name', function () {
    expect(Helper::buildMethodName('get', 'Hello-Doll'))->toEqual('getHelloDoll');
});

test('compare versions', function () {
    expect(Helper::compareVersions('1.0', '1'))->toBeTrue();
    expect(Helper::compareVersions('1.0', '1.00'))->toBeTrue();
    expect(Helper::compareVersions('1.0', '1.0.10'))->toBeFalse();
});

test('filter input', function () {
    $_POST['xxx'] = 'xxx';
    expect(Helper::filterInput('xxx'))->toEqual('xxx');
    expect(Helper::filterInput('zzz'))->toEqual(null);
});

test('filter input array', function () {
    $test = ['a' => ['b', 'c']];
    $_POST['xxx'] = $test;
    expect(Helper::filterInputArray('xxx'))->toEqual($test);
    expect(Helper::filterInputArray('zzz'))->toEqual([]);
});

test('get ip address', function () {
    expect(Helper::clientIp())->toEqual('127.0.0.1');
});

test('get page number', function () {
    $queryvar = glsr()->constant('PAGED_QUERY_VAR');
    expect(Helper::getPageNumber("https://test.com?{$queryvar}=2"))->toEqual('2');
    expect(Helper::getPageNumber())->toEqual('1');
});

test('get user id', function () {
    $userId = createUser([
        'user_login' => 'test_user',
    ]);
    wp_set_current_user($userId);
    $user = wp_get_current_user();
    expect(Helper::getUserId($user))->toEqual($userId);
    expect(Helper::getUserId($userId))->toEqual($userId);
    expect(Helper::getUserId('user_id'))->toEqual($userId);
    expect(Helper::getUserId('test_user'))->toEqual($userId);
    expect(Helper::getUserId('xxx'))->toEqual(0);
    $fn = fn () => 13;
    add_filter('site-reviews/assigned_users/author_id', $fn);
    add_filter('site-reviews/assigned_users/profile_id', $fn);
    add_filter('site-reviews/assigned_users/user_id', $fn);
    expect(Helper::getUserId('author_id'))->toEqual(13);
    expect(Helper::getUserId('profile_id'))->toEqual(13);
    expect(Helper::getUserId('user_id'))->toEqual(13);
    remove_filter('site-reviews/assigned_users/author_id', $fn);
    remove_filter('site-reviews/assigned_users/profile_id', $fn);
    remove_filter('site-reviews/assigned_users/user_id', $fn);
});

test('if empty', function () {
    expect(Helper::ifEmpty(0, 'abc'))->toEqual(0);
    expect(Helper::ifEmpty(0, 'abc', $strict = true))->toEqual('abc');
    expect(Helper::ifEmpty([], 'abc'))->toEqual('abc');
    expect(Helper::ifEmpty([], 'abc', $strict = true))->toEqual('abc');
    expect(Helper::ifEmpty(false, 'abc'))->toEqual($strict = false);
    expect(Helper::ifEmpty(false, 'abc', $strict = true))->toEqual('abc');
    expect(Helper::ifEmpty(null, 'abc'))->toEqual('abc');
    expect(Helper::ifEmpty(null, 'abc', $strict = true))->toEqual('abc');
    expect(Helper::ifEmpty('', 'abc'))->toEqual('abc');
    expect(Helper::ifEmpty('', 'abc', $strict = true))->toEqual('abc');
});

test('is greater then', function () {
    expect(Helper::isGreaterThan('1.0', '1'))->toBeFalse();
    expect(Helper::isGreaterThan('1.0.0', '1.0'))->toBeFalse();
    expect(Helper::isGreaterThan('1.0.0', '1.0.0'))->toBeFalse();
    expect(Helper::isGreaterThan('1.0.0', '1.0.1'))->toBeFalse();
});

test('is greater then or equal', function () {
    expect(Helper::isGreaterThanOrEqual('1.0', '1'))->toBeTrue();
    expect(Helper::isGreaterThanOrEqual('1.0.0', '1.0'))->toBeTrue();
    expect(Helper::isGreaterThanOrEqual('1.0.0', '1.0.0'))->toBeTrue();
    expect(Helper::isGreaterThanOrEqual('1.0.0', '1.0.1'))->toBeFalse();
});

test('is less then', function () {
    expect(Helper::isLessThan('1', '1.0'))->toBeFalse();
    expect(Helper::isLessThan('1.0', '1.0.0'))->toBeFalse();
    expect(Helper::isLessThan('1.0.0', '1.0.0'))->toBeFalse();
    expect(Helper::isLessThan('1.0.1', '1.0.0'))->toBeFalse();
});

test('is less then or equal', function () {
    expect(Helper::isLessThanOrEqual('1', '1.0'))->toBeTrue();
    expect(Helper::isLessThanOrEqual('1.0', '1.0.0'))->toBeTrue();
    expect(Helper::isLessThanOrEqual('1.0.0', '1.0.0'))->toBeTrue();
    expect(Helper::isLessThanOrEqual('1.0.1', '1.0.0'))->toBeFalse();
});
