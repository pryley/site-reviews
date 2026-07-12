<?php

use GeminiLabs\SiteReviews\Helpers\Url;

uses()->group('plugin');

test('home', function () {
    $url = network_home_url();
    expect(Url::home())->toEqual($url.'/');
    expect(Url::home('test'))->toEqual($url.'/test/');
});

test('path', function () {
    $url = 'https://test.com';
    expect(Url::path($url))->toEqual('');
    expect(Url::path($url.'/'))->toEqual('');
    expect(Url::path($url.'/test'))->toEqual('/test');
    expect(Url::path($url.'/test/'))->toEqual('/test');
    expect(Url::path($url.'/test/dir'))->toEqual('/test/dir');
    expect(Url::path($url.'/test/dir/'))->toEqual('/test/dir');
});

test('query', function () {
    $url = 'https://test.com?abc=xyz';
    expect(Url::query($url, 'abc'))->toEqual('xyz');
    expect(Url::query($url, 'ab', '123'))->toEqual('123');
    expect(Url::query($url, 'ab'))->toBeNull();
});
