<?php

use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Overrides\ScheduledActionsTable;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Scheduled Actions tab on the Tools page.
 *
 * The plugin does its slow work — geolocating an IP, sending a notification, migrating a
 * database — through Action Scheduler rather than in the request that triggered it. This
 * table is the only window onto that queue, and it is where somebody goes when a
 * notification never arrived: to see whether the action is pending, whether it failed,
 * and why.
 *
 * It is a subclass of Action Scheduler's own list table, and almost all of what is worth
 * asserting is in the overrides: which actions are shown (only the plugin's — the queue
 * is shared with WooCommerce and half a dozen other plugins on a typical site), and which
 * row actions a person is offered, which depends entirely on the action's STATUS. Running
 * an action that has already run, or cancelling one that has already been cancelled, are
 * both meaningless, and the buttons for them are not drawn.
 *
 * Note how the actions are scheduled below. Queue is inert under GLSR_UNIT_TESTS — every
 * one of its scheduling methods short-circuits — so the actions are put into the queue
 * with Action Scheduler's own function, in the plugin's group, which is exactly what
 * Queue::once() does when it is not being tested. They are ordinary rows in ordinary
 * tables, so the per-test transaction rolls them back.
 */

uses(InteractsWithExits::class);

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    $_GET = [];
    $_REQUEST = [];
});

afterEach(function () {
    set_current_screen('front');
    $_GET = [];
    $_REQUEST = [];
});

/**
 * An action in the plugin's own queue, exactly as Queue::once() would put it there.
 */
function scheduleAction(string $hook, array $args = [], int $timestamp = 0): int
{
    return (int) as_schedule_single_action(
        $timestamp ?: time() + HOUR_IN_SECONDS,
        glsr()->id.'/'.$hook,
        $args,
        glsr()->id
    );
}

function actionsTable(): ScheduledActionsTable
{
    return new ScheduledActionsTable();
}

/**
 * The rows the table would draw, keyed by action id.
 */
function preparedItems(ScheduledActionsTable $table): array
{
    $table->prepare_items();

    return $table->items;
}

function displayedPage(ScheduledActionsTable $table): string
{
    ob_start();
    $table->display_page();

    return (string) ob_get_clean();
}

/**
 * The row-action links offered for one row, by name.
 *
 * Read out of the LINKS rather than out of the span classes, because the span class is
 * not the action name: Action Scheduler uses `class` from the action config when there is
 * one, so Cancel renders as `class="cancel trash"` and Delete as `class="trash"`.
 *
 * @return string[]
 */
function rowActionsFor(ScheduledActionsTable $table, array $row): array
{
    preg_match_all('/row_action=([a-z]+)/', $table->column_hook($row), $matches);

    return $matches[1];
}

/*
 * What is in the table.
 */

test('the table shows the plugin\'s actions, and nobody else\'s', function () {
    // Action Scheduler's queue is shared: WooCommerce, RankMath, WPForms and the rest all
    // put their actions in it. The group is what separates them, and a table that showed
    // everything would be useless the moment a second plugin used the library.
    $ours = scheduleAction('queue/notification', ['review_id' => 1]);
    $theirs = (int) as_schedule_single_action(time() + HOUR_IN_SECONDS, 'woocommerce/whatever', [], 'woocommerce');

    $items = preparedItems(actionsTable());

    expect(array_keys($items))->toContain($ours)
        ->not->toContain($theirs);
    expect($items[$ours]['hook'])->toBe('site-reviews/queue/notification');
    expect($items[$ours]['args'])->toBe(['review_id' => 1]);
});

test('an action that is waiting says when it will run, and what it will do', function () {
    $id = scheduleAction('queue/geolocation', [], time() + 2 * HOUR_IN_SECONDS);
    $table = actionsTable();
    $row = preparedItems($table)[$id];

    expect($table->column_schedule($row))->toContain(gmdate('Y-m-d', time() + 2 * HOUR_IN_SECONDS))
        ->toContain('hour'); // and how long that is from now, in words (Date::interval)

    expect($table->column_hook($row))->toContain('site-reviews/queue/geolocation');
});

