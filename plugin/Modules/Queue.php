<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\QueueContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Str;

class Queue implements QueueContract
{
    public const STATUS_CANCELED = \ActionScheduler_Store::STATUS_CANCELED;
    public const STATUS_COMPLETE = \ActionScheduler_Store::STATUS_COMPLETE;
    public const STATUS_FAILED = \ActionScheduler_Store::STATUS_FAILED;
    public const STATUS_PENDING = \ActionScheduler_Store::STATUS_PENDING;
    public const STATUS_RUNNING = \ActionScheduler_Store::STATUS_RUNNING;

    protected bool $isTesting = false;

    public function __construct()
    {
        $this->isTesting = defined('GLSR_UNIT_TESTS');
    }

    public function app(): PluginContract
    {
        return glsr();
    }

    public function actionCounts(): array
    {
        global $wpdb;
        $counts = [];
        $labels = \ActionScheduler_Store::instance()->get_status_labels();
        $sql = glsr(Query::class)->sql("
            SELECT a.status, count(a.status) as 'count'
            FROM {$wpdb->actionscheduler_actions} a
            INNER JOIN {$wpdb->actionscheduler_groups} g ON (g.group_id = a.group_id)
            WHERE g.slug = '%s'
            GROUP BY a.status
        ");
        $results = glsr(Database::class)->dbGetResults(
            sprintf($sql, glsr()->id)
        );
        foreach ($results as $result) {
            if (!array_key_exists($result->status, $labels)) {
                continue;
            }
            $counts[$result->status] = [
                'count' => $result->count,
                'latest' => $this->actionStatusDate($result->status, false),
                'oldest' => $this->actionStatusDate($result->status, true),
            ];
        }
        return $counts;
    }

    /**
     * {@inheritdoc}
     */
    public function async(string $hook, array $args = [], bool $unique = false)
    {
        if (!function_exists('as_enqueue_async_action') || $this->isTesting) {
            return 0;
        }
        return as_enqueue_async_action($this->hook($hook), $args, glsr()->id, $unique);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(string $hook, array $args = [])
    {
        if (!function_exists('as_unschedule_action') || $this->isTesting) {
            return null;
        }
        return as_unschedule_action($this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelAction($actionId)
    {
        \ActionScheduler_Store::instance()->cancel_action($actionId);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelAll(string $hook, array $args = [])
    {
        if (!function_exists('as_unschedule_all_actions') || $this->isTesting) {
            return;
        }
        if (empty($args)) {
            \ActionScheduler_Store::instance()->cancel_actions_by_hook($this->hook($hook));
        } elseif (function_exists('as_unschedule_all_actions')) {
            as_unschedule_all_actions($this->hook($hook), $args, glsr()->id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cron(int $timestamp, string $schedule, string $hook, array $args = [], bool $unique = false)
    {
        if (!function_exists('as_schedule_cron_action') || $this->isTesting) {
            return 0;
        }
        return as_schedule_cron_action($timestamp, $schedule, $this->hook($hook), $args, glsr()->id, $unique);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAction($actionId)
    {
        return \ActionScheduler_Store::instance()->fetch_action($actionId);
    }

    /**
     * {@inheritdoc}
     */
    public function isPending(string $hook, array $args = []): bool
    {
        if (!function_exists('as_has_scheduled_action') || $this->isTesting) {
            return false;
        }
        if (!empty($args)) {
            return (bool) as_has_scheduled_action($this->hook($hook), $args, glsr()->id);
        }
        return !empty(
            glsr(Queue::class)->search([
                'hook' => $this->hook($hook),
                'status' => static::STATUS_PENDING,
            ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function next(string $hook, array $args = [])
    {
        if (!function_exists('as_next_scheduled_action') || $this->isTesting) {
            return false;
        }
        $next = as_next_scheduled_action($this->hook($hook), $args, glsr()->id);
        if (is_numeric($next)) {
            return new \DateTime("@{$next}", new \DateTimeZone('UTC'));
        }
        return $next;
    }

    /**
     * {@inheritdoc}
     */
    public function once(int $timestamp, string $hook, array $args = [], bool $unique = false)
    {
        if (!function_exists('as_schedule_single_action') || $this->isTesting) {
            return 0;
        }
        return as_schedule_single_action($timestamp, $this->hook($hook), $args, glsr()->id, $unique);
    }

    /**
     * {@inheritdoc}
     */
    public function recurring(int $timestamp, int $intervalInSeconds, string $hook, array $args = [], bool $unique = false)
    {
        if (!function_exists('as_schedule_recurring_action') || $this->isTesting) {
            return 0;
        }
        return as_schedule_recurring_action($timestamp, $intervalInSeconds, $this->hook($hook), $args, glsr()->id, $unique);
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $args = [], string $returnFormat = OBJECT)
    {
        if (!function_exists('as_get_scheduled_actions') || $this->isTesting) {
            return [];
        }
        if (isset($args['hook'])) {
            $args['hook'] = $this->hook($args['hook']);
        }
        $args = wp_parse_args($args, ['group' => glsr()->id]);
        return as_get_scheduled_actions($args, $returnFormat);
    }

    protected function actionStatusDate(string $status, bool $oldest = true, string $format = 'Y-m-d H:i:s'): string
    {
        $order = $oldest ? 'ASC' : 'DESC';
        $action = \ActionScheduler_Store::instance()->query_actions([
            'claimed' => false,
            'group' => glsr()->id,
            'order' => $order,
            'per_page' => 1,
            'status' => $status,
        ]);
        if (!empty($action)) {
            $datetime = \ActionScheduler_Store::instance()->get_date($action[0]);
            $date = $datetime->format($format); // 'Y-m-d H:i:s O'
        } else {
            $date = '&ndash;';
        }
        return $date;
    }

    protected function hook(string $hook): string
    {
        return Str::prefix($hook, $this->app()->id.'/');
    }
}
