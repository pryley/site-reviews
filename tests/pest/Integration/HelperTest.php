<?php

use GeminiLabs\SiteReviews\Helper;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createTerm;
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

test('get post id', function () {
    $parentId = createPost();
    $childId = createPost(['post_parent' => $parentId]);
    $GLOBALS['post'] = get_post($childId);

    expect(Helper::getPostId('post_id'))->toEqual($childId);
    expect(Helper::getPostId('parent_id'))->toEqual($parentId);
    expect(Helper::getPostId($childId))->toEqual($childId);
    expect(Helper::getPostId(get_post($childId)))->toEqual($childId);
    expect(Helper::getPostId(null))->toEqual(0);

    unset($GLOBALS['post']);
});

test('get term taxonomy id', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);

    expect(Helper::getTermTaxonomyId(get_term($termId)))->toEqual($termId); // a WP_Term instance
    expect(Helper::getTermTaxonomyId($termId))->toEqual($termId);
    expect(Helper::getTermTaxonomyId('no-such-term'))->toEqual(0);
});

test('input reads each source, and fails closed', function () {
    $_COOKIE['glsr_test'] = 'from-cookie';
    $_SERVER['GLSR_TEST'] = 'from-server';
    $_ENV['GLSR_TEST'] = 'from-env';
    $_GET['glsr_number'] = 'not-a-number';
    try {
        expect(Helper::input(INPUT_COOKIE, 'glsr_test'))->toBe('from-cookie');
        expect(Helper::input(INPUT_SERVER, 'GLSR_TEST'))->toBe('from-server');
        expect(Helper::input(INPUT_ENV, 'GLSR_TEST'))->toBe('from-env');
        // present but failing the filter: null from the SAPI, null again from the fallback
        expect(Helper::input(INPUT_GET, 'glsr_number', FILTER_VALIDATE_INT))->toBeNull();
        expect(Helper::input(INPUT_GET, 'glsr_absent'))->toBeNull();
    } finally {
        unset($_COOKIE['glsr_test'], $_SERVER['GLSR_TEST'], $_ENV['GLSR_TEST']);
    }
});

test('is local ip address', function () {
    expect(Helper::isLocalIpAddress('127.0.0.1'))->toBeTrue();
    expect(Helper::isLocalIpAddress('::1'))->toBeTrue();
    expect(Helper::isLocalIpAddress('203.0.113.5'))->toBeFalse();
    expect(Helper::isLocalIpAddress('not-an-ip'))->toBeTrue(); // unparseable: assume local, fail closed
});

test('is local server', function () {
    // filter_input(INPUT_SERVER) is shadowed onto $_SERVER by the suite, so the branches
    // are drivable. Restore what wp-env had afterwards.
    $original = $_SERVER;
    try {
        $_SERVER['SERVER_ADDR'] = '203.0.113.5';
        $_SERVER['HTTP_HOST'] = 'staging.example.test';
        expect(Helper::isLocalServer())->toBeTrue(); // a .test domain is a dev site

        $_SERVER['HTTP_HOST'] = 'www.example.com';
        expect(Helper::isLocalServer())->toBeFalse(); // public address, public host
    } finally {
        $_SERVER = $original;
    }
});

test('remote status check', function () {
    $ok = fn () => ['body' => '', 'headers' => [], 'response' => ['code' => 301, 'message' => 'Moved']];
    add_filter('pre_http_request', $ok);
    try {
        expect(Helper::remoteStatusCheck('https://example.org'))->toEqual(301);
    } finally {
        remove_filter('pre_http_request', $ok);
    }

    $down = fn () => new WP_Error('http_request_failed', 'unreachable');
    add_filter('pre_http_request', $down);
    try {
        expect(Helper::remoteStatusCheck('https://example.org'))->toBeFalse();
    } finally {
        remove_filter('pre_http_request', $down);
    }
});

test('server ip', function () {
    // The ipecho.net response, faked at the HTTP layer. Api caches into a site transient,
    // which the test transaction rolls back.
    $fake = function () {
        $requestsResponse = new \WpOrg\Requests\Response();
        $requestsResponse->body = '203.0.113.7';
        $requestsResponse->status_code = 200;
        $requestsResponse->success = true;
        return [
            'body' => '203.0.113.7',
            'cookies' => [],
            'filename' => null,
            'headers' => [],
            'http_response' => new \WP_HTTP_Requests_Response($requestsResponse),
            'response' => ['code' => 200, 'message' => 'OK'],
        ];
    };
    add_filter('pre_http_request', $fake);
    try {
        expect(Helper::serverIp())->toBe('203.0.113.7');
    } finally {
        remove_filter('pre_http_request', $fake);
    }
});

test('version', function () {
    expect(Helper::version('v1.2.3-beta', 'major'))->toBe('1');
    expect(Helper::version('v1.2.3-beta', 'minor'))->toBe('1.2');
    expect(Helper::version('v1.2.3-beta', 'patch'))->toBe('1.2.3');
    expect(Helper::version('v1.2.3-beta'))->toBe('v1.2.3-beta'); // no level: as given
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
