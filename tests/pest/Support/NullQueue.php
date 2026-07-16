<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Queue;

/**
 * A Queue that schedules nothing.
 *
 * The plugin does its slow work through Action Scheduler — a geolocation lookup, a notification
 * and an avatar per review. Letting that be queued would write thousands of action rows nothing
 * asserts on and make every review-creating test pay for a subsystem it is not testing.
 *
 * A container binding (see bootstrap.php) rather than a flag inside Queue: the plugin carries no
 * test-only branch, the fake is readable, and a test that WANTS a real queue can bind the real
 * one back for its duration. ScheduledActionsTest does exactly that, scheduling with Action
 * Scheduler's own as_schedule_single_action() in the plugin's group — what Queue::once() does
 * unfaked.
 *
 * The RETURN VALUES are not arbitrary: they match what Queue's methods return when Action
 * Scheduler is not loaded (each opens with a function_exists check), a state the plugin already
 * handles — so nothing downstream sees anything it would not on a site where the library failed
 * to load.
 */
class NullQueue extends Queue
{
    /**
     * Whether the queue should claim to have work pending. GeolocateReviews releases a stale
     * processing lock when NOTHING is pending — what stops a worker that died mid-batch from
     * locking the site out of geolocation for an hour. A queue that always says "nothing pending"
     * would make that the only branch, and the lock could never be honoured.
     */
    public static bool $isPending = false;

    /**
     * Every scheduling call, in order: ['method' => …, 'hook' => …, 'args' => …, 'timestamp' => …].
     * Scheduling still doesn't HAPPEN — but it is now observable, so "was it queued / not queued"
     * can be a real assertion instead of a constant. Cleared by resetGlobalState() per test.
     * calls('async', 'queue/notification') filters by method and/or hook.
     */
    public static array $calls = [];

    /**
     * @return array[] the recorded calls, filtered by method and/or hook when given
     */
    public static function calls(string $method = '', string $hook = ''): array
    {
        return array_values(array_filter(static::$calls,
            fn (array $call) => ('' === $method || $call['method'] === $method)
                && ('' === $hook || $call['hook'] === $hook)
        ));
    }

    protected static function record(string $method, string $hook, array $args, int $timestamp = 0): void
    {
        static::$calls[] = [
            'method' => $method,
            'hook' => $hook,
            'args' => $args,
            'timestamp' => $timestamp,
        ];
    }

    public function async(string $hook, array $args = [], bool $unique = false)
    {
        static::record('async', $hook, $args);
        return 0;
    }

    public function cancel(string $hook, array $args = [])
    {
        static::record('cancel', $hook, $args);
        return null;
    }

    public function cancelAll(string $hook, array $args = [])
    {
        static::record('cancelAll', $hook, $args);
    }

    public function cron(int $timestamp, string $schedule, string $hook, array $args = [], bool $unique = false)
    {
        static::record('cron', $hook, $args, $timestamp);
        return 0;
    }

    public function isPending(string $hook, array $args = []): bool
    {
        return static::$isPending;
    }

    public function next(string $hook, array $args = [])
    {
        return false;
    }

    public function once(int $timestamp, string $hook, array $args = [], bool $unique = false)
    {
        static::record('once', $hook, $args, $timestamp);
        return 0;
    }

    public function recurring(int $timestamp, int $intervalInSeconds, string $hook, array $args = [], bool $unique = false)
    {
        static::record('recurring', $hook, $args, $timestamp);
        return 0;
    }

    public function search(array $args = [], string $returnFormat = OBJECT)
    {
        return [];
    }
}
