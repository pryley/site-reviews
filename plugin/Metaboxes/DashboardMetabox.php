<?php

namespace GeminiLabs\SiteReviews\Metaboxes;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\DashboardDataDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Date;

class DashboardMetabox
{
    public function register(): void
    {
        $id = glsr()->prefix.'dashboard_widget';
        $title = __('Site Reviews Overview', 'site-reviews');
        wp_add_dashboard_widget($id, $title, [$this, 'render']);
    }

    public function render(): void
    {
        $data = $this->data();
        foreach ($data as $key => $values) {
            $data[$key] = glsr(DashboardDataDefaults::class)->restrict($values);
        }
        glsr()->render('partials/dashboard-widget', $data);
    }

    public function data(): array
    {
        $data = [ // order is intentional
            'total' => [
                'dashicon' => 'dashicons-star-half',
                'label' => _x('in total', 'admin-text', 'site-reviews'),
                'url' => glsr_admin_url(),
                'value' => $this->total(),
            ],
            'this_month' => [
                'dashicon' => 'dashicons-calendar',
                'label' => _x('this month', 'admin-text', 'site-reviews'),
                'url' => add_query_arg('m', wp_date('Ym'), glsr_admin_url()),
                'value' => $this->thisMonth(),
            ],
            'unapproved' => [
                'dashicon' => 'dashicons-clock',
                'label' => _x('awaiting approval', 'admin-text', 'site-reviews'),
                'url' => add_query_arg('post_status', 'pending', glsr_admin_url()),
                'value' => $this->unapproved(),
            ],
        ];
        $data = glsr()->filterArray('dashboard/widget/data', $data);
        return $data;
    }

    public function thisMonth(): int
    {
        $count = glsr(Cache::class)->get('monthly', 'count');
        if (glsr(Date::class)->isThisMonth(Arr::get($count, 'timestamp'))) {
            return Arr::getAs('int', $count, 'count');
        }
        $month = (int) date('m');
        $year = (int) date('Y');
        $sql = "
            SELECT COUNT(*) AS count
            FROM table|posts
            WHERE 1=1
            AND post_type = %s
            AND post_status IN ('pending','publish')
            AND YEAR(post_date) = %d
            AND MONTH(post_date) = %d
        ";
        $result = (int) glsr(Database::class)->dbGetVar(
            glsr(Query::class)->sql($sql, glsr()->post_type, $year, $month)
        );
        glsr(Cache::class)->store('monthly', 'count', [
            'count' => $result,
            'timestamp' => current_time('timestamp'),
        ]);
        return $result;
    }

    public function total(): int
    {
        $postCount = wp_count_posts(glsr()->post_type);
        return (int) $postCount->publish;
    }

    public function unapproved(): int
    {
        $postCount = wp_count_posts(glsr()->post_type);
        return (int) $postCount->pending;
    }
}
