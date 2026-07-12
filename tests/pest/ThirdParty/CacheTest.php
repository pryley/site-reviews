<?php

use GeminiLabs\SiteReviews\Commands\ApproveReview;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The page-cache integration.
 *
 * A review that is created, approved or edited changes what a visitor should see
 * on the page it is assigned to, so the page caches have to be told. This
 * integration has no third party to detect — it registers on every site — and
 * fans one flush out to thirteen cache plugins, skipping the ones that are not
 * installed.
 *
 * Four of the thirteen are reachable here, and they are the four that dispatch by
 * firing the cache plugin's own action (Endurance, Hummingbird, LiteSpeed, WP
 * Fastest Cache): a do_action() fires whether or not anybody is listening, so a
 * test can listen. The other nine call a function or a class the cache plugin
 * declares, so their bodies are guarded by function_exists()/class_exists() and
 * cannot run against a stub — what IS tested for those is that the guard holds
 * and the dispatcher does not fall over.
 *
 * Every test drives a real entry point: the review is genuinely created or
 * approved, and the flush is whatever the plugin's own hooks do in response.
 */

beforeEach(function () {
    resetPluginState();
    // ApproveReview is capability-gated on edit_post, so the approval test needs a
    // permitted user; the rest are indifferent to who is logged in.
    wp_set_current_user(createUser(['role' => 'administrator']));

    // THE TRIPWIRE for the Import suite's ordering.
    //
    // flushAfterCreated() returns immediately if WP_IMPORTING is defined — a review
    // arriving from a spreadsheet should not flush the page cache, and ten thousand
    // of them certainly should not. Correct, and it makes every flush test below
    // silently pass while asserting nothing, because nothing is flushed.
    //
    // The Import suite defines that constant and cannot undefine it, which is why it
    // is declared LAST in phpunit.xml. If it ever stops being last, this is where
    // you find out.
    expect(defined('WP_IMPORTING'))->toBeFalse();
});

/**
 * Records every time $hook fires, with the arguments it was given.
 */
function recordedAction(string $hook): ArrayObject
{
    $recorded = new ArrayObject([]);
    add_action($hook, function (...$args) use ($recorded) {
        $recorded[] = $args;
    }, 10, 5);

    return $recorded;
}

/**
 * Narrows the fan-out to a single cache plugin. The list is a filter precisely so
 * that a site can switch one off; here it isolates one purge method per test.
 */
function onlyCacheIntegration(string $slug): void
{
    add_filter('site-reviews/cache/integrations', fn () => [$slug => true]);
}

test('a new review purges the pages it is assigned to', function () {
    onlyCacheIntegration('litespeed');
    $purgedPost = recordedAction('litespeed_purge_post');
    $purgedAll = recordedAction('litespeed_purge_all');
    $postId = createPost();

    createReview(['assigned_posts' => $postId]);

    // flushAfterCreated merges the assigned posts with the post the form was on
    // (post_id, which is 0 here) and Arr::uniqueInt drops the 0.
    expect($purgedPost)->toHaveCount(1)
        ->and($purgedPost[0][0])->toBe($postId)
        ->and($purgedAll)->toHaveCount(0); // a targeted purge, not a blunt one
});

test('a review assigned to nothing purges everything', function () {
    onlyCacheIntegration('litespeed');
    $purgedPost = recordedAction('litespeed_purge_post');
    $purgedAll = recordedAction('litespeed_purge_all');

    createReview();

    expect($purgedAll)->toHaveCount(1)
        ->and($purgedPost)->toHaveCount(0);
});

test('the blunt purge can be switched off', function () {
    // The escape hatch for a site where an unassigned review should not blow away
    // the whole cache. Note it only guards the empty-array case: a null postIds —
    // which is what a migration or an explicit flush_all passes — still purges all.
    onlyCacheIntegration('litespeed');
    add_filter('site-reviews/cache/flush_when_empty_assigned_posts', '__return_false');
    $purgedAll = recordedAction('litespeed_purge_all');

    createReview();

    expect($purgedAll)->toHaveCount(0);
});

