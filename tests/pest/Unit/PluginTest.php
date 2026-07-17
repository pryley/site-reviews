<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Request;

uses()->group('plugin');

/*
 * The Plugin trait: the Application's utility surface — magic filters, magic properties,
 * paths/urls, views. Everything here runs against glsr() itself; the one static bit (load)
 * is exercised on a briefly-vacated singleton slot.
 */

test('the filter methods cast what the filter returns', function () {
    add_filter('site-reviews/unique-things', fn () => ['a', 'b', 'a', '', 'b']);
    add_filter('site-reviews/unique-ints', fn () => ['3', 3, '17', 0]);

    expect(glsr()->filterArrayUnique('unique-things', []))->toBe(['a', 'b']);
    expect(glsr()->filterArrayUniqueInt('unique-ints', []))->toBe([3, 17]);
    expect(glsr()->filterArrayUniqueString('unique-things', []))->toBe(['a', 'b']);
});

test('a magic method that is not a filter cast is refused loudly', function () {
    expect(fn () => glsr()->filterNonsenseCast('hook', ''))
        ->toThrow(BadMethodCallException::class);
});

test('isset() sees the constants too', function () {
    // post_type has no property behind it — only the POST_TYPE constant — and __isset must
    // agree with __get about that, or empty(glsr()->post_type) lies.
    expect(isset(glsr()->post_type))->toBeTrue();
    expect(glsr()->post_type)->toBe('site-review');
    expect(isset(glsr()->no_such_property_or_constant))->toBeFalse();
});

test('catching a fatal error logs nothing for somebody else\'s fatal', function () {
    // error_get_last() is whatever PHP saw most recently; only an E_ERROR inside the
    // plugin's own path is the plugin's to log. (A real E_ERROR cannot be staged in a
    // test — this drives the not-ours path.)
    glsr()->catchFatalError();
    expect(true)->toBeTrue();
});

test('load() creates the singleton once, and only once', function () {
    expect(Application::load())->toBe(glsr()); // already created at bootstrap

    // vacate the slot to prove creation, and put the real one back before anything notices
    $property = new ReflectionProperty(Application::class, 'instance');
    $property->setAccessible(true);
    $original = $property->getValue();
    $property->setValue(null, null);
    try {
        $fresh = Application::load();
        expect($fresh)->toBeInstanceOf(Application::class)
            ->and($fresh)->not->toBe($original);
    } finally {
        $property->setValue(null, $original);
    }
});

test('option() reads through the settings helper', function () {
    expect(glsr()->option('reviews.date.format', 'default'))->toBeString();
});

test('a view that does not exist is logged, not a broken include', function () {
    expect(glsr()->build('no/such/view'))->toBe('');
});

test('request() wraps values in a Request', function () {
    $request = glsr()->request(['action' => 'test']);

    expect($request)->toBeInstanceOf(Request::class)
        ->and($request->action)->toBe('test');
});

test('url() does not double the plugin directory', function () {
    $absolute = glsr()->path('assets/styles/site-reviews.css');

    $url = glsr()->url($absolute);

    expect($url)->toBe(glsr()->url('assets/styles/site-reviews.css'))
        ->and($url)->toContain('assets/styles/site-reviews.css')
        ->not->toContain(glsr()->path()); // the filesystem path must not leak into the url
});
