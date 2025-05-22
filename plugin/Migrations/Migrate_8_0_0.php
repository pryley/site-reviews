<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables\TableStats;
use GeminiLabs\SiteReviews\Helpers\Str;

class Migrate_8_0_0 implements MigrateContract
{
    protected bool $updateElementor;

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
            $this->updateElementor = false;
            $document = \Elementor\Plugin::$instance->documents->get($postId);
            if ($document) {
                $data = $document->get_elements_data(); // @phpstan-ignore-line
            }
            if (empty($data)) {
                continue;
            }
            $data = \Elementor\Plugin::$instance->db->iterate_data($data, function ($element) {
                $shortcode = $element['widgetType'] ?? '';
                if (!str_starts_with($shortcode, 'site_review')) {
                    return $element;
                }
                if (empty($element['settings'])) {
                    return $element;
                }
                $element = $this->updateElementorAssignments($element);
                $element = $this->updateElementorCheckboxes($element);
                $element = $this->updateElementorStyles($element);
                return $element;
            });
            if (!$this->updateElementor) { // @phpstan-ignore-line
                continue;
            }
            // We need the `wp_slash` because `update_post_meta` does `wp_unslash`
            $json = wp_slash(wp_json_encode($data)); // @phpstan-ignore-line
            update_metadata('post', $postId, '_elementor_data', $json);
        }
    }

    protected function updateElementorAssignments(array $element): array
    {
        $assignments = [
            'assigned_posts' => 'assigned_posts_custom',
            'assigned_users' => 'assigned_users_custom',
        ];
        foreach ($assignments as $assignment => $custom) {
            if ('custom' === ($element['settings'][$assignment] ?? '')) {
                $this->updateElementor = true;
                $element['settings'][$assignment] = $element['settings'][$custom] ?? '';
            }
            if (isset($element['settings'][$custom])) {
                $this->updateElementor = true;
                unset($element['settings'][$custom]);
            }
        }
        return $element;
    }

    protected function updateElementorCheckboxes(array $element): array
    {
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
                $this->updateElementor = true;
                $element['settings'][$setting] = implode(',', $r['values']);
            }
        }
        return $element;
    }

    protected function updateElementorStyles(array $element): array
    {
        $replacements = [
            'alignment' => 'style_align',
            'alignment_mobile' => 'style_align_mobile',
            'alignment_tablet' => 'style_align_tablet',
            'max_width' => 'style_max_width',
            'max_width_mobile' => 'style_max_width_mobile',
            'max_width_tablet' => 'style_max_width_tablet',
            'percentage_bar_height' => 'style_bar_size',
            'percentage_bar_height_mobile' => 'style_bar_size_mobile',
            'percentage_bar_height_tablet' => 'style_bar_size_tablet',
            'percentage_bar_spacing' => 'style_bar_gap',
            'percentage_bar_spacing_mobile' => 'style_bar_gap_mobile',
            'percentage_bar_spacing_tablet' => 'style_bar_gap_tablet',
            'rating_color' => 'style_rating_color',
            'rating_size' => 'style_rating_size',
            'rating_size_mobile' => 'style_rating_size_mobile',
            'rating_size_tablet' => 'style_rating_size_tablet',
            'rating_spacing' => 'style_rating_gap',
            'rating_spacing_mobile' => 'style_rating_gap_mobile',
            'rating_spacing_tablet' => 'style_rating_gap_tablet',
            'spacing' => 'style_row_gap',
            'spacing_mobile' => 'style_row_gap_mobile',
            'spacing_tablet' => 'style_row_gap_tablet',
        ];
        foreach ($replacements as $old => $new) {
            if (!array_key_exists($old, $element['settings'])) {
                continue;
            }
            $element['settings'][$new] = $element['settings'][$old];
            unset($element['settings'][$old]);
            $this->updateElementor = true;
        }
        if ($color = $element['settings']['__globals__']['rating_color'] ?? false) {
            $element['settings']['__globals__']['style_rating_color'] = $color;
            unset($element['settings']['__globals__']['rating_color']);
            $this->updateElementor = true;
        }
        return $element;
    }
}
