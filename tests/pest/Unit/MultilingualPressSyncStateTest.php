<?php

use GeminiLabs\SiteReviews\Integrations\MultilingualPress\SyncState;

uses()->group('plugin');

test('a sync is active while its callback runs, nested or not, and never after', function () {
    $state = glsr(SyncState::class);
    expect($state->isActive())->toBeFalse();

    $seen = [];
    $result = $state->during(function () use ($state, &$seen) {
        $seen[] = $state->isActive();
        $state->during(function () use ($state, &$seen) {
            $seen[] = $state->isActive();
        });
        $seen[] = $state->isActive();
        return 'done';
    });
    expect($result)->toBe('done')
        ->and($seen)->toBe([true, true, true])
        ->and($state->isActive())->toBeFalse();

    // A callback that throws leaves the state off for the next event.
    try {
        $state->during(fn () => throw new RuntimeException('x'));
    } catch (RuntimeException $e) {
    }
    expect($state->isActive())->toBeFalse();
});
