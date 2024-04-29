<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class TaxonomyController extends AbstractController
{
    public const PRIORITY_META_KEY = 'term_priority';

    /**
     * @param string[] $columns
     *
     * @return string[]
     *
     * @filter manage_edit-{glsr()->taxonomy}_columns
     */
    public function filterColumns(array $columns): array
    {
        if ($this->termPriorityEnabled()) {
            $columns[static::PRIORITY_META_KEY] = _x('Priority', 'admin-text', 'site-reviews');
            $columns['term_id'] = _x('TID', 'admin-text', 'site-reviews');
            $columns['term_taxonomy_id'] = _x('TTID', 'admin-text', 'site-reviews');
        }
        return $columns;
    }

    /**
     * @filter manage_{glsr()->taxonomy}_custom_column
     */
    public function filterColumnValue(string $value, string $column, int $termId): string
    {
        if ('term_id' === $column) {
            return (string) $termId;
        }
        if ('term_taxonomy_id' === $column) {
            return (string) get_term_by('term_id', $termId, glsr()->taxonomy)->term_taxonomy_id;
        }
        if (static::PRIORITY_META_KEY !== $column || !$this->termPriorityEnabled()) {
            return $value;
        }
        return (string) $this->termPriority($termId);
    }

    /**
     * @param string[] $hidden
     *
     * @return string[]
     *
     * @filter default_hidden_columns
     */
    public function filterDefaultHiddenColumns(array $hidden, \WP_Screen $screen): array
    {
        if ('edit-'.glsr()->taxonomy !== Arr::get($screen, 'id')) {
            return $hidden;
        }
        return array_unique(array_merge($hidden, [
            'term_id',
            'term_taxonomy_id',
        ]));
    }

    /**
     * @param string[] $actions
     *
     * @return string[]
     *
     * @filter {glsr()->taxonomy}_row_actions
     */
    public function filterRowActions(array $actions, \WP_Term $term): array
    {
        $action = ['id' => sprintf('<span>ID: %d</span>', $term->term_id)];
        return array_merge($action, $actions);
    }

    /**
     * @param string[] $clauses
     * @param string[] $taxonomies
     *
     * @return string[]
     *
     * @filter terms_clauses
     */
    public function filterTermsClauses(array $clauses, array $taxonomies, array $args): array
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
            $clauses['join'] .= $wpdb->prepare(" LEFT JOIN {$wpdb->termmeta} AS tm ON (tm.term_id = t.term_id AND tm.meta_key = %s) ", static::PRIORITY_META_KEY);
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
     * @action {glsr()->taxonomy}_edit_form_fields
     */
    public function renderEditFields(\WP_Term $term): void
    {
        if ($this->termPriorityEnabled()) {
            glsr()->render('views/partials/taxonomy/edit-term_priority', [
                'id' => static::PRIORITY_META_KEY,
                'value' => $this->termPriority($term->term_id),
            ]);
        }
    }

    /**
     * @action quick_edit_custom_box
     */
    public function renderQuickEditFields(string $column, string $type, string $taxonomy): void
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
     *
     * @action deleted_term_meta
     */
    public function termPriorityDeleted(array $metaIds, int $termId, string $metaKey): void
    {
        $term = get_term((int) $termId, glsr()->taxonomy);
        if (is_a($term, \WP_Term::class) && static::PRIORITY_META_KEY === $metaKey) {
            delete_transient(glsr()->prefix.static::PRIORITY_META_KEY);
        }
    }

    /**
     * @action edit_{glsr()->taxonomy}
     */
    public function termPriorityUpdated(int $termId, int $ttId, array $args): void
    {
        if (!$this->termPriorityEnabled()) {
            return;
        }
        $value = Arr::getAs('int', $args, static::PRIORITY_META_KEY);
        if (0 === $value) {
            delete_term_meta($termId, static::PRIORITY_META_KEY); // transient deleted with "deleted_term_meta" hook
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
            $sql = "
                SELECT COUNT(*) 
                FROM table|termmeta AS tm
                INNER JOIN table|term_taxonomy AS tt ON (tt.term_id = tm.term_id)
                WHERE tt.taxonomy = %s
                AND meta_key = %s
            ";
            $result = (int) glsr(Database::class)->dbGetVar(
                glsr(Query::class)->sql($sql, glsr()->taxonomy, static::PRIORITY_META_KEY)
            );
            set_transient(glsr()->prefix.static::PRIORITY_META_KEY, $result);
        }
        return (int) $result > 0;
    }
}
