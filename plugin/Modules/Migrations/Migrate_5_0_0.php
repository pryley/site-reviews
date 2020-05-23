<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\AssignTerms;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
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
    public function migrateAssignedTo()
    {
        global $wpdb;
        $offset = 0;
        $limit = 250;
        $table = glsr(Query::class)->getTable('ratings');
        while (true) {
            $results = $wpdb->get_results($wpdb->prepare("
                SELECT r.ID AS rating_id, m.meta_value AS post_id
                FROM {$table} AS r
                INNER JOIN {$wpdb->postmeta} AS m ON r.review_id = m.post_id
                WHERE m.meta_key = '_assigned_to' AND m.meta_value > 0 
                LIMIT %d, %d
            ", $offset, $limit), ARRAY_A);
            if (empty($results)) {
                break;
            }
            glsr(RatingManager::class)->insertBulk('assigned_posts', $results, [
                'rating_id',
                'post_id',
            ]);
            $offset += $limit;
        }
    }

    /**
     * @return void
     */
    public function migrateRatings()
    {
        global $wpdb;
        $offset = 0;
        $limit = 200;
        while (true) {
            $scope = $wpdb->get_col($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = '%s' LIMIT %d, %d",
                glsr()->post_type,
                $offset,
                $limit
            ));
            if (empty($scope)) {
                break;
            }
            $scope = implode(',', $scope);
            $results = $wpdb->get_results("
                SELECT p.ID, m.meta_key AS mk, m.meta_value AS mv, CAST(IF(p.post_status = 'publish', 1, 0) AS UNSIGNED) AS is_approved
                FROM {$wpdb->posts} AS p
                INNER JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id
                WHERE p.ID IN ({$scope})
            ");
            $values = $this->parseResults($results);
            $fields = array_keys(glsr(RatingDefaults::class)->defaults());
            glsr(RatingManager::class)->insertBulk('ratings', $values, $fields);
            $offset += $limit;
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
    public function migrateTerms()
    {
        global $wpdb;
        $offset = 0;
        $limit = 250;
        $table = glsr(Query::class)->getTable('ratings');
        while (true) {
            $results = $wpdb->get_results($wpdb->prepare("
                SELECT r.ID AS rating_id, tt.term_id AS term_id
                FROM {$table} AS r
                INNER JOIN {$wpdb->term_relationships} AS tr ON r.review_id = tr.object_id
                INNER JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LIMIT %d, %d
            ", $offset, $limit), ARRAY_A);
            if (empty($results)) {
                break;
            }
            glsr(RatingManager::class)->insertBulk('assigned_terms', $results, [
                'rating_id',
                'term_id',
            ]);
            $offset += $limit;
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
        $this->migrateSettings();
        $this->migrateSidebarWidgets();
        $this->migrateThemeModWidgets();
        $this->migrateWidgets();
        $this->migrateRatings();
        $this->migrateAssignedTo();
        $this->migrateTerms();
    }

    /**
     * @return array
     */
    protected function parseResults(array $results)
    {
        $values = [];
        foreach ($results as $result) {
            if (!isset($values[$result->ID])) {
                $values[$result->ID] = ['is_approved' => (int) $result->is_approved];
            }
            $values[$result->ID][$result->mk] = $result->mv;
        }
        $results = [];
        foreach ($values as $postId => $value) {
            $meta = Arr::unprefixKeys($value);
            $meta['name'] = Arr::get($meta, 'author');
            $meta['is_pinned'] = Arr::get($meta, 'pinned');
            $meta['review_id'] = $postId;
            $meta['type'] = Arr::get($meta, 'review_type');
            $meta = Arr::removeEmptyValues($meta);
            $meta = glsr(RatingDefaults::class)->restrict($meta);
            $results[] = $meta;
        }
        return $results;
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
