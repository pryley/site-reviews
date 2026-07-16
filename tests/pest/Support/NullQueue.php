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

    public function async(string $hook, array $args = [], bool $unique = false)
    {
        return 0;
    }

    public function cancel(string $hook, array $args = [])
    {
        return null;
    }

    public function cancelAll(string $hook, array $args = [])
    {
        // nothing was scheduled, so there is nothing to cancel
    }

    public function cron(int $timestamp, string $schedule, string $hook, array $args = [], bool $unique = false)
    {
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
        return 0;
    }

    public function recurring(int $timestamp, int $intervalInSeconds, string $hook, array $args = [], bool $unique = false)
    {
        return 0;
    }

    public function search(array $args = [], string $returnFormat = OBJECT)
    {
        return [];
    }
}
