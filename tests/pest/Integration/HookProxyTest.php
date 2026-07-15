<?php

use GeminiLabs\SiteReviews\HookProxy;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The proxy every hook callback the plugin does not own is wrapped in.
 *
 * A third party fires a filter with data of the wrong shape; the plugin's typed callback would
 * fatal on it and take the whole page — and the blame — with it. proxy() catches the throwable,
 * logs a line, and degrades: a filter hands back its first argument unfiltered, an action simply
 * does nothing. The rethrow filter turns the catch off for anyone debugging (and for this suite,
 * which forces it ON in bootstrap so a throwable inside a hook cannot make a test pass quietly).
 *
 * So the swallow path is the one thing the rest of the suite CANNOT reach — every other test runs
 * with rethrow on. This turns it back off, which is the production default, and drives it.
 */

beforeEach(function () {
    resetPluginState();
    remove_all_filters('site-reviews/hook/rethrow'); // back to the production default: swallow, don't rethrow
});

/**
 * Something with a hook callback that always throws, wrapped exactly as the plugin wraps its own.
 */
function throwingSubject(): object
{
    return new class() {
        use HookProxy;

        public function filterSomething($value)
        {
            throw new \RuntimeException('third-party data was the wrong shape');
        }

        public function onSomething($value): void
        {
            throw new \RuntimeException('third-party data was the wrong shape');
        }
    };
}

test('a proxied filter that throws hands back its first argument unfiltered', function () {
    // The degrade-not-whitescreen contract: the site keeps the value it already had.
    $proxied = throwingSubject()->proxy('filterSomething');

    expect($proxied('the original, unfiltered value'))->toBe('the original, unfiltered value');
});

test('a proxied action that throws is swallowed and returns nothing', function () {
    // An action has nothing to hand back, so it just stops — the page carries on.
    $proxied = throwingSubject()->proxy('onSomething');

    expect($proxied('ignored'))->toBeNull();
});

test('with rethrow on, the throwable is let out instead of swallowed', function () {
    // The suite's own default, restored here: the error surfaces rather than hiding in the console,
    // which is what lets a test assert on a throwable fired through a hook at all.
    add_filter('site-reviews/hook/rethrow', '__return_true');
    $proxied = throwingSubject()->proxy('filterSomething');

    expect(fn () => $proxied('anything'))->toThrow(\RuntimeException::class);
});
