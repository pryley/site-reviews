<?php

use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The queue, with a real Action Scheduler behind it.
 *
 * Every slow thing the plugin does happens here: geolocating an IP, sending a notification,
 * migrating a database, recalculating the counts. None of it happens in the request that asked
 * for it, and all of it is somebody else's library — Action Scheduler, which WooCommerce and
 * half the plugins on a typical site also use, from the same tables.
 *
 * The suite binds a NullQueue over this for every other test (see bootstrap.php), because
 * otherwise every review created in the suite would queue three jobs nobody asserts on. This
 * file is the exception NullQueue's docblock promises: it binds the REAL queue back, and is the
 * only place the plugin's own Queue code runs at all.
 *
 * The properties worth holding on to:
 *
 *   the GROUP     the queue is shared. Everything the plugin schedules goes into its own group,
 *                 and everything it reads back is filtered to that group — or it would be
 *                 cancelling WooCommerce's jobs.
 *   the PREFIX    likewise the hook name, so `queue/notification` cannot collide with somebody
 *                 else's `queue/notification`.
 *   UNIQUENESS    a review saved five times in a row must not queue five notifications.
 *
 * Action Scheduler's rows are ordinary rows in ordinary tables, so the per-test transaction
 * rolls them back like anything else.
 */

beforeEach(function () {
    resetPluginState();
    // The real thing, for this file only.
    glsr()->bind(Queue::class, Queue::class, $shared = true);
});

afterEach(function () {
    glsr()->bind(Queue::class, NullQueue::class, $shared = true);
});

function queue(): Queue
{
    return glsr(Queue::class);
}

/**
 * The actions Action Scheduler is holding, whoever put them there.
 */
function scheduledActions(array $args = []): array
{
    return as_get_scheduled_actions(wp_parse_args($args, ['per_page' => -1]), 'ids');
}

/*
 * Scheduling.
 */

test('a job is queued in the plugin\'s own group, under the plugin\'s own hook', function () {
    // Both halves matter. The group is what stops the plugin's Tools page listing (and
    // cancelling) WooCommerce's jobs; the hook prefix is what stops two plugins that both
    // scheduled `queue/notification` from running each other's callbacks.
    $actionId = queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 1]);

    expect($actionId)->toBeGreaterThan(0);

    $action = ActionScheduler::store()->fetch_action((string) $actionId);
    expect($action->get_hook())->toBe('site-reviews/queue/notification')
        ->and($action->get_group())->toBe('site-reviews')
        ->and($action->get_args())->toBe(['review_id' => 1]);
});

test('an async job is queued to run as soon as possible', function () {
    $actionId = queue()->async('queue/notification', ['review_id' => 1]);

    expect($actionId)->toBeGreaterThan(0);
    expect(queue()->isPending('queue/notification', ['review_id' => 1]))->toBeTrue();
});

test('a recurring job repeats', function () {
    $actionId = queue()->recurring(time() + HOUR_IN_SECONDS, DAY_IN_SECONDS, 'queue/sync');

    $action = ActionScheduler::store()->fetch_action((string) $actionId);
    expect($action->get_schedule()->is_recurring())->toBeTrue();
});

test('the same job is not queued twice', function () {
    // A review saved five times in a row must not send five notifications. `unique` is what
    // Action Scheduler checks against the pending actions with the same hook AND args.
    $first = queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 1], true);
    $second = queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 1], true);

    expect($first)->toBeGreaterThan(0)
        ->and($second)->toBe(0); // refused

    // …but a job for a DIFFERENT review is a different job
    $other = queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 2], true);
    expect($other)->toBeGreaterThan(0);
});

/*
 * Reading it back. Everything here is filtered to the plugin's own group — the queue is shared.
 */

test('the plugin only ever sees its own jobs', function () {
    queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification');
    $theirs = as_schedule_single_action(time() + HOUR_IN_SECONDS, 'woocommerce/whatever', [], 'woocommerce');

    $found = queue()->search(['status' => Queue::STATUS_PENDING]);

    expect($found)->not->toBeEmpty();
    foreach ($found as $action) {
        expect($action->get_group())->toBe('site-reviews');
    }
    // and the counts on the Tools page are ours alone
    expect(array_sum(array_column(queue()->actionCounts(), 'count')))->toBe(1);

    // …while theirs is still sitting there, untouched. (as_get_scheduled_actions() hands the
    // ids back as strings; as_schedule_single_action() returned an int.)
    expect(array_map('intval', scheduledActions()))->toContain($theirs);
});

test('asking whether a job is pending is asking about our own', function () {
    as_schedule_single_action(time() + HOUR_IN_SECONDS, 'site-reviews/queue/notification', [], 'woocommerce');

    // Same hook, somebody else's group. Not ours, and not pending as far as we are concerned.
    expect(queue()->isPending('queue/notification'))->toBeFalse();

    queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification');
    expect(queue()->isPending('queue/notification'))->toBeTrue();
});

test('when a job will next run', function () {
    $when = time() + 2 * HOUR_IN_SECONDS;
    queue()->once($when, 'queue/geolocation');

    $next = queue()->next('queue/geolocation');

    expect($next)->toBeInstanceOf(DateTime::class)
        ->and($next->getTimestamp())->toBe($when);
});

test('a job that was never queued is not pending, and has no next run', function () {
    expect(queue()->isPending('queue/notification'))->toBeFalse()
        ->and(queue()->next('queue/notification'))->toBeFalse()
        ->and(queue()->search(['status' => Queue::STATUS_PENDING]))->toBe([]);
});

/*
 * Cancelling.
 */

test('a job can be cancelled, and somebody else\'s is left alone', function () {
    queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 1]);
    $theirs = as_schedule_single_action(time() + HOUR_IN_SECONDS, 'site-reviews/queue/notification', ['review_id' => 1], 'woocommerce');

    queue()->cancel('queue/notification', ['review_id' => 1]);

    expect(queue()->isPending('queue/notification', ['review_id' => 1]))->toBeFalse();
    // the identical hook and args in somebody else's group survives
    $survivor = ActionScheduler::store()->fetch_action((string) $theirs);
    expect($survivor->get_schedule()->get_date())->not->toBeNull();
});

test('every job of a kind can be cancelled at once', function () {
    queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 1]);
    queue()->once(time() + HOUR_IN_SECONDS, 'queue/notification', ['review_id' => 2]);

    queue()->cancelAll('queue/notification');

    expect(queue()->search(['hook' => 'queue/notification', 'status' => Queue::STATUS_PENDING]))
        ->toBe([]);
});
