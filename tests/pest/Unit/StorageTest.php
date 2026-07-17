<?php

use GeminiLabs\SiteReviews\Storage;

uses()->group('plugin');

/*
 * The Storage trait: the in-memory state register on the Application singleton. Tested on a
 * fresh consumer rather than glsr(), whose storage was initialized at bootstrap — the lazy
 * initialization is part of the contract.
 */

function freshStorage(): object
{
    return new class() {
        use Storage;
    };
}

test('storage initializes itself on first touch, and round-trips a value', function () {
    $storage = freshStorage();

    expect($storage->retrieve('missing'))->toBeNull();
    expect($storage->retrieve('missing', 'fallback'))->toBe('fallback');

    $storage->store('answer', 42);
    expect($storage->retrieve('answer'))->toBe(42);
    expect($storage->retrieveAs('string', 'answer'))->toBe('42');

    $storage->discard('answer');
    expect($storage->retrieve('answer'))->toBeNull();
});

test('append builds a list, or a map, but never overwrites a scalar', function () {
    $storage = freshStorage();

    expect($storage->append('log', 'first'))->toBeTrue();
    expect($storage->append('log', 'second'))->toBeTrue();
    expect($storage->retrieve('log'))->toBe(['first', 'second']);

    expect($storage->append('versions', '1.0', 'addon-id'))->toBeTrue();
    expect($storage->retrieve('versions'))->toBe(['addon-id' => '1.0']);

    $storage->store('scalar', 'not-a-list');
    expect($storage->append('scalar', 'nope'))->toBeFalse(); // refused, not clobbered
    expect($storage->retrieve('scalar'))->toBe('not-a-list');
});
