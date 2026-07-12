<?php

use GeminiLabs\SiteReviews\Database\OptionManager;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

test('all', function () {
    $options = glsr(OptionManager::class)->all();
    expect($options)->toHaveKey('settings');
    expect($options)->toHaveKey('version');
    expect($options)->toHaveKey('version_upgraded_from');
    expect($options['settings'])->toHaveKey('general');
    expect($options['settings'])->toHaveKey('forms');
    expect($options['settings'])->toHaveKey('reviews');
    expect($options['settings'])->toHaveKey('schema');
});

test('database key', function () {
    expect(OptionManager::databaseKey())->toEqual('site_reviews');
});

test('database keys', function () {
    expect(OptionManager::databaseKeys())->toEqual([
        8 => "site_reviews",
        7 => "site_reviews",
        6 => "site_reviews_v6",
        5 => "site_reviews_v5",
        4 => "site_reviews_v4",
        3 => "site_reviews_v3",
        2 => "geminilabs_site_reviews-v2",
        1 => "geminilabs_site_reviews_settings",
    ]);
});

test('get', function () {
    $options = glsr(OptionManager::class);
    $path = 'settings.general.require.approval';
    expect($options->get($path))->toEqual('no');
    expect($options->get($path, 'yes'))->toEqual('no');
    expect($options->get($path, 'yes', 'bool'))->toEqual(false);
    expect($options->get('xyz', 'fallback'))->toEqual('fallback');
});

test('get array', function () {
    $options = glsr(OptionManager::class);
    $path = 'settings.general.require.approval';
    expect($options->getArray($path))->toEqual(['no']);
    expect($options->getArray($path, ['yes']))->toEqual(['no']);
    expect($options->getArray('xyz', ['fallback']))->toEqual(['fallback']);
});

test('get bool', function () {
    $options = glsr(OptionManager::class);
    $path = 'settings.general.require.approval';
    expect($options->getBool($path))->toBeFalse();
    expect($options->getBool($path, 'yes'))->toBeFalse();
    $options->set($path, 'yes');
    expect($options->getBool($path))->toBeTrue();
});

test('get int', function () {
    $options = glsr(OptionManager::class);
    $path = 'settings.reviews.excerpts_length';
    expect($options->getInt($path))->toEqual(55);
    $options->set($path, '50');
    expect($options->getInt($path))->toEqual(50);
});

test('set', function () {
    $options = glsr(OptionManager::class);
    $path = 'settings.general.require.approval';
    $value = $options->get($path);
    expect($options->get($path))->toEqual('no');
    $options->set($path, 'yes');
    expect($options->get($path))->toEqual('yes');
    $options->set($path, $value);
});

test('wp', function () {
    $options = glsr(OptionManager::class);
    expect($options->wp('blog_charset'))->toEqual('UTF-8');
    expect($options->wp('blog_charset_x'))->toEqual('');
    expect($options->wp('blog_charset_x', 'xyz'))->toEqual('xyz');
    expect($options->wp('blog_charset_x', 'xyz', 'bool'))->toEqual(false);
});
