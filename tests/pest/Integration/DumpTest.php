<?php

use GeminiLabs\SiteReviews\Modules\Dump;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The pretty-printer behind every console line. dump() turns whatever it is handed — a scalar, an
 * array, an object, a closure — into the readable, indented text the log stores. What is pinned
 * here are the three shapes the ordinary logging paths do not already reach: a closure, an
 * ArrayObject, and an object graph deep enough to trip the depth limit that stops it running away.
 */

beforeEach(fn () => resetPluginState());

test('a closure is dumped as its signature, references and all', function () {
    $out = glsr(Dump::class)->dump(function (string $a, &$b) {});

    expect($out)->toContain('Closure')
        ->and($out)->toContain('$a')
        ->and($out)->toContain('&$b'); // a by-reference parameter keeps its &
});

test('an ArrayObject is labelled as one', function () {
    // It is iterated like an array but named as the object it is, so the log does not pretend a
    // typed collection was a plain array.
    $out = glsr(Dump::class)->dump(new \ArrayObject(['name' => 'Jane']));

    expect($out)->toContain('ArrayObject')
        ->and($out)->toContain('Jane');
});

test('an object nested past the depth limit is summarized, not expanded', function () {
    // The depth guard: without it a self-referential or very deep object graph would fill the log.
    $deep = (object) ['a' => (object) ['b' => (object) ['c' => 'too deep to show']]];

    $out = glsr(Dump::class)->dump($deep, 2); // only two levels are expanded

    expect($out)->toContain('Nested')
        ->and($out)->toContain('Object')
        ->and($out)->not->toContain('too deep to show'); // the deepest level was not expanded
});
