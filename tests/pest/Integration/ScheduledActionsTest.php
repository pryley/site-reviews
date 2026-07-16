<?php

use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Overrides\ScheduledActionsTable;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Scheduled Actions tab on the Tools page.
 *
 * The plugin does its slow work through Action Scheduler, not the triggering request. This table is
 * the only window onto that queue — where someone goes when a notification never arrived, to see
 * whether the action is pending, failed, and why.
 *
 * It subclasses Action Scheduler's own list table, and almost all worth asserting is in the
 * overrides: which actions are shown (only the plugin's — the queue is shared with WooCommerce and
 * others), and which row actions are offered, which depends entirely on STATUS (re-running a run
 * action or cancelling a cancelled one is meaningless, and those buttons are not drawn).
 *
 * Note how actions are scheduled below: the suite binds a NullQueue, so Queue's methods return
 * without touching Action Scheduler — the actions go into the queue with Action Scheduler's own
 * function, in the plugin's group, exactly as Queue::once() does unfaked. Ordinary rows, so the
 * per-test transaction rolls them back.
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

/**
 * The admin notices the table would print above itself.
 */
function displayedNotices(ScheduledActionsTable $table): string
{
    ob_start();
    $table->display_admin_notices();

    return (string) ob_get_clean();
}

/**
 * A table backed by a different store than ActionScheduler::store().
 *
 * The wp-env site's store is a HybridStore (its database has been migrated from the old
 * post-based store), and a HybridStore SWALLOWS operations on a missing action id where the
 * DBStore — what most live sites run — throws. The tests for the table's error handling
 * stand in a DBStore, or one rigged to throw, this way.
 */
