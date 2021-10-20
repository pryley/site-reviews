<?php

namespace GeminiLabs\SiteReviews\Modules;

use ActionScheduler_Store;
use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\QueueContract;

class Queue implements QueueContract
{
    const STATUS_CANCELED = ActionScheduler_Store::STATUS_CANCELED;
    const STATUS_COMPLETE = ActionScheduler_Store::STATUS_COMPLETE;
    const STATUS_FAILED = ActionScheduler_Store::STATUS_FAILED;
    const STATUS_PENDING = ActionScheduler_Store::STATUS_PENDING;
    const STATUS_RUNNING = ActionScheduler_Store::STATUS_RUNNING;

    /**
     * {@inheritdoc}
     */
    public function async($hook, $args = [], $group = Application::ID)
    {
        return as_enqueue_async_action($hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel($hook, $args = [], $group = Application::ID)
    {
        return as_unschedule_action($hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelAll($hook, $args = [], $group = Application::ID)
    {
        as_unschedule_all_actions($hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function cron($timestamp, $cron, $hook, $args = [], $group = Application::ID)
    {
        return as_schedule_cron_action($timestamp, $cron, $hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function isPending($hook, $args = [], $group = Application::ID)
    {
        return as_has_scheduled_action($hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function next($hook, $args = null, $group = Application::ID)
    {
        $next = as_next_scheduled_action($hook, $args, $group);
        if (is_numeric($next)) {
            return new \DateTime('@'.$next, new \DateTimeZone('UTC'));
        }
        return $next;
    }

    /**
     * {@inheritdoc}
     */
    public function once($timestamp, $hook, $args = [], $group = Application::ID)
    {
        return as_schedule_single_action($timestamp, $hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function recurring($timestamp, $intervalInSeconds, $hook, $args = [], $group = Application::ID)
    {
        return as_schedule_recurring_action($timestamp, $intervalInSeconds, $hook, $args, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function search($args = [], $returnFormat = OBJECT)
    {
        $args = wp_parse_args($args, ['group' => Application::ID]);
        return as_get_scheduled_actions($args, $returnFormat);
    }
}
