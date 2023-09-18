<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class TaxonomyController extends Controller
{
    public const PRIORITY_META_KEY = 'term_priority';

    /**
     * @param array $columns
     * @return array
     * @filter manage_edit-{glsr()->taxonomy}_columns
     */
    public function filterColumns($columns)
    {
        if ($this->termPriorityEnabled()) {
            $columns[static::PRIORITY_META_KEY] = _x('Priority', 'admin-text', 'site-reviews');
        }
        return $columns;
    }

    /**
     * @param string $value
     * @param string $column
     * @param int $termId
     * @return string
     * @filter manage_{glsr()->taxonomy}_custom_column
     */
    public function filterColumnValue($value, $column, $termId)
    {
        if (static::PRIORITY_META_KEY !== $column || !$this->termPriorityEnabled()) {
            return $value;
        }
        return (string) $this->termPriority($termId);
    }

    /**
     * @param string[] $actions
     * @param \WP_Term $term
     * @return array
     * @filter {glsr()->taxonomy}_row_actions
     */
    public function filterRowActions($actions, $term)
    {
        $action = ['id' => sprintf('<span>ID: %d</span>', $term->term_id)];
        return array_merge($action, $actions);
    }

    /**
     * @param string[] $clauses
     * @param string[] $taxonomies
     * @param array $args
     * @return array
     * @filter terms_clauses
     */
    public function filterTermsClauses($clauses, $taxonomies, $args)
    {
        if (is_admin()) {
            return $clauses;
        }
        if ($taxonomies !== [glsr()->taxonomy]) {
            return $clauses;
        }
        if (!$this->termPriorityEnabled()) {
            return $clauses;
        }
        if (!$this->termPriorityExists()) {
            return $clauses;
        }
        $meta = new \WP_Meta_Query();
        $meta->parse_query_vars($args);
        $meta->get_sql('term', 't', 'term_id');
        if (empty($meta->get_clauses())) {
            global $wpdb;
            $clauses['join'] .= $wpdb->prepare(" LEFT JOIN {$wpdb->termmeta} AS tm ON (t.term_id = tm.term_id AND tm.meta_key = %s) ", static::PRIORITY_META_KEY);
            $clauses['where'] .= $wpdb->prepare(" AND (tm.term_id IS NULL OR tm.meta_key = %s) ", static::PRIORITY_META_KEY);
            $clauses['orderby'] = str_replace('ORDER BY', 'ORDER BY tm.meta_value DESC, ', $clauses['orderby']);
        }
        return $clauses;
    }

    /**
     * @action {glsr()->taxonomy}_add_form_fields
     */
    public function renderAddFields(): void
    {
        if ($this->termPriorityEnabled()) {
            glsr()->render('views/partials/taxonomy/add-term_priority', [
                'id' => static::PRIORITY_META_KEY,
            ]);
        }
    }

    /**
     * @param \WP_Term $term
     * @action {glsr()->taxonomy}_edit_form_fields
     */
    public function renderEditFields($term): void
    {
        if ($this->termPriorityEnabled()) {
            glsr()->render('views/partials/taxonomy/edit-term_priority', [
                'id' => static::PRIORITY_META_KEY,
                'value' => $this->termPriority($term->term_id),
            ]);
        }
    }

    /**
     * @param string $column
     * @param string $type
     * @param string $taxonomy
     * @action quick_edit_custom_box
     */
    public function renderQuickEditFields($column, $type, $taxonomy): void
    {
        if ('edit-tags' !== $type || $taxonomy !== glsr()->taxonomy || static::PRIORITY_META_KEY !== $column) {
            return;
        }
        if ($this->termPriorityEnabled()) {
            glsr()->render('views/partials/taxonomy/quickedit-term_priority', [
                'id' => static::PRIORITY_META_KEY,
            ]);
        }
    }

    /**
     * @param string[] $metaIds
     * @param int $termId
     * @param string $metaKey
     * @action deleted_term_meta
     */
    public function termPriorityDeleted($metaIds, $termId, $metaKey): void
    {
        $term = get_term((int) $termId, glsr()->taxonomy);
        if (is_a($term, \WP_Term::class) && static::PRIORITY_META_KEY === $metaKey) {
            delete_transient(glsr()->prefix.static::PRIORITY_META_KEY);
        }
    }

    /**
     * @param int $termId
     * @param int $ttId
     * @param array $args
     * @action edit_{glsr()->taxonomy}
     */
    public function termPriorityUpdated($termId, $ttId, $args): void
    {
        if (!$this->termPriorityEnabled()) {
            return;
        }
        $value = Arr::getAs('int', $args, static::PRIORITY_META_KEY);
        if (0 === $value) {
            delete_term_meta($termId, static::PRIORITY_META_KEY);
            // transient deleted with "deleted_term_meta" hook
        } else {
            update_term_meta($termId, static::PRIORITY_META_KEY, $value);
            delete_transient(glsr()->prefix.static::PRIORITY_META_KEY);
        }
    }

    protected function termPriority(int $termId): int
    {
        return Cast::toInt(get_term_meta($termId, static::PRIORITY_META_KEY, true));
    }

    protected function termPriorityEnabled(): bool
    {
        return !glsr()->filterBool('taxonomy/disable_term_priority', false);
    }

    protected function termPriorityExists(): bool
    {
        $result = get_transient(glsr()->prefix.static::PRIORITY_META_KEY);
        if (false === $result) {
            global $wpdb;
            $result = (int) $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM {$wpdb->termmeta} AS tm
                INNER JOIN {$wpdb->term_taxonomy} AS tt ON tm.term_id = tt.term_id
                WHERE tt.taxonomy = %s
                AND meta_key = %s
            ", glsr()->taxonomy, static::PRIORITY_META_KEY));
            set_transient(glsr()->prefix.static::PRIORITY_META_KEY, $result);
        }
        return (int) $result > 0;
    }
}
