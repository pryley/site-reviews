<?php

use GeminiLabs\SiteReviews\Modules\Backtrace;

/*
 * Backtrace turns a debug_backtrace into the "Class:line" prefix on every log
 * entry. The interesting part is WHOSE frame it names: a call that came through
 * helpers.php or the container's BlackHole is attributed to the real caller two
 * frames up, not to the plumbing.
 */

test('a call through helpers.php is attributed to the caller behind it', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ['file' => '/plugin/helpers.php', 'line' => 10],
        ['file' => '/theme/functions.php', 'line' => 20, 'class' => 'PluralClass'],
        ['class' => 'RealCaller', 'line' => 30],
    ]);

    expect($line)->toBe('RealCaller:20');
});

test('a call through the BlackHole is attributed to the caller behind it', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ['file' => '/plugin/BlackHole.php', 'line' => 5],
        ['class' => 'MiddleClass', 'line' => 6],
        ['class' => 'RealCaller', 'line' => 7],
    ]);

    expect($line)->toBe('RealCaller:6');
});

test('except when the BlackHole was invoked by a hook, which really is the caller', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ['file' => '/plugin/BlackHole.php', 'line' => 5],
        ['class' => 'MiddleClass', 'line' => 6],
        ['class' => 'WP_Hook', 'line' => 7],
    ]);

    expect($line)->toBe('MiddleClass:5');
});
