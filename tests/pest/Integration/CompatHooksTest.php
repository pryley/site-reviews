<?php

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Compat\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Compat\Controllers\Controller;
use GeminiLabs\SiteReviews\Compat\Controllers\DeprecationController;
use GeminiLabs\SiteReviews\Compat\Controllers\HookController;
use GeminiLabs\SiteReviews\Compat\Hooks;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\Vectorface\Whip\Whip;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
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
    foreach ([6, 7, 8] as $version) {
        remove_filter("site-reviews/support/deprecated/v{$version}", '__return_false');
        if (!in_array($version, $versions)) {
            add_filter("site-reviews/support/deprecated/v{$version}", '__return_false');
        }
    }
    glsr(Hooks::class)->onPluginsLoaded();
}

test('a listener of an old filter name receives the value, and its answer is used', function () {
    registerOldHooks([8]);
    add_filter('site-reviews/review-form/order', fn ($order) => array_merge($order, ['v8']));

    expect(glsr()->filterArray('review-form/fields/order', ['rating']))->toBe(['rating', 'v8']);

    $notices = implode(' ', glsr()->retrieveAs('array', 'deprecated'));
    expect($notices)->toContain('site-reviews/review-form/order');
});

test('a listener of an old action name receives every argument', function () {
    // the plugin's own listeners expect a Review
    remove_all_actions('site-reviews/review/updated');
    registerOldHooks([6]);
    $heard = [];
    add_action('site-reviews/review/saved', function (...$args) use (&$heard) { $heard = $args; }, 10, 2);

    $review = createReview();
    do_action('site-reviews/review/updated', $review, ['title' => 'x']);

    expect($heard)->toBe([$review, ['title' => 'x']]);
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

test('with Compatibility Mode off the old hooks of every version are off, and the filter of a version cannot turn its own back on', function () {
    glsr(OptionManager::class)->set('settings.advanced.compat', 'no');
    foreach ([6, 7, 8] as $version) {
        remove_filter("site-reviews/support/deprecated/v{$version}", '__return_false');
    }
    add_filter('site-reviews/support/deprecated/v6', '__return_true');
    $optimiser = hookedAt('wp_enqueue_scripts', 10);
    glsr(Hooks::class)->onPluginsLoaded();
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

    expect($heard)->toBe([])
        ->and(hookedAt('wp_enqueue_scripts', 10))->toBe($optimiser);
});

/*
 * A callback on a hook of WordPress is wrapped in a closure (HookProxy), so has_action() cannot
 * find it: these tests count the callbacks of the hook and priority instead.
 */

function hookedAt(string $hook, int $priority): int
{
    global $wp_filter;
    return isset($wp_filter[$hook]) ? count($wp_filter[$hook]->callbacks[$priority] ?? []) : 0;
}

test('with Compatibility Mode off only the notices of the compat layer are hooked', function () {
    remove_action('site-reviews/route/ajax/submit-review', [glsr(AjaxController::class), 'submitReviewAjax']);
    remove_filter('site-reviews/support/deprecated/v8', '__return_false');
    $blocks = hookedAt('render_block_data', 10);
    $canvas = hookedAt('enqueue_block_assets', 999);
    $editor = hookedAt('enqueue_block_editor_assets', 10);
    $notices = hookedAt('deprecated_hook_run', 10);
    $scripts = hookedAt('wp_print_scripts', 0);
    glsr(OptionManager::class)->set('settings.advanced.compat', 'no');

    glsr(Hooks::class)->run();
    glsr(Hooks::class)->onPluginsLoaded();

    expect(has_action('site-reviews/route/ajax/submit-review', [glsr(AjaxController::class), 'submitReviewAjax']))->toBeFalse();
    expect(hookedAt('render_block_data', 10))->toBe($blocks);
    expect(hookedAt('enqueue_block_editor_assets', 10))->toBe($editor);
    expect(hookedAt('enqueue_block_assets', 999))->toBe($canvas);
    expect(hookedAt('wp_print_scripts', 0))->toBe($scripts);
    expect(hookedAt('deprecated_hook_run', 10))->toBe($notices + 1);
});

test('with Compatibility Mode on the bridges of the compat layer are hooked', function () {
    $canvas = hookedAt('enqueue_block_assets', 999);
    $scripts = hookedAt('wp_print_scripts', 0);

    glsr(Hooks::class)->run();
    glsr(Hooks::class)->onPluginsLoaded();

    expect(has_action('site-reviews/route/ajax/submit-review', [glsr(AjaxController::class), 'submitReviewAjax']))->toBe(10);
    expect(hookedAt('enqueue_block_assets', 999))->toBe($canvas + 1);
    expect(hookedAt('wp_print_scripts', 0))->toBe($scripts + 1);
});

test('a snippet that still turns the 9.0.0 asset optimiser on is told once, and a site without one hears nothing', function () {
    // The filters stay declared so the snippet that hooks one is named, not ignored;
    // apply_filters_deprecated returns early while nothing hooks the filter.
    glsr(HookController::class)->filterOptimizeAsset();
    expect(glsr()->retrieveAs('array', 'deprecated'))->toBe([]);

    add_filter('site-reviews/optimize/css', '__return_true');
    add_filter('site-reviews/optimize/js', '__return_true');
    glsr(HookController::class)->filterOptimizeAsset();
    $notices = implode(' ', glsr()->retrieveAs('array', 'deprecated'));
    expect($notices)->toContain('Hook site-reviews/optimize/css is', 'Hook site-reviews/optimize/js is', 'no longer combines');
});

test('a snippet that still hooks one of the two 7.0.6 IP detection filters is told, and its answer is not used', function () {
    // Neither filter has been applied since 7.0.6. They are declared on the filter that replaced
    // them so the snippet is named; what it returns changes nothing.
    registerOldHooks([7]);
    $trusted = [Whip::CUSTOM_HEADERS => [Whip::IPV4 => ['10.0.0.1'], Whip::IPV6 => []]];

    expect(glsr()->filterArray('whip/whitelist', $trusted))->toBe($trusted)
        ->and(glsr()->retrieveAs('array', 'deprecated'))->toBe([]);

    add_filter('site-reviews/whip/methods', fn () => Whip::REMOTE_ADDR);
    add_filter('site-reviews/whip/whitelist/cloudflare', '__return_true');

    expect(glsr()->filterArray('whip/whitelist', $trusted))->toBe($trusted);
    $notices = implode(' ', glsr()->retrieveAs('array', 'deprecated'));
    expect($notices)->toContain(
        'Hook site-reviews/whip/methods is',
        'Hook site-reviews/whip/whitelist/cloudflare is',
        'Configure IP Address Detection tool'
    );
});

test('the 7.0.6 IP detection filters are not declared when the old hooks of version 7 are off', function () {
    registerOldHooks([]);
    add_filter('site-reviews/whip/methods', fn () => Whip::REMOTE_ADDR);

    glsr()->filterArray('whip/whitelist', []);

    expect(glsr()->retrieveAs('array', 'deprecated'))->toBe([]);
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

/*
 * Until 8.0 a block kept its assignments in assign_to (the form) or assigned_to, category and
 * user, and wrote assigned_posts, assigned_terms and assigned_users only for typed IDs. A block
 * that has not been saved since still holds the old attributes. They are read with the old hooks
 * of version 8.
 */

function assignedTo(string $html, string $name): string
{
    preg_match('/'.$name.'&#093;" value="([^"]*)"/', $html, $matches);
    return $matches[1] ?? 'missing';
}

test('a reviews block saved before 8.0 shows the reviews of the page it names', function () {
    registerOldHooks([8]);
    $pageId = createPost(['post_type' => 'page']);
    $assigned = createReview(['assigned_posts' => $pageId]);
    $other = createReview();

    $html = do_blocks('<!-- wp:site-reviews/reviews {"assigned_to":"'.$pageId.'"} /-->');

    expect($html)->toContain('id="review-'.$assigned->ID.'"');
    expect($html)->not->toContain('id="review-'.$other->ID.'"');
});

test('a form block saved before 8.0 assigns its reviews to the page, category and user it names', function () {
    registerOldHooks([8]);
    $pageId = createPost(['post_type' => 'page']);
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $userId = createUser();

    $html = do_blocks('<!-- wp:site-reviews/form {"assign_to":"'.$pageId.'","category":'.$termId.',"user":"'.$userId.'"} /-->');

    expect(assignedTo($html, 'assigned_posts'))->toBe((string) $pageId);
    expect(assignedTo($html, 'assigned_terms'))->toBe((string) $termId);
    expect(assignedTo($html, 'assigned_users'))->toBe((string) $userId);
});

test('a block saved before 8.0 is read inside another block', function () {
    registerOldHooks([8]);
    $pageId = createPost(['post_type' => 'page']);

    $html = do_blocks('<!-- wp:group --><div class="wp-block-group"><!-- wp:site-reviews/form {"assign_to":"'.$pageId.'"} /--></div><!-- /wp:group -->');

    expect(assignedTo($html, 'assigned_posts'))->toBe((string) $pageId);
});

test('a block saved before 8.0 with its schema on prints the schema', function (string $block) {
    // The block wrote true or false; today it declares 0 or 1, and WordPress gives the render
    // the default in place of a value that does not fit.
    registerOldHooks([8]);
    createReview(['rating' => 5]);
    glsr(OptionManager::class)->set('settings.schema.integration.plugin', '');

    do_blocks('<!-- wp:site-reviews/'.$block.' {"schema":true} /-->');
    ob_start();
    glsr(PublicController::class)->renderSchema();

    expect((string) ob_get_clean())->toContain('application/ld+json');
})->with(['reviews', 'summary']);

test('a block saved before 8.0 with its schema on prints no schema with the old hooks of version 8 off', function (string $block) {
    registerOldHooks([6, 7]);
    createReview(['rating' => 5]);
    glsr(OptionManager::class)->set('settings.schema.integration.plugin', '');

    do_blocks('<!-- wp:site-reviews/'.$block.' {"schema":true} /-->');
    ob_start();
    glsr(PublicController::class)->renderSchema();

    expect((string) ob_get_clean())->not->toContain('application/ld+json');
})->with(['reviews', 'summary']);

test('the typed IDs of a block saved before 8.0 are kept', function () {
    registerOldHooks([8]);
    $pageId = createPost(['post_type' => 'page']);

    $html = do_blocks('<!-- wp:site-reviews/form {"assign_to":"custom","assigned_posts":"'.$pageId.'"} /-->');

    expect(assignedTo($html, 'assigned_posts'))->toBe((string) $pageId);
});

test('the editor script of each block that 8.0 rebuilt waits for the compat script of the blocks', function () {
    // The compat script gives the blocks a deprecated version, which the editor has to know
    // before a block registers.
    $handles = [];
    foreach (['form', 'review', 'reviews', 'summary'] as $block) {
        $handles[$block] = WP_Block_Type_Registry::get_instance()->get_registered(glsr()->id.'/'.$block)->editor_script_handles[0];
        if (!wp_script_is($handles[$block], 'registered')) {
            wp_register_script($handles[$block], 'https://example.org/index.js'); // the suite starts each test without scripts
        }
    }

    glsr(Controller::class)->enqueueBlocksScript();

    $compat = glsr()->id.'/blocks/compat';
    expect(wp_scripts()->registered[$compat]->src)->toBe(glsr()->url('assets/scripts/site-reviews-blocks-compat.js'));
    foreach (['form', 'reviews', 'summary'] as $block) {
        expect(wp_scripts()->registered[$handles[$block]]->deps)->toContain($compat);
    }
    expect(wp_scripts()->registered[$handles['review']]->deps)->not->toContain($compat);
});

test('the compat script of the blocks is hooked with the old hooks of version 8 only', function () {
    $hooked = hookedAt('enqueue_block_editor_assets', 10);

    registerOldHooks([6, 7]);
    expect(hookedAt('enqueue_block_editor_assets', 10))->toBe($hooked);

    registerOldHooks([8]);
    expect(hookedAt('enqueue_block_editor_assets', 10))->toBe($hooked + 1);
});

test('the old attributes of a block are not read with the old hooks of version 8 off', function () {
    registerOldHooks([6, 7]);
    $pageId = createPost(['post_type' => 'page']);

    $html = do_blocks('<!-- wp:site-reviews/form {"assign_to":"'.$pageId.'"} /-->');

    expect(assignedTo($html, 'assigned_posts'))->toBe('');
});

test('the old attributes of a block are not read with Compatibility Mode off', function () {
    remove_filter('site-reviews/support/deprecated/v8', '__return_false');
    glsr(OptionManager::class)->set('settings.advanced.compat', 'no');
    glsr(Hooks::class)->onPluginsLoaded();
    $pageId = createPost(['post_type' => 'page']);

    $html = do_blocks('<!-- wp:site-reviews/form {"assign_to":"'.$pageId.'"} /-->');

    expect(assignedTo($html, 'assigned_posts'))->toBe('');
});

test('the old attributes of a block are not read as shortcode options', function () {
    registerOldHooks([8]);
    $pageId = createPost(['post_type' => 'page']);

    $html = do_shortcode('[site_reviews_form assign_to="'.$pageId.'"]');

    expect(assignedTo($html, 'assigned_posts'))->toBe('');
});
