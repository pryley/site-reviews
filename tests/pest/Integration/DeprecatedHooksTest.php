<?php

use GeminiLabs\Vectorface\Whip\Whip;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The suite turns the deprecated fallbacks off (mu-plugins/site-reviews-tests.php), and
 * deprecated.php registers them once, on plugins_loaded. So a test turns one version back on
 * and runs the callbacks that deprecated.php hooked there.
 */

beforeEach(function () {
    resetPluginState();
    // WordPress raises E_USER_DEPRECATED for each; the deprecated_hook_run action is what is tested
    add_filter('deprecated_hook_trigger_error', '__return_false');
});

function registerDeprecatedHooksOf(int $version): void
{
    remove_filter("site-reviews/support/deprecated/v{$version}", '__return_false');
    foreach ($GLOBALS['wp_filter']['plugins_loaded']->callbacks as $callbacks) {
        foreach ($callbacks as $callback) {
            $function = $callback['function'];
            if (!$function instanceof Closure) {
                continue;
            }
            if (str_ends_with((string) (new ReflectionFunction($function))->getFileName(), '/site-reviews/deprecated.php')) {
                $function();
            }
        }
    }
}

test('a snippet that still hooks one of the two 7.0.6 IP detection filters is told, and its answer is not used', function () {
    // Neither filter has been applied since 7.0.6. They are declared on the filter that replaced
    // them so the snippet is named; what it returns changes nothing.
    registerDeprecatedHooksOf(7);
    $told = [];
    add_action('deprecated_hook_run', function ($hook) use (&$told) {
        $told[] = $hook;
    });
    $trusted = [Whip::CUSTOM_HEADERS => [Whip::IPV4 => ['10.0.0.1'], Whip::IPV6 => []]];

    expect(glsr()->filterArray('whip/whitelist', $trusted))->toBe($trusted)
        ->and($told)->toBe([]);

    add_filter('site-reviews/whip/methods', fn () => Whip::REMOTE_ADDR);
    add_filter('site-reviews/whip/whitelist/cloudflare', '__return_true');

    expect(glsr()->filterArray('whip/whitelist', $trusted))->toBe($trusted)
        ->and($told)->toBe(['site-reviews/whip/methods', 'site-reviews/whip/whitelist/cloudflare']);
});

test('the 7.0.6 IP detection filters are not declared while the fallbacks of version 7 are off', function () {
    $told = [];
    add_action('deprecated_hook_run', function ($hook) use (&$told) {
        $told[] = $hook;
    });
    add_filter('site-reviews/whip/methods', fn () => Whip::REMOTE_ADDR);

    glsr()->filterArray('whip/whitelist', []);

    expect($told)->toBe([]);
});
