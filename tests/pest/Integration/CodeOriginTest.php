<?php

use GeminiLabs\SiteReviews\Modules\Diagnostics\CodeOrigin;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The "[CODE SNIPPET]" tag: the first file outside Site Reviews on the call chain, when it is
 * the site owner's code. A theme template that renders a shortcode is not that file: WordPress
 * sits between the template and the plugin.
 */

beforeEach(fn () => resetPluginState());

function codeOriginOurs(string $file = 'plugin/Modules/Console.php'): string
{
    return glsr()->path($file);
}

test('the first file outside the plugin is the origin when it is user code', function (string $file) {
    $origin = glsr(CodeOrigin::class)->fromBacktrace([
        ['file' => codeOriginOurs(), 'line' => 1],
        ['function' => 'call_user_func_array'], // an internal call has no file
        ['file' => codeOriginOurs('plugin/Commands/ToggleVerified.php'), 'line' => 2],
        ['file' => $file, 'line' => 42],
        ['file' => ABSPATH.'wp-includes/class-wp-hook.php', 'line' => 3],
    ]);

    expect($origin)->toBe(['file' => $file, 'line' => 42]);
})->with([
    'the theme' => fn () => get_stylesheet_directory().'/functions.php',
    'a must-use plugin' => fn () => WPMU_PLUGIN_DIR.'/site-tweaks.php',
    'a Code Snippets or WPCode snippet' => fn () => WP_PLUGIN_DIR."/code-snippets/php/snippet-ops.php(822) : eval()'d code",
    'a Fluent Snippets file' => fn () => WP_CONTENT_DIR.'/fluent-snippet-storage/1-my-snippet.php',
    'a Code Snippets file' => fn () => WP_CONTENT_DIR.'/code-snippets/snippets/5.php',
]);

test('user code that reaches the plugin through WordPress is not the origin', function () {
    // A classic theme's template renders a page, WordPress runs the shortcode, the plugin logs.
    $origin = glsr(CodeOrigin::class)->fromBacktrace([
        ['file' => codeOriginOurs(), 'line' => 1],
        ['file' => ABSPATH.'wp-includes/shortcodes.php', 'line' => 2],
        ['file' => get_template_directory().'/page.php', 'line' => 3],
    ]);

    expect($origin)->toBe([]);
});

test('another plugin is not user code', function () {
    $origin = glsr(CodeOrigin::class)->fromBacktrace([
        ['file' => codeOriginOurs(), 'line' => 1],
        ['file' => WP_PLUGIN_DIR.'/some-other-plugin/plugin.php', 'line' => 2],
        ['file' => get_stylesheet_directory().'/functions.php', 'line' => 3],
    ]);

    expect($origin)->toBe([])
        ->and(glsr(CodeOrigin::class)->isUserCode(WP_PLUGIN_DIR.'/some-other-plugin/plugin.php'))->toBeFalse()
        ->and(glsr(CodeOrigin::class)->isUserCode(''))->toBeFalse();
});

function codeOriginSnippets(): array
{
    return require WPMU_PLUGIN_DIR.'/fixtures/code-origin.php';
}

test('an exception names the user code that threw it, and a callback the place it is defined', function () {
    $snippets = codeOriginSnippets();
    try {
        $snippets['throw']();
    } catch (\Throwable $error) {
    }

    $thrown = glsr(CodeOrigin::class)->fromThrowable($error);
    $defined = glsr(CodeOrigin::class)->fromCallback($snippets['throw']);

    expect($thrown['file'] ?? '')->toBe(WPMU_PLUGIN_DIR.'/fixtures/code-origin.php')
        ->and($thrown['line'] ?? 0)->toBeGreaterThan(0)
        ->and($defined['file'] ?? '')->toBe(WPMU_PLUGIN_DIR.'/fixtures/code-origin.php')
        ->and($defined['line'] ?? 0)->toBeLessThan($thrown['line'])
        ->and(glsr(CodeOrigin::class)->fromCallback('wp_insert_post'))->toBe([])
        ->and(glsr(CodeOrigin::class)->fromCallback('no_such_function_anywhere'))->toBe([])
        ->and(glsr(CodeOrigin::class)->fromCallback([glsr(Console::class), 'get']))->toBe([])
        ->and(glsr(CodeOrigin::class)->fromThrowable(new \RuntimeException('thrown by the plugin\'s own tests')))->toBe([]);
});

test('the console tags an entry that user code logged, and one the plugin logged because user code called it', function () {
    wp_set_current_user(0);
    glsr(Console::class)->clear();
    $snippets = codeOriginSnippets();

    $snippets['log']('a snippet logged this');
    $snippets['call'](); // ToggleVerified logs that the review is not valid
    glsr_log()->error('the plugin logged this');

    $lines = array_values(array_filter(explode("\n", glsr(Console::class)->getRaw())));
    expect($lines)->toHaveCount(3)
        ->and($lines[0])->toMatch('/^\[[^\]]+\] ERROR \[CODE SNIPPET\] \[\\\\mu-plugins\\\\fixtures\\\\code-origin\.php:\d+\] a snippet logged this$/')
        ->and($lines[1])->toMatch('/^\[[^\]]+\] ERROR \[CODE SNIPPET\] \[\\\\mu-plugins\\\\fixtures\\\\code-origin\.php:\d+\] \[Commands\\\\ToggleVerified\.php:\d+\] Cannot toggle verified status/')
        ->and($lines[2])->not->toContain('CODE SNIPPET')
        ->and($lines[2])->toEndWith('the plugin logged this');
});

test('the console tags the entry after origin(), once, for an exception or a callback from user code', function () {
    glsr(Console::class)->clear();
    $snippets = codeOriginSnippets();
    try {
        $snippets['throw']();
    } catch (\Throwable $error) {
    }

    glsr_log()->origin($error)->error($error->getMessage());
    glsr_log()->error('the next entry is the plugin\'s own');
    glsr_log()->origin($snippets['log'])->warning('a callback on a retired hook');
    glsr_log()->origin(new \RuntimeException('thrown by the test file'))->error('not user code');

    $lines = array_values(array_filter(explode("\n", glsr(Console::class)->getRaw())));
    expect($lines)->toHaveCount(4)
        ->and($lines[0])->toContain('ERROR [CODE SNIPPET] [\\mu-plugins\\fixtures\\code-origin.php:')
        ->and($lines[0])->toEndWith('thrown in a snippet')
        ->and($lines[1])->not->toContain('CODE SNIPPET')
        ->and($lines[2])->toContain('WARNING [CODE SNIPPET] [\\mu-plugins\\fixtures\\code-origin.php:')
        ->and($lines[3])->not->toContain('CODE SNIPPET');
});
