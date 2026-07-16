<?php

use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The plugin's own log, the one the Tools > Console screen shows and people paste into support
 * threads. It is a file in the uploads directory, written a line at a time. What is exercised here
 * is the machinery around the writing: the log levels, the string casts, the interpolation of
 * context into a message, and once()/logOnce() — the pair that collapses a message fired on every
 * page load into a single "[RECURRING]" line so a hot loop cannot fill the disk.
 */

beforeEach(function () {
    resetPluginState();
    glsr()->store(Console::LOG_ONCE_KEY, []);
});

test('the console casts to its own contents', function () {
    expect((string) glsr(Console::class))->toBe(glsr(Console::class)->get());
});

test('an unknown log-level method is a bad method call', function () {
    // The magic __call maps error()/warning()/… to a level constant of the same name; a method that
    // names no constant is a programming mistake, not a silent no-op.
    expect(fn () => glsr(Console::class)->banana('nope'))->toThrow(\BadMethodCallException::class);
});

test('a log-level setting outside the known levels falls back to INFO', function () {
    update_option(Console::LOG_LEVEL_KEY, 999); // not a real level

    expect(glsr(Console::class)->getLevel())->toBe(Console::INFO);

    delete_option(Console::LOG_LEVEL_KEY);
});

test('a message interpolates its context, formatting dates and encoding structures', function () {
    glsr_log()->error('user {name} sent {payload} at {when}', [
        'name' => 'Jane',                                   // scalar, used as-is
        'payload' => ['a' => 1],                            // not scalar, encoded to json
        'when' => new \DateTime('2026-01-02 03:04:05'),     // a date, formatted
    ]);

    $log = glsr(Console::class)->get();

    expect($log)->toContain('user Jane sent')
        ->and($log)->toContain('2026-01-02 03:04:05')
        ->and($log)->not->toContain('{name}')   // the placeholders were replaced, not left raw
        ->and($log)->not->toContain('{when}');
});

test('once() records a recurring message a single time, and cleans up a throwable', function () {
    // The same level+handle firing twice is recorded once. A throwable is reduced to its message,
    // with PHP's ", called in …" tail (which is noise in a recurring line) stripped.
    $error = new \RuntimeException('the widget exploded, called in /var/www/thing.php on line 9');

    glsr(Console::class)->once('error', 'widget', $error);
    glsr(Console::class)->once('error', 'widget', $error); // same handle+level — not added again
    glsr(Console::class)->once('warning', 'other', ['not' => 'a throwable']); // a different one

    $once = glsr()->retrieveAs('array', Console::LOG_ONCE_KEY);

    expect($once)->toHaveCount(2);
    $widget = $once[0];
    expect($widget['message'])->toContain('[RECURRING]')
        ->and($widget['message'])->toContain('the widget exploded')
        ->and($widget['message'])->not->toContain('called in'); // the tail was stripped
});

test('logOnce writes the recurring entries to the console and empties the queue', function () {
    glsr(Console::class)->once('error', 'widget', new \RuntimeException('a recurring failure'));

    glsr(Console::class)->logOnce();

    expect(glsr(Console::class)->get())->toContain('a recurring failure');
    expect(glsr()->retrieveAs('array', Console::LOG_ONCE_KEY))->toBe([]); // flushed
});