test('an action that is not repeating says so', function () {
    $id = scheduleAction('queue/notification');

    expect(preparedItems(actionsTable())[$id]['recurrence'])->toBe('Non-repeating');
});

test('an action that repeats says how often', function () {
    // as_schedule_recurring_action, which is what Queue::recurring() calls.
    $id = (int) as_schedule_recurring_action(
        time() + HOUR_IN_SECONDS, DAY_IN_SECONDS, glsr()->id.'/queue/sync', [], glsr()->id
    );

    expect(preparedItems(actionsTable())[$id]['recurrence'])->toContain('Every');
});

test('the arguments an action was given are shown, without being run', function () {
    // var_export of the args, in a tooltip. It is how somebody works out WHICH review a
    // failed notification was for.
    $id = scheduleAction('queue/notification', ['review_id' => 42]);
    $table = actionsTable();
    $row = preparedItems($table)[$id];

    expect($table->column_args($row))->toContain('review_id')
        ->toContain('42');

    // and an action with no arguments has no tooltip to show
    $bare = scheduleAction('queue/migration');
    expect($table->column_args(preparedItems($table)[$bare]))->toBe('');
});

/*
 * What a person is offered, which depends on what the action has already done.
 */

test('an action that has not run yet can be run now, or cancelled', function () {
    // And NOT deleted or retried: there is nothing to retry, and cancelling is the safe
    // way to stop something that has not happened yet — it leaves the row to look at.
    $id = scheduleAction('queue/notification');
    $table = actionsTable();

    $actions = rowActionsFor($table, preparedItems($table)[$id]);

    expect($actions)->toContain('run')
        ->toContain('cancel')
        ->not->toContain('delete')
        ->not->toContain('retry');
});

test('an action that has already run can only be deleted', function () {
    // Running it again would run it twice; cancelling something that has finished means
    // nothing. All that is left is to tidy it away.
    $id = scheduleAction('queue/notification');
    ActionScheduler::store()->mark_complete((string) $id);
    $table = actionsTable();

    $actions = rowActionsFor($table, preparedItems($table)[$id]);

    expect($actions)->toBe(['delete']);
});

test('an action that failed can be retried, or deleted', function () {
    // The one that matters: a notification that failed is a notification the site owner
    // still wants sent, and Retry is how they send it without going and finding the
    // review.
    $id = scheduleAction('queue/notification');
    ActionScheduler::store()->mark_failure((string) $id);
    $table = actionsTable();

    $actions = rowActionsFor($table, preparedItems($table)[$id]);

    expect($actions)->toContain('retry')
        ->toContain('delete')
        ->not->toContain('run')
        ->not->toContain('cancel');
});

test('an action that has been cancelled is offered nothing at all', function () {
    $id = scheduleAction('queue/notification');
    ActionScheduler::store()->cancel_action((string) $id);
    $table = actionsTable();

    expect(rowActionsFor($table, preparedItems($table)[$id]))->toBe([]);
});

/*
 * And what pressing them does.
 */

test('cancelling an action stops it without throwing it away', function () {
    $id = scheduleAction('queue/notification');
    $table = actionsTable();

    protectedMethod(ScheduledActionsTable::class, 'row_action_cancel')->invoke($table, $id);

    expect(ActionScheduler::store()->get_status((string) $id))->toBe(Queue::STATUS_CANCELED);
    expect(preparedItems($table))->toHaveKey($id); // still there to look at
});

test('deleting an action throws it away', function () {
    $id = scheduleAction('queue/notification');
    $table = actionsTable();

    protectedMethod(ScheduledActionsTable::class, 'row_action_delete')->invoke($table, $id);

    expect(preparedItems($table))->not->toHaveKey($id);
});

test('retrying a failed action queues it again, under the same hook', function () {
    // It does NOT go through Queue — Queue would prefix the hook a second time, and the
    // action would be scheduled under a hook nothing is listening on. That is why
    // process_row_action() calls as_schedule_single_action() directly, and why it is
    // worth asserting the hook it comes back with.
    $id = scheduleAction('queue/notification', ['review_id' => 7]);
    ActionScheduler::store()->mark_failure((string) $id);
    $table = actionsTable();

    protectedMethod(ScheduledActionsTable::class, 'row_action_retry')->invoke($table, $id);

    $items = preparedItems($table);
    expect($items)->not->toHaveKey($id)  // the failed one is gone
        ->and($items)->toHaveCount(1);   // replaced by exactly one

    $retried = reset($items);
    expect($retried['hook'])->toBe('site-reviews/queue/notification')
        ->and($retried['args'])->toBe(['review_id' => 7])
        ->and($retried['status_name'])->toBe(Queue::STATUS_PENDING);
});

