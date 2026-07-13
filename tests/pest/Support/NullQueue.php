<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Queue;

/**
 * A Queue that schedules nothing.
 *
 * The plugin does its slow work through Action Scheduler: a geolocation lookup per review,
 * a notification per review, an avatar per review. A suite that let all of that be queued
 * would be writing thousands of action rows it never asserts on, and every test that
 * creates a review would be paying for a subsystem it is not testing.
 *
 * This used to be a `defined('GLSR_UNIT_TESTS')` check inside Queue itself, on eight of its
 * methods. It is a container binding now (see bootstrap.php), which is better in three
 * ways: the plugin no longer carries a branch that only the tests take, the fake is
 * visible and can be read, and a test that WANTS a real queue can bind the real one back
 * for the length of the test.
 *
 * ScheduledActionsTest is that test, in effect: it puts actions into the queue with Action
 * Scheduler's own as_schedule_single_action(), in the plugin's group, which is exactly what
 * Queue::once() does when it is not being faked.
 *
 * The RETURN VALUES matter and are not arbitrary — they are what Queue's own methods return
 * when Action Scheduler is not loaded at all, which is a state the plugin already handles
 * (every one of them opens with a function_exists check). So nothing downstream sees
 * anything it would not see on a site where the library failed to load.
 */
class NullQueue extends Queue
{
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
        return false;
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
