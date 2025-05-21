<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables\TableStats;
use GeminiLabs\SiteReviews\Helpers\Str;

class Migrate_8_0_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->migrateElementor();
        return $this->migrateDatabase();
    }

    public function migrateDatabase(): bool
    {
        $isDirty = false;
        $indexes = glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql("SHOW INDEXES FROM table|ratings")
        );
        $keyNames = wp_list_pluck($indexes, 'Key_name');
        if (!in_array('glsr_ratings_ip_address_index', $keyNames)) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings ADD INDEX glsr_ratings_ip_address_index (ip_address)
            ");
            if (false === glsr(Database::class)->dbQuery($sql)) {
                glsr_log()->error("The ratings table could not be altered, the [ip_address_index] index was not added.");
                $isDirty = true;
            }
        }
        glsr(TableStats::class)->create();
        glsr(TableStats::class)->addForeignConstraints();
        if (!glsr(TableStats::class)->exists() || $isDirty) {
            return false;
        }
        update_option(glsr()->prefix.'db_version', '1.5');
        return true;
    }

    public function migrateElementor(): void
    {
        if (!class_exists('Elementor\Plugin')) {
            return;
        }
        $sql = glsr(Query::class)->sql("
            SELECT post_id
            FROM table|postmeta 
            WHERE meta_key = '_elementor_data'
            AND meta_value LIKE %s
        ", '%"widgetType":"site_review%');
        $postIds = glsr(Database::class)->dbGetCol($sql);
        if (empty($postIds)) {
            return;
        }
        foreach ($postIds as $postId) {
            $do_update = false;
            $document = \Elementor\Plugin::$instance->documents->get($postId);
            if ($document) {
                $data = $document->get_elements_data(); // @phpstan-ignore-line
            }
            if (empty($data)) {
                continue;
            }
            $data = \Elementor\Plugin::$instance->db->iterate_data($data, function ($element) use (&$do_update) {
                $widget = $element['widgetType'] ?? '';
                if (!str_starts_with($widget, 'site_review')) {
                    return $element;
                }
                if (empty($element['settings'])) {
                    return $element;
                }
                $assignments = [
                    'assigned_posts' => 'assigned_posts_custom',
                    'assigned_users' => 'assigned_users_custom',
                ];
                foreach ($assignments as $assignment => $custom) {
                    if ('custom' === ($element['settings'][$assignment] ?? '')) {
                        $element['settings'][$assignment] = $element['settings'][$custom] ?? '';
                        $do_update = true;
                    }
                    if (isset($element['settings'][$custom])) {
                        unset($element['settings'][$custom]);
                        $do_update = true;
                    }
                }
                $replacements = [
                    'hide' => [
                        'prefix' => 'hide-',
                        'values' => [],
                    ],
                    'filters' => [ // used by the Review Filters addon
                        'prefix' => 'filter-',
                        'values' => [],
                    ],
                ];
                foreach ($element['settings'] as $key => $value) {
                    foreach ($replacements as $setting => $r) {
                        if (str_starts_with($key, $r['prefix']) && !empty($value)) {
                            $replacements[$setting]['values'][] = Str::removePrefix($key, $r['prefix']);
                            unset($element['settings'][$key]);
                            break;
                        }
                    }
                }
                foreach ($replacements as $setting => $r) {
                    if (!empty($r['values'])) {
                        $element['settings'][$setting] = implode(',', $r['values']);
                        $do_update = true;
                    }
                }
                return $element;
            });
            if (!$do_update) {
                continue;
            }
            // We need the `wp_slash` because `update_post_meta` does `wp_unslash`
            $json = wp_slash(wp_json_encode($data));
            update_metadata('post', $postId, '_elementor_data', $json);
        }
    }
}