test('a row action is refused without the nonce that goes with it', function () {
    // Every link carries a nonce built from the action name and the row id, so a link for
    // one action cannot be used against another. process_row_actions() checks it before
    // it dispatches to anything.
    $id = scheduleAction('queue/notification');
    $table = actionsTable();

    $_REQUEST = [
        'row_action' => 'delete',
        'row_id' => $id,
        'nonce' => wp_create_nonce('delete::999999'), // for a different row
    ];

    // and it redirects either way, so there is nothing to be learnt from the response —
    // only from whether the action is still there
    $this->expectsRedirect(fn () => protectedMethod(ScheduledActionsTable::class, 'process_row_actions')->invoke($table));

    expect(preparedItems($table))->toHaveKey($id); // untouched
});

test('a row action with its own nonce is carried out', function () {
    $id = scheduleAction('queue/notification');
    $table = actionsTable();

    $_REQUEST = [
        'row_action' => 'cancel',
        'row_id' => $id,
        'nonce' => wp_create_nonce("cancel::{$id}"),
    ];

    $this->expectsRedirect(fn () => protectedMethod(ScheduledActionsTable::class, 'process_row_actions')->invoke($table));

    expect(ActionScheduler::store()->get_status((string) $id))->toBe(Queue::STATUS_CANCELED);
});

test('several actions can be deleted at once', function () {
    $one = scheduleAction('queue/notification');
    $two = scheduleAction('queue/geolocation');
    $table = actionsTable();

    protectedMethod(ScheduledActionsTable::class, 'bulk_delete')->invoke($table, [$one, $two], '');

    $items = preparedItems($table);
    expect($items)->not->toHaveKey($one)
        ->and($items)->not->toHaveKey($two);
});

/*
 * The page.
 */

test('the page draws the table, with the actions in it', function () {
    scheduleAction('queue/notification', ['review_id' => 1]);
    scheduleAction('queue/geolocation');

    $html = displayedPage(actionsTable());

    expect($html)->toContain('site-reviews/queue/notification')
        ->toContain('site-reviews/queue/geolocation')
        ->toContain('action-pending')     // the row class, which is what colours it
        ->toContain('Scheduled Date')     // the column headings
        ->toContain('Recurrence');
});

test('the page counts the actions by status, so that the failures can be found', function () {
    scheduleAction('queue/notification');
    $failed = scheduleAction('queue/geolocation');
    ActionScheduler::store()->mark_failure((string) $failed);

    $html = displayedPage(actionsTable());

    expect($html)->toContain('status=pending')
        ->toContain('status=failed');
});

test('the table can be narrowed to one status', function () {
    scheduleAction('queue/notification');
    $failed = scheduleAction('queue/geolocation');
    ActionScheduler::store()->mark_failure((string) $failed);
    $_GET['status'] = 'failed';

    $items = preparedItems(actionsTable());

    expect(array_keys($items))->toBe([$failed]);
});

test('there is no search box, because there is nothing worth searching', function () {
    expect(actionsTable()->search_box('Search', 'plugin'))->toBeNull();
});

test('the tools page hands the row actions to the table before it renders', function () {
    // This has to happen on `load-…`, not while the page is being drawn: a row action ends
    // in a redirect, and by the time the table is being rendered the headers have gone out
    // (which is what the comment on processPageActions says, and why it exists).
    $id = scheduleAction('queue/notification');
    $_REQUEST = [
        'row_action' => 'cancel',
        'row_id' => $id,
        'nonce' => wp_create_nonce("cancel::{$id}"),
    ];

    $this->expectsRedirect(fn () => glsr(MenuController::class)->processPageActions());

    expect(ActionScheduler::store()->get_status((string) $id))->toBe(Queue::STATUS_CANCELED);
});