test('an unapproved review purges nothing', function () {
    // Nothing a visitor can see has changed, so there is nothing to purge.
    onlyCacheIntegration('litespeed');
    $purgedPost = recordedAction('litespeed_purge_post');
    $purgedAll = recordedAction('litespeed_purge_all');

    createReview(['assigned_posts' => createPost(), 'is_approved' => false]);

    expect($purgedPost)->toHaveCount(0)
        ->and($purgedAll)->toHaveCount(0);
});

test('approving a review purges the pages it is assigned to', function () {
    onlyCacheIntegration('litespeed');
    $postId = createPost();
    $review = createReview(['assigned_posts' => $postId, 'is_approved' => false]);
    $purgedPost = recordedAction('litespeed_purge_post'); // listen only from here

    (new ApproveReview(glsr_get_review($review->ID)))->handle();

    // ApproveReview calls wp_update_post directly rather than going through
    // ReviewManager::update, so site-reviews/review/transitioned fires and
    // site-reviews/review/updated does not — exactly one flush, from
    // flushAfterTransitioned. (The did_action() guards those two hooks carry are
    // there for the editor save, where both fire in the one request.)
    expect($purgedPost)->toHaveCount(1)
        ->and($purgedPost[0][0])->toBe($postId);
});

test('an explicit flush purges everything', function () {
    onlyCacheIntegration('litespeed');
    $purgedAll = recordedAction('litespeed_purge_all');

    do_action('site-reviews/cache/flush_all', 'a reason for the log');

    expect($purgedAll)->toHaveCount(1);
});

test('a flush for one review purges the pages that review is assigned to', function () {
    onlyCacheIntegration('litespeed');
    $postId = createPost();
    $review = createReview(['assigned_posts' => $postId]);
    $purgedPost = recordedAction('litespeed_purge_post');

    do_action('site-reviews/cache/flush', '', $review);

    expect($purgedPost)->toHaveCount(1)
        ->and($purgedPost[0][0])->toBe($postId);
});

test('the fan-out reaches every cache plugin that dispatches by action', function () {
    // No filter here: the default list of all thirteen. The four below are the
    // ones whose purge is a do_action, so they fire whether or not the cache
    // plugin is present. The other nine are guarded by function_exists() or
    // class_exists() and quietly do nothing — which is the assertion that matters:
    // the dispatcher walks all thirteen without a fatal.
    $endurance = recordedAction('epc_purge');
    $hummingbird = recordedAction('wphb_clear_page_cache');
    $litespeed = recordedAction('litespeed_purge_all');
    $wpFastestCache = recordedAction('wpfc_clear_all_cache');

    createReview();

    expect($endurance)->toHaveCount(1)
        ->and($hummingbird)->toHaveCount(1)
        ->and($litespeed)->toHaveCount(1)
        ->and($wpFastestCache)->toHaveCount(1);
});

test('an unknown cache plugin in the list is skipped', function () {
    // Helper::buildMethodName turns the slug into purgeSomethingElse, which does
    // not exist, so the dispatcher skips it rather than erroring.
    add_filter('site-reviews/cache/integrations', fn () => [
        'something_else' => true,
        'litespeed' => true,
    ]);
    $purgedAll = recordedAction('litespeed_purge_all');

    createReview();

    expect($purgedAll)->toHaveCount(1); // the real one still ran
});

test('a cache plugin switched off in the list is not purged', function () {
    add_filter('site-reviews/cache/integrations', fn () => [
        'endurance' => false,
        'litespeed' => true,
    ]);
    $endurance = recordedAction('epc_purge');
    $litespeed = recordedAction('litespeed_purge_all');

    createReview();

    expect($endurance)->toHaveCount(0)
        ->and($litespeed)->toHaveCount(1);
});

test('the cloudflare integration advertises the plugin purge actions', function () {
    // The companion to purgeCloudflare(): the official Cloudflare plugin purges
    // when one of the actions on these two lists fires, so the integration adds
    // the actions Site Reviews will fire. Both are plain filters — the Cloudflare
    // plugin does not have to be installed for the integration to register them.
    expect(apply_filters('cloudflare_purge_url_actions', ['existing']))
        ->toBe(['existing', 'site-reviews/cloudflare/purge']);
    expect(apply_filters('cloudflare_purge_everything_actions', []))
        ->toBe(['site-reviews/cloudflare/purge_all']);
});