function tableWithStore(\ActionScheduler_Store $store): ScheduledActionsTable
{
    $table = new ScheduledActionsTable();
    $property = new ReflectionProperty(ScheduledActionsTable::class, 'store');
    $property->setAccessible(true);
    $property->setValue($table, $store);

    return $table;
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

test('an action on a cron schedule shows its cron expression as the recurrence', function () {
    // A cron recurrence is an expression, not a number of seconds, so it cannot be put
    // into words with Date::interval — it is shown as-is.
    $id = (int) as_schedule_cron_action(time(), '0 4 * * *', glsr()->id.'/queue/sync', [], glsr()->id);

    expect(preparedItems(actionsTable())[$id]['recurrence'])->toBe('0 4 * * *');
});

test('an action that is overdue says how long ago it should have run', function () {
    $id = scheduleAction('queue/notification', [], time() - 2 * HOUR_IN_SECONDS - 30);
    $table = actionsTable();

    expect($table->column_schedule(preparedItems($table)[$id]))->toContain('ago');
});

test('an action with no date at all shows a zero date rather than nothing', function () {
    // What a corrupted action's NullSchedule comes back with.
    expect(actionsTable()->column_schedule(['schedule' => new ActionScheduler_NullSchedule()]))
        ->toBe('0000-00-00 00:00:00');
});

test('a corrupted action is left out of the table rather than breaking it', function () {
    // Action Scheduler stores args as JSON; a row whose args no longer decode (a crashed
    // write, a bad migration) comes back from the store as a NullAction, and the table
    // skips it instead of rendering a broken row.
    global $wpdb;
    $good = scheduleAction('queue/notification');
    $bad = scheduleAction('queue/geolocation');
    $wpdb->update($wpdb->actionscheduler_actions, ['args' => '{corrupt'], ['action_id' => $bad]);

    $items = preparedItems(actionsTable());

    expect($items)->toHaveKey($good)
        ->not->toHaveKey($bad);
});

test('an action the store cannot read at all is skipped, not fatal', function () {
    // The store is pluggable (the action_scheduler_store_class filter), and fetch_action()
    // is not guaranteed not to throw on other stores the way ActionScheduler_DBStore is —
    // prepare_items() treats an unreadable action like a missing one.
    scheduleAction('queue/notification');
    $table = tableWithStore(new class extends ActionScheduler_DBStore {
        public function fetch_action($action_id)
        {
            throw new RuntimeException('unreadable');
        }
    });

    expect(preparedItems($table))->toBe([]);
});

test('sorting respects an explicit direction, and defaults newest-first by schedule', function () {
    $table = actionsTable();
    $order = protectedMethod(ScheduledActionsTable::class, 'get_request_order');

    $_GET['order'] = 'desc';
    expect($order->invoke($table))->toBe('DESC');

    $_GET['order'] = 'asc';
    expect($order->invoke($table))->toBe('ASC');

    unset($_GET['order']); // nothing chosen: the schedule column sorts newest-first...
    expect($order->invoke($table))->toBe('DESC');

    $_GET['orderby'] = 'hook'; // ...and any other column sorts ascending
    expect($order->invoke($table))->toBe('ASC');
});

test('bulk delete logs the action it cannot delete and still deletes the rest', function () {
    // A row deleted by a cron run between the page load and the submit throws on a DBStore
    // site (see tableWithStore) — logged, and it must not stop the ones that are still
    // there from being deleted.
    $real = scheduleAction('queue/notification');
    $table = tableWithStore(new class extends ActionScheduler_DBStore {
        public function delete_action($action_id)
        {
            if ('999999999' === $action_id) {
                throw new InvalidArgumentException("Unidentified action {$action_id}");
            }
            parent::delete_action($action_id);
        }
    });

    protectedMethod(ScheduledActionsTable::class, 'bulk_delete')->invoke($table, [999999999, $real], '');

    expect(preparedItems($table))->not->toHaveKey($real);
});

/*
 * The notices above the table.
 */

test('running an action now executes it, and the notice says so', function () {
    // A harmless listener of this test's own, not queue/notification's real handler in
    // QueueHooks — that would make this depend on SendNotification's behaviour instead of
    // the table's. It cannot be a hook with NO listener either: Action Scheduler marks an
    // action failed when nothing is registered to run it.
    $listened = [];
    add_action(glsr()->id.'/queue/run-now-probe', function () use (&$listened) {
        $listened[] = current_action();
    });
    $id = scheduleAction('queue/run-now-probe');
    $table = actionsTable();

    protectedMethod(ScheduledActionsTable::class, 'row_action_run')->invoke($table, $id);

    expect($listened)->toBe(['site-reviews/queue/run-now-probe']);
    expect(ActionScheduler::store()->get_status((string) $id))->toBe(Queue::STATUS_COMPLETE);
    expect(displayedNotices($table))->toContain('Successfully executed action')
        ->toContain('site-reviews/queue/run-now-probe');

    // the notice is shown once: displaying consumed the transient behind it
    expect(get_transient('action_scheduler_admin_notice'))->toBeFalse();
});

test('cancelling and deleting have their own notices', function () {
    $cancelled = scheduleAction('queue/notification');
    $table = actionsTable();
    protectedMethod(ScheduledActionsTable::class, 'row_action_cancel')->invoke($table, $cancelled);
    expect(displayedNotices($table))->toContain('Successfully canceled action');

    $deleted = scheduleAction('queue/geolocation');
    protectedMethod(ScheduledActionsTable::class, 'row_action_delete')->invoke($table, $deleted);
    expect(displayedNotices($table))->toContain('Successfully deleted action');
});

test('a retried action gets the generic notice', function () {
    // The notice switch knows run/cancel/delete by name; retry falls through to the
    // catch-all wording.
    $id = scheduleAction('queue/notification');
    ActionScheduler::store()->mark_failure((string) $id);
    $table = actionsTable();

    protectedMethod(ScheduledActionsTable::class, 'row_action_retry')->invoke($table, $id);

    expect(displayedNotices($table))->toContain('Successfully processed change for action');
});

test('a row action that fails says why, instead of pretending it worked', function () {
    // What ActionScheduler_DBStore::delete_action() throws when another process deleted
    // the row first — the table catches it and puts the error in the notice rather than
    // letting it fatal.
    $table = tableWithStore(new class extends ActionScheduler_DBStore {
        public function delete_action($action_id)
        {
            throw new InvalidArgumentException("Unidentified action {$action_id}");
        }
    });

    protectedMethod(ScheduledActionsTable::class, 'row_action_delete')->invoke($table, 999999999);

    expect(displayedNotices($table))->toContain('Could not process change for action')
        ->toContain('999999999')
        ->toContain('Unidentified action');
});

test('when every queue slot is already claimed, the table says no more will start', function () {
    // Zero allowed concurrent batches means the current claim count (zero) is already the
    // maximum — the cheapest way to stand in the state a busy site is in.
    add_filter('action_scheduler_queue_runner_concurrent_batches', '__return_zero');

    $html = displayedNotices(actionsTable());

    remove_filter('action_scheduler_queue_runner_concurrent_batches', '__return_zero');
    expect($html)->toContain('Maximum simultaneous queues already in progress');
});

test('when a queue is due but the runner lock is held, the notice says how long until it starts', function () {
    scheduleAction('queue/notification', [], time() - HOUR_IN_SECONDS); // due, and waiting
    ActionScheduler::lock()->set('async-request-runner');

    expect(displayedNotices(actionsTable()))
        ->toContain('The next queue will begin processing in approximately');
});

test('a missing database table is recreated, with a notice to say so', function () {
    // DROP TABLE is DDL, so MySQL commits the transaction implicitly — declared, and
    // Pest.php purges what stuck. The claims table is the one dropped because its rows
    // are ephemeral claims nothing else in the run depends on.
    commitsTransaction();
    global $wpdb;
    $claims = $wpdb->prefix.'actionscheduler_claims';
    $wpdb->query("DROP TABLE {$claims}");

    $html = displayedNotices(actionsTable());

    expect($html)->toContain('database tables were missing');
    expect($wpdb->get_var("SHOW TABLES LIKE '{$claims}'"))->toBe($claims);

    // A HybridStore recreates through itself; from a plain DBStore site the table builds
    // a HybridStore to do it, so both stores get the same schema back.
    $wpdb->query("DROP TABLE {$claims}");

    $html = displayedNotices(tableWithStore(new ActionScheduler_DBStore()));

    expect($html)->toContain('database tables were missing');
    expect($wpdb->get_var("SHOW TABLES LIKE '{$claims}'"))->toBe($claims);
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
