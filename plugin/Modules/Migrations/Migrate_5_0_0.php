<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Migrate_5_0_0
{
    public $db;
    public $limit;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->limit = 250;
    }

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
        $offset = 0;
        $table = glsr(Query::class)->table('ratings');
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT r.ID AS rating_id, m.meta_value AS post_id, CAST(IF(p.post_status = 'publish', 1, 0) AS UNSIGNED) AS is_approved
                FROM {$table} AS r
                INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
                INNER JOIN {$this->db->postmeta} AS m ON r.review_id = m.post_id
                WHERE m.meta_key = '_assigned_to' AND m.meta_value > 0 
                LIMIT %d, %d
            ", $offset, $this->limit), 'migrate-assigned-posts');
            $results = $this->db->get_results($sql, ARRAY_A);
            if (empty($results)) {
                break;
            }
            glsr(Database::class)->insertBulk('assigned_posts', $results, [
                'rating_id',
                'post_id',
                'is_approved',
            ]);
            $offset += $this->limit;
        }
    }

    /**
     * @return void
     */
    public function migrateRatings()
    {
        $offset = 0;
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT p.ID, m.meta_key AS mk, m.meta_value AS mv, CAST(IF(p.post_status = 'publish', 1, 0) AS UNSIGNED) AS is_approved
                FROM {$this->db->posts} AS p
                INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
                WHERE p.ID IN (
                    SELECT * FROM (
                        SELECT ID
                        FROM gl_posts
                        WHERE post_type = '%s'
                        LIMIT %d, %d
                    ) AS post_ids
                )
                AND m.meta_key IN ('_author','_avatar','_email','_ip_address','_pinned','_rating','_review_type','_url')
            ", glsr()->post_type, $offset, $this->limit), 'migrate-ratings');
            $results = $this->db->get_results($sql);
            if (empty($results)) {
                break;
            }
            $values = $this->parseRatings($results);
            $fields = array_keys(glsr(RatingDefaults::class)->defaults());
            glsr(Database::class)->insertBulk('ratings', $values, $fields);
            $offset += $this->limit;
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
        $offset = 0;
        $table = glsr(Query::class)->table('ratings');
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT r.ID AS rating_id, tt.term_id AS term_id
                FROM {$table} AS r
                INNER JOIN {$this->db->term_relationships} AS tr ON r.review_id = tr.object_id
                INNER JOIN {$this->db->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LIMIT %d, %d
            ", $offset, $this->limit), 'migrate-assigned-terms');
            $results = $this->db->get_results($sql, ARRAY_A);
            if (empty($results)) {
                break;
            }
            glsr(Database::class)->insertBulk('assigned_terms', $results, [
                'rating_id',
                'term_id',
            ]);
            $offset += $this->limit;
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
    public function migrateUserMeta()
    {
        $postType = glsr()->post_type;
        $metaKey = 'meta-box-order_'.$postType;
        $metaOrder = [
            'side' => [
                'submitdiv',
                $postType.'-categorydiv',
                $postType.'-postsdiv',
                $postType.'-usersdiv',
                $postType.'-authordiv',
            ],
            'normal' => [
                $postType.'-responsediv',
                $postType.'-detailsdiv',
            ],
            'advanced' => [],
        ];
        array_walk($metaOrder, function (&$order) {
            $order = implode(',', $order);
        });
        $userIds = get_users([
            'fields' => 'ID',
            'meta_compare' => 'EXISTS',
            'meta_key' => $metaKey,
        ]);
        foreach ($userIds as $userId) {
            update_user_meta($userId, $metaKey, $metaOrder);
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
            $oldWidget = 'widget_'.glsr()->id.'_'.$widget;
            $newWidget = 'widget_'.glsr()->prefix.$widget;
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
        $this->migrateUserMeta();
        $this->migrateWidgets();
        $this->migrateRatings();
        // $this->migrateAssignedTo();
        // $this->migrateTerms();
    }

    /**
     * @return array
     */
    protected function parseRatings(array $results)
    {
        $values = [];
        foreach ($results as $result) {
            $value = maybe_unserialize($result->mv);
            if (is_array($value)) {
                continue;
            }
            if (!isset($values[$result->ID])) {
                $values[$result->ID] = ['is_approved' => (int) $result->is_approved];
            }
            $values[$result->ID][$result->mk] = $value;
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
                if (Str::startsWith(glsr()->id.'_', $widget)) {
                    $widget = Str::replaceFirst(glsr()->id.'_', glsr()->prefix, $widget);
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
            if (Str::startsWith(glsr()->id.'_', $widget)) {
                return true;
            }
        }
        return false;
    }
}
