<?php

use GeminiLabs\SiteReviews\Arguments;

/*
 * Arguments::merge() must overwrite by key, not renumber. wp_parse_args()/array_merge() RENUMBERS
 * integer-like keys — a value under a numeric key moves to the end under a fresh index instead of
 * overwriting, even when nothing is passed in, because array_merge() reindexes the defaults too.
 *
 * The honeypot field name is an 8-char hex hash (Honeypot::hash), all digits on a fraction of
 * sites depending on their salts — how this surfaced. These tests pin the mechanism, not the symptom.
 */

test('an Arguments can be built from another Arguments', function () {
    $original = new Arguments(['name' => 'Jane']);

    expect((new Arguments($original))->toArray())->toBe(['name' => 'Jane']);
});

test('an Arguments serializes to a string and back', function () {
    $arguments = new Arguments(['name' => 'Jane']);

    expect((string) $arguments)->toBe(serialize(['name' => 'Jane']));
    expect(unserialize((string) $arguments))->toBe(['name' => 'Jane']);
});

test('replace swaps the whole contents, unlike merge', function () {
    $arguments = new Arguments(['name' => 'Jane', 'other' => 'kept']);
    $arguments->replace(['name' => 'John']);

    expect($arguments->toArray())->toBe(['name' => 'John']); // 'other' is gone
});

test('merge overwrites a value by its key', function () {
    $arguments = new Arguments(['name' => 'old', 'other' => 'kept']);
    $arguments->merge(['name' => 'new']);
    expect($arguments['name'])->toBe('new')
        ->and($arguments['other'])->toBe('kept');
});

test('merge preserves an integer-like key', function () {
    $arguments = new Arguments(['form_id' => 'glsr-12345678']);
    $arguments->merge(['84333063' => '', 'name' => 'Jane']);

    // The key must still be reachable — array_merge() would have moved it to 0.
    expect($arguments->offsetExists('84333063'))->toBeTrue();
    expect($arguments['84333063'])->toBe('');
    expect(array_key_exists(84333063, $arguments->toArray()))->toBeTrue();
    expect(array_key_exists(0, $arguments->toArray()))->toBeFalse();

    // and nothing else was disturbed
    expect($arguments['form_id'])->toBe('glsr-12345678')
        ->and($arguments['name'])->toBe('Jane');
});

test('merge preserves an integer-like key even when given nothing', function () {
    // array_merge($storage) alone reindexes: passing no data was not safe either.
    $arguments = new Arguments(['12345678' => 'kept', 'name' => 'Jane']);
    $arguments->merge();
    expect($arguments->offsetExists('12345678'))->toBeTrue();
    expect($arguments['12345678'])->toBe('kept');
    expect(array_key_exists(0, $arguments->toArray()))->toBeFalse();
});
