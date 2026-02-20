<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Str;

class MigrateElementor implements MigrateContract
{
    public const MAPPED_KEYS = [
        'alignment' => 'style_text_align',
        'max_width' => 'style_max_width',
        'percentage_bar_height' => 'style_bar_size',
        'percentage_bar_spacing' => 'style_bar_gap',
        'rating_color' => 'style_rating_color',
        'rating_size' => 'style_rating_size',
        'spacing' => 'style_row_gap',
    ];

    /**
     * Run migration.
     */
    public function run(): bool
    {
        if (!class_exists('Elementor\Plugin')) {
            return false;
        }
        $sql = "
            SELECT p.ID
            FROM table|posts p
            INNER JOIN table|postmeta pm ON (pm.post_id = p.ID)
            WHERE 1=1
            AND p.post_type != 'revision'
            AND pm.meta_key = '_elementor_data'
            AND pm.meta_value LIKE %s
        ";
        $postIds = glsr(Database::class)->dbGetCol(
            glsr(Query::class)->sql($sql, '%"widgetType":"site_review%')
        );
        $updated = false;
        foreach ($postIds as $postId) {
            $document = \Elementor\Plugin::$instance->documents->get((int) $postId);
            if (!$document || !$document->is_built_with_elementor()) {
                continue;
            }
            $data = $document->get_elements_data();
            if (empty($data)) {
                continue;
            }
            $migrated = \Elementor\Plugin::$instance->db->iterate_data(
                $data,
                $this->migrateElement(...)
            );
            if ($migrated !== $data) {
                $document->update_json_meta('_elementor_data', $migrated); // @phpstan-ignore-line
                $updated = true;
            }
        }
        return $updated;
    }

    protected function migrateElement(array $element): array
    {
        if (!$this->shouldMigrate($element)) {
            return $element;
        }
        $settings = $element['settings'];
        $settings = $this->updateAssignments($settings);
        $settings = $this->updateCheckboxValues($settings);
        $settings = $this->updateStyleKeys($settings, $element['widgetType'] ?? '');
        $element['settings'] = $settings;
        return $element;
    }

    protected function shouldMigrate(array $element): bool
    {
        return 'widget' === ($element['elType'] ?? '')
            && str_starts_with($element['widgetType'] ?? '', 'site_review')
            && !empty($element['settings']);
    }

    protected function splitKeyAndBreakpoint(string $key): array
    {
        if (preg_match('/^(.*?)_(tablet|mobile)$/', $key, $m)) {
            return [$m[1], "_{$m[2]}"];
        }
        return [$key, ''];
    }

    protected function updateAssignments(array $settings): array
    {
        $assignments = [
            'assigned_posts' => 'assigned_posts_custom',
            'assigned_users' => 'assigned_users_custom',
        ];
        foreach ($assignments as $field => $customField) {
            if ('custom' === ($settings[$field] ?? '')) {
                $settings[$field] = $settings[$customField] ?? '';
            }
            unset($settings[$customField]);
        }
        return $settings;
    }

    protected function updateCheckboxValues(array $settings): array
    {
        $groups = [
            'hide' => 'hide-',
            'filters' => 'filter-', // Review Filters addon
        ];
        $collected = [];
        foreach ($settings as $key => $value) {
            foreach ($groups as $group => $prefix) {
                if (str_starts_with($key, $prefix) && !empty($value)) {
                    $collected[$group] ??= [];
                    $collected[$group][] = Str::removePrefix($key, $prefix);
                    unset($settings[$key]);
                    break;
                }
            }
        }
        foreach ($collected as $group => $values) {
            $values = array_filter(array_unique($values));
            $settings[$group] = implode(',', $values);
        }
        return $settings;
    }

    protected function updateStyleKeys(array $settings, string $widgetType): array
    {
        $results = [];
        foreach ($settings as $key => $value) {
            if ('__globals__' === $key) {
                $results['__globals__'] = $this->updateStyleKeys($value, $widgetType);
                continue;
            }
            [$baseKey, $breakpoint] = $this->splitKeyAndBreakpoint($key);
            $mappedKey = static::MAPPED_KEYS[$baseKey] ?? $baseKey;
            $results[$mappedKey.$breakpoint] = $value;
            // migrate the summary bar color into its own value
            if ('site_reviews_summary' === $widgetType && 'style_rating_color' === $mappedKey) {
                $results['style_bar_color'.$breakpoint] = $value;
            }
        }
        return $results;
    }
}
