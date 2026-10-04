<?php

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Compat\Controllers\DeprecationController;
use GeminiLabs\SiteReviews\Compat\Hooks;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The suite turns the old hooks off (mu-plugins/site-reviews-tests.php), so each
 * test registers those of the versions it names.
 */

beforeEach(function () {
    resetPluginState();
    // WordPress raises E_USER_DEPRECATED for each; the notice is what is tested
    add_filter('deprecated_function_trigger_error', '__return_false');
    add_filter('deprecated_hook_trigger_error', '__return_false');
});

function registerOldHooks(array $versions): void
{
    foreach ([5, 6, 7, 8] as $version) {
        remove_filter("site-reviews/support/deprecated/v{$version}", '__return_false');
        if (!in_array($version, $versions)) {
            add_filter("site-reviews/support/deprecated/v{$version}", '__return_false');
        }
    }
    glsr(Hooks::class)->onPluginsLoaded();
}

test('a listener of an old filter name receives the value, and its answer is used', function () {
    registerOldHooks([5, 8]);
    add_filter('site-reviews/submission-form/order', fn ($order) => array_merge($order, ['v5']));
    add_filter('site-reviews/review-form/order', fn ($order) => array_merge($order, ['v8']));

    // the 5.0 name is relayed at priority 9, the 8.0 name at 10
    expect(glsr()->filterArray('review-form/fields/order', ['rating']))->toBe(['rating', 'v5', 'v8']);

    $notices = implode(' ', glsr()->retrieveAs('array', 'deprecated'));
    expect($notices)->toContain('site-reviews/submission-form/order');
    expect($notices)->toContain('site-reviews/review-form/order');
});

test('a listener of an old action name receives every argument', function () {
    // the plugin's own listeners expect a Review
    remove_all_actions('site-reviews/review/responded');
    remove_all_actions('site-reviews/review/updated');
    registerOldHooks([5, 6]);
    $heard = [];
    add_action('site-reviews/review/saved', function (...$args) use (&$heard) { $heard['saved'] = $args; }, 10, 2);
    add_action('site-reviews/review/response', function (...$args) use (&$heard) { $heard['response'] = $args; }, 10, 2);

    do_action('site-reviews/review/updated', 'review', ['title' => 'x']);
    do_action('site-reviews/review/responded', 'review', 'thanks');

    expect($heard)->toBe([
        'saved' => ['review', ['title' => 'x']],
        'response' => ['review', 'thanks'],
    ]);
});

test('the filter of a version turns off the old hooks of that version only', function () {
    registerOldHooks([6]);
    $heard = [];
    add_filter('site-reviews/review-form/order', function ($order) use (&$heard) {
        $heard[] = 'v8';
        return $order;
    });
    add_filter('site-reviews/slack/compose', function ($notification) use (&$heard) {
        $heard[] = 'v6';
        return $notification;
    });

    glsr()->filterArray('review-form/fields/order', []);
    glsr()->filterArray('slack/notification', []);

    expect($heard)->toBe(['v6']);
});

test('the 5.x template tags still work', function () {
    remove_all_filters('site-reviews/build/template/review');
    registerOldHooks([5]);
    $template = '<div id="{{ review_id }}">{{ assigned_to }}</div>';

    expect(glsr()->filterString('build/template/review', $template))
        ->toBe('<div id="review-{{ review_id }}">{{ assigned_links }}</div>');
});

test('the two functions that 5.0 deprecated still exist, and each use is noted', function () {
    expect(glsr_calculate_ratings())->toBeNull();
    expect(glsr_get_rating(['rating' => 5]))->toBeInstanceOf(Arguments::class);

    $notices = implode(' ', glsr()->retrieveAs('array', 'deprecated'));
    expect($notices)->toContain('glsr_calculate_ratings');
    expect($notices)->toContain('Use glsr_get_ratings instead');
});

test('the notices are written to the console once each, and those of other plugins are left out', function () {
    do_action('deprecated_function_run', 'glsr_get_rating', 'glsr_get_ratings', '5.0');
    do_action('deprecated_function_run', 'glsr_get_rating', 'glsr_get_ratings', '5.0');
    do_action('deprecated_function_run', 'another_plugin_function', '', '1.0');
    do_action('deprecated_hook_run', 'another-plugin/hook', '', '1.0', '');
    do_action('deprecated_hook_run', 'site-reviews/slack/compose', 'site-reviews/slack/notification', '6.9.0', '');

    expect(glsr()->retrieveAs('array', 'deprecated'))->toHaveCount(3);

    glsr(DeprecationController::class)->logNotices();

    $console = glsr(Console::class)->get();
    expect(substr_count($console, 'Function glsr_get_rating is'))->toBe(1);
    expect($console)->toContain('Hook site-reviews/slack/compose is');
    expect($console)->not->toContain('another');
});
