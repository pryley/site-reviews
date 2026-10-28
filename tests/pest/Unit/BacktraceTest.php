<?php

use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Diagnostics\Backtrace;

/*
 * Backtrace turns a debug_backtrace into the "Class:line" prefix on every log entry: the line of
 * the log call and the class whose method made it, however the call reached Console::log().
 *
 * The frames below are the shapes PHP 8 produces. A frame's file and line are where its function
 * was called from. call_user_func_array has a frame of its own when it is called inside a
 * namespace (Console::__call, HookProxy's closure), and the function it invokes then has no file.
 * Called from the global namespace (helpers.php, WP_Hook) it has no frame, and the function it
 * invokes takes the call_user_func_array line as its file and line.
 */

function backtraceOurs(string $file): string
{
    return glsr()->path($file);
}

/**
 * Return the frames every log call starts with: Console::log() reached through Console::__call().
 */
function backtraceConsole(): array
{
    return [
        ['class' => Backtrace::class, 'function' => 'line', 'file' => backtraceOurs('plugin/Modules/Console.php'), 'line' => 124],
        ['class' => Console::class, 'function' => 'log'],
        ['function' => 'call_user_func_array', 'file' => backtraceOurs('plugin/Modules/Console.php'), 'line' => 58],
    ];
}

test('a level call made in a proxied hook callback is located at its line', function () {
    // A glsr_log()->debug() on line 58 of a controller method that WordPress ran through HookProxy::proxy().
    $line = glsr(Backtrace::class)->buildLine([
        ...backtraceConsole(),
        ['class' => Console::class, 'function' => '__call', 'file' => backtraceOurs('plugin/Controllers/RealCaller.php'), 'line' => 58],
        ['class' => 'RealCaller', 'function' => 'filterSomething'],
        ['function' => 'call_user_func_array', 'file' => backtraceOurs('plugin/HookProxy.php'), 'line' => 30],
        ['class' => 'RealCaller', 'function' => '{closure}', 'file' => ABSPATH.'wp-includes/class-wp-hook.php', 'line' => 353],
        ['class' => 'WP_Hook', 'function' => 'apply_filters', 'file' => ABSPATH.'wp-includes/plugin.php', 'line' => 205],
    ]);

    expect($line)->toBe('RealCaller:58');
});

test('a level call made in an ordinary method is located at its line', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ...backtraceConsole(),
        ['class' => Console::class, 'function' => '__call', 'file' => backtraceOurs('plugin/Commands/RealCaller.php'), 'line' => 40],
        ['class' => 'RealCaller', 'function' => 'handle', 'file' => backtraceOurs('plugin/Controllers/Caller.php'), 'line' => 12],
        ['class' => 'Caller', 'function' => 'run', 'file' => backtraceOurs('plugin/Application.php'), 'line' => 7],
    ]);

    expect($line)->toBe('RealCaller:40');
});

test('a call through helpers.php is attributed to the caller behind it', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ...backtraceConsole(),
        ['class' => Console::class, 'function' => '__call', 'file' => backtraceOurs('helpers.php'), 'line' => 265],
        ['function' => 'glsr_log', 'file' => backtraceOurs('plugin/Commands/RealCaller.php'), 'line' => 40],
        ['class' => 'RealCaller', 'function' => 'handle', 'file' => backtraceOurs('plugin/Controllers/Caller.php'), 'line' => 12],
    ]);

    expect($line)->toBe('RealCaller:40');
});

test('a call through the BlackHole is attributed to the caller behind it', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ...backtraceConsole(),
        ['class' => Console::class, 'function' => '__call', 'file' => backtraceOurs('plugin/BlackHole.php'), 'line' => 22],
        ['class' => 'GeminiLabs\SiteReviews\BlackHole', 'function' => '__call', 'file' => backtraceOurs('plugin/Commands/RealCaller.php'), 'line' => 40],
        ['class' => 'RealCaller', 'function' => 'handle', 'file' => backtraceOurs('plugin/Controllers/Caller.php'), 'line' => 12],
    ]);

    expect($line)->toBe('RealCaller:40');
});

test('a BlackHole invoked by a hook is attributed to the hook', function () {
    $line = glsr(Backtrace::class)->buildLine([
        ...backtraceConsole(),
        ['class' => Console::class, 'function' => '__call', 'file' => backtraceOurs('plugin/BlackHole.php'), 'line' => 22],
        ['class' => 'GeminiLabs\SiteReviews\BlackHole', 'function' => '__call', 'file' => ABSPATH.'wp-includes/class-wp-hook.php', 'line' => 353],
        ['class' => 'WP_Hook', 'function' => 'apply_filters', 'file' => ABSPATH.'wp-includes/plugin.php', 'line' => 205],
    ]);

    expect($line)->toBe('WP_Hook:353');
});

test('a call made outside a class is attributed to its file', function () {
    $functions = get_stylesheet_directory().'/functions.php';
    $line = glsr(Backtrace::class)->buildLine([
        ...backtraceConsole(),
        ['class' => Console::class, 'function' => '__call', 'file' => backtraceOurs('helpers.php'), 'line' => 265],
        ['function' => 'glsr_log', 'file' => $functions, 'line' => 20],
        ['function' => 'my_theme_setup', 'file' => ABSPATH.'wp-includes/class-wp-hook.php', 'line' => 353],
    ]);

    expect($line)->toBe($functions.':20');
});

test('frames that only pass the call on give no location', function () {
    expect(glsr(Backtrace::class)->buildLine(backtraceConsole()))->toBe('');
});
