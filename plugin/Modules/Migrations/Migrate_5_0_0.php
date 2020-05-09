<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\AssignTerms;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Migrate_5_0_0
{
    /**
     * @return void
     */
    public function createDatabaseTable()
    {
        glsr(Database::class)->createTables();
    }

    /**
     * @return void
     */
    public function migrateRatings()
    {
        global $wpdb;
        $offset = 0;
        $limit = 100;
        $reviewCount = array_sum((array) wp_count_posts(glsr()->post_type));
        while ($reviewCount > 0) {
            $reviews = $wpdb->get_results($wpdb->prepare("
                SELECT p.ID, p.post_status AS status
                FROM {$wpdb->posts} AS p
                WHERE p.post_type = '%s'
                LIMIT %d, %d
            ", glsr()->post_type, $offset, $limit));
            foreach ($reviews as $review) {
                $rating = glsr(RatingManager::class)->insert($review->ID, [
                    'is_approved' => 'publish' === $review->status,
                    'is_pinned' => Helper::castToBool(get_post_meta($review->ID, '_pinned', true)),
                    'rating' => get_post_meta($review->ID, '_rating', true),
                    'type' => get_post_meta($review->ID, '_review_type', true),
                ]);
                if ($postId = get_post_meta($review->ID, '_assigned_to', true)) {
                    glsr(RatingManager::class)->assignPost($rating->ID, $postId);
                }
                $terms = wp_get_post_terms($review->ID, glsr()->taxonomy, ['fields' => 'ids']);
                if (!is_wp_error($terms) && !empty($terms)) {
                    (new AssignTerms($rating->ID, $terms))->handle();
                }
            }
            $offset += $limit;
            $reviewCount -= $limit;
        }
    }

    /**
     * @return void
     */
    public function migrateSettings()
    {
        if ($settings = get_option(OptionManager::databaseKey(4))) {
            update_option(OptionManager::databaseKey(5), $settings);
        }
    }

    /**
     * @return void
     */
    public function migrateSidebarWidgets()
    {
        $sidebars = Arr::consolidate(get_option('sidebars_widgets'));
        if ($this->widgetsExist($sidebars)) {
            $sidebars = $this->updateWidgetNames($sidebars);
            update_option('sidebars_widgets', $sidebars);
        }
    }

    /**
     * @return void
     */
    public function migrateThemeModWidgets()
    {
        $themes = $this->queryThemeMods();
        foreach ($themes as $theme) {
            $themeMod = get_option($theme);
            $sidebars = Arr::consolidate(Arr::get($themeMod, 'sidebars_widgets.data'));
            if ($this->widgetsExist($sidebars)) {
                $themeMod['sidebars_widgets']['data'] = $this->updateWidgetNames($sidebars);
                update_option($theme, $themeMod);
            }
        }
    }

    /**
     * @return void
     */
    public function migrateWidgets()
    {
        $widgets = [
            'site-reviews',
            'site-reviews-form',
            'site-reviews-summary',
        ];
        foreach ($widgets as $widget) {
            $oldWidget = 'widget_'.Application::ID.'_'.$widget;
            $newWidget = 'widget_'.Application::PREFIX.$widget;
            if ($option = get_option($oldWidget)) {
                update_option($newWidget, $option);
                delete_option($oldWidget);
            }
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->createDatabaseTable();
        $this->migrateRatings();
        $this->migrateSettings();
        $this->migrateSidebarWidgets();
        $this->migrateThemeModWidgets();
        $this->migrateWidgets();
    }

    /**
     * @return array
     */
    protected function queryThemeMods()
    {
        global $wpdb;
        return $wpdb->get_col("
            SELECT option_name 
            FROM {$wpdb->options} 
            WHERE option_name LIKE '%theme_mods_%'
        ");
    }

    /**
     * @param array $sidebars
     * @return array
     */
    protected function updateWidgetNames(array $sidebars)
    {
        array_walk($sidebars, function (&$widgets) {
            array_walk($widgets, function (&$widget) {
                if (Str::startsWith(Application::ID.'_', $widget)) {
                    $widget = Str::replaceFirst(Application::ID.'_', Application::PREFIX, $widget);
                }
            });
        });
        return $sidebars;
    }

    /**
     * @return bool
     */
    protected function widgetsExist(array $sidebars)
    {
        $widgets = call_user_func_array('array_merge', array_filter($sidebars, 'is_array'));
        foreach ($widgets as $widget) {
            if (Str::startsWith(Application::ID.'_', $widget)) {
                return true;
            }
        }
        return false;
    }
}
