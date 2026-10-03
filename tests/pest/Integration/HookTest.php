<?php

use GeminiLabs\SiteReviews\Controllers\NetworkController;
use GeminiLabs\SiteReviews\Helpers\Hook;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Removing another plugin's callback: remove_filter() needs the identical callable, so
 * Hook::find() matches on class and method name, and reaches inside a closure for the
 * `callback` it closed over.
 */

beforeEach(function () {
    resetPluginState();
});

/**
 * Somebody else's plugin, doing something to a hook.
 */
class SomebodyElsesPlugin
{
    public bool $ran = false;

    public function theirCallback($value)
    {
        $this->ran = true;

        return 'they changed it';
    }

    public function somethingElse($value)
    {
        return $value;
    }
}

/*
 * Finding it.
 */

test('a callback added as an array is found by its class and method', function () {
    $them = new SomebodyElsesPlugin();
    add_filter('some_hook', [$them, 'theirCallback']);

    $found = Hook::find('some_hook', 'theirCallback', SomebodyElsesPlugin::class);

    expect($found)->not->toBeEmpty()
        ->and($found['function'][1])->toBe('theirCallback');
});

test('a callback added as a CLOSURE is found by reaching inside it', function () {
    // Depends on the wrapper closing over a variable named `callback`; a rename shows up here.
    $them = new SomebodyElsesPlugin();
    $callback = [$them, 'theirCallback'];
    add_filter('some_hook', function ($value) use ($callback) {
        return call_user_func($callback, $value);
    });

    $found = Hook::find('some_hook', 'theirCallback', SomebodyElsesPlugin::class);

    expect($found)->not->toBeEmpty();
});

test('the wrong hook, the wrong priority, the wrong class or the wrong method finds nothing', function () {
    $them = new SomebodyElsesPlugin();
    add_filter('some_hook', [$them, 'theirCallback'], 10);

    expect(Hook::find('a_hook_nobody_added', 'theirCallback', SomebodyElsesPlugin::class))->toBe([])
        ->and(Hook::find('some_hook', 'theirCallback', SomebodyElsesPlugin::class, 20))->toBe([])
        ->and(Hook::find('some_hook', 'theirCallback', self::class))->toBe([])
        ->and(Hook::find('some_hook', 'somethingElse', SomebodyElsesPlugin::class))->toBe([]);
});

/*
 * Removing it.
 */

test('their callback is taken off the hook, and stops running', function () {
    $them = new SomebodyElsesPlugin();
    add_filter('some_hook', [$them, 'theirCallback']);

    expect(apply_filters('some_hook', 'ours'))->toBe('they changed it');
    expect($them->ran)->toBeTrue();

    $removed = Hook::remove('some_hook', 'theirCallback', SomebodyElsesPlugin::class);

    expect($removed)->toBeTrue();
    expect(apply_filters('some_hook', 'ours'))->toBe('ours'); // and ours survives untouched
});

test('removing a callback that is not there says so, rather than pretending', function () {
    expect(Hook::remove('some_hook', 'nope', SomebodyElsesPlugin::class))
        ->toBeFalse();
});

/*
 * Multisite.
 */

test('the admin bar is left alone on a single site', function () {
    require_once ABSPATH.'wp-includes/class-wp-admin-bar.php';
    wp_set_current_user(createUser(['role' => 'administrator']));
    $bar = new WP_Admin_Bar();

    glsr(NetworkController::class)->extendAdminBar($bar);

    expect($bar->get_node('blog-1-site-reviews'))->toBeNull();
});

test('the admin bar is left alone for somebody who is not logged in', function () {
    require_once ABSPATH.'wp-includes/class-wp-admin-bar.php';
    wp_set_current_user(0);
    $bar = new WP_Admin_Bar();

    glsr(NetworkController::class)->extendAdminBar($bar);

    expect($bar->get_nodes())->toBeNull();
});

/*
 * Importing a review's images is tested in tests/pest/Import/AttachmentImportTest.php:
 * importAttachments() defines WP_IMPORTING, which changes every review created after it.
 */
