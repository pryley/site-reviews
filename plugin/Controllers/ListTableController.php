<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterRating;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterType;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Defaults\ColumnOrderbyDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use WP_Post;
use WP_Query;
use WP_Screen;

class ListTableController extends Controller
{
    /**
     * @param array $columns
     * @return array
     * @filter manage_{glsr()->post_type}_posts_columns
     */
    public function filterColumnsForPostType($columns)
    {
        $columns = Arr::consolidate($columns);
        $postTypeColumns = glsr()->retrieve('columns.'.glsr()->post_type, []);
        foreach ($postTypeColumns as $key => &$value) {
            if (array_key_exists($key, $columns) && empty($value)) {
                $value = $columns[$key];
            }
        }
        return array_filter($postTypeColumns, 'strlen');
    }

    /**
     * @param string $status
     * @param WP_Post $post
     * @return string
     * @filter post_date_column_status
     */
    public function filterDateColumnStatus($status, $post)
    {
        $isReview = glsr()->post_type === Arr::get($post, 'post_type');
        return Helper::ifTrue(!$isReview, $status, _x('Submitted', 'admin-text', 'site-reviews'));
    }

    /**
     * @param array $hidden
     * @param WP_Screen $screen
     * @return array
     * @filter default_hidden_columns
     */
    public function filterDefaultHiddenColumns($hidden, $screen)
    {
        if (Arr::get($screen, 'id') === 'edit-'.glsr()->post_type) {
            $hidden = Arr::consolidate($hidden);
            $hidden = array_unique(array_merge($hidden, [
                'assigned_users', 'author_name', 'author_email', 'ip_address', 'response',
            ]));
        }
        return $hidden;
    }

    /**
     * @return array
     * @filter posts_clauses
     */
    public function filterPostClauses(array $clauses, WP_Query $query)
    {
        if (!$this->hasQueryPermission($query) || (!$this->isListFiltered() && !$this->isListOrdered())) {
            return $clauses;
        }
        $table = glsr(Query::class)->table('ratings');
        foreach ($clauses as $key => &$clause) {
            $method = Helper::buildMethodName($key, 'modifyClause');
            if (method_exists($this, $method)) {
                $clause = call_user_func([$this, $method], $clause, $table, $query);
            }
        }
        return glsr()->filterArray('review-table/clauses', $clauses, $table, $query);
    }

    /**
     * @param array $actions
     * @param WP_Post $post
     * @return array
     * @filter post_row_actions
     */
    public function filterRowActions($actions, $post)
    {
        if (glsr()->post_type !== Arr::get($post, 'post_type')
            || 'trash' == $post->post_status
            || !user_can(get_current_user_id(), 'edit_post', $post->ID)) {
            return $actions;
        }
        unset($actions['inline hide-if-no-js']); //Remove Quick-edit
        $rowActions = [
            'approve' => _x('Approve', 'admin-text', 'site-reviews'),
            'unapprove' => _x('Unapprove', 'admin-text', 'site-reviews'),
        ];
        $newActions = ['id' => sprintf(_x('<span>ID: %d</span>', 'The Review Post ID (admin-text)', 'site-reviews'), $post->ID)];
        foreach ($rowActions as $key => $text) {
            $newActions[$key] = glsr(Builder::class)->a($text, [
                'aria-label' => esc_attr(sprintf(_x('%s this review', 'Approve the review (admin-text)', 'site-reviews'), $text)),
                'class' => 'glsr-toggle-status',
                'href' => wp_nonce_url(
                    admin_url('post.php?post='.$post->ID.'&action='.$key.'&plugin='.glsr()->id),
                    $key.'-review_'.$post->ID
                ),
            ]);
        }
        return $newActions + Arr::consolidate($actions);
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_edit-{glsr()->post_type}_sortable_columns
     */
    public function filterSortableColumns($columns)
    {
        $columns = Arr::consolidate($columns);
        $postTypeColumns = glsr()->retrieve('columns.'.glsr()->post_type, []);
        unset($postTypeColumns['cb']);
        foreach ($postTypeColumns as $key => $value) {
            if (!Str::startsWith('assigned', $key) && !Str::startsWith('taxonomy', $key)) {
                $columns[$key] = $key;
            }
        }
        return $columns;
    }

    /**
     * @param string $postType
     * @return void
     * @action restrict_manage_posts
     */
    public function renderColumnFilters($postType)
    {
        if (glsr()->post_type === $postType) {
            echo Cast::toString(glsr()->runIf(ColumnFilterRating::class));
            echo Cast::toString(glsr()->runIf(ColumnFilterType::class));
            echo Cast::toString(glsr()->runIf(glsr()->filterString('review-table/filter', '')));
        }
    }

    /**
     * @param string $column
     * @param int $postId
     * @return void
     * @action manage_{glsr()->post_type}_posts_custom_column
     */
    public function renderColumnValues($column, $postId)
    {
        $review = glsr(Query::class)->review($postId);
        if (!$review->isValid()) {
            glsr(Migrate::class)->reset(); // looks like a migration is needed!
            return;
        }
        $className = Helper::buildClassName('column-value-'.$column, 'Controllers\ListTableColumns');
        $className = glsr()->filterString('column/'.$column, $className);
        $value = glsr()->runIf($className, $review);
        $value = glsr()->filterString('columns/'.$column, $value, $postId);
        echo Helper::ifEmpty($value, '&mdash;');
    }

    /**
     * @return void
     * @action pre_get_posts
     */
    public function setQueryForColumn(WP_Query $query)
    {
        if (!$this->hasQueryPermission($query)) {
            return;
        }
        $orderby = $query->get('orderby');
        if ('response' === $orderby) {
            $query->set('meta_key', Str::prefix($orderby, '_'));
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * @return array
     * */
    protected function filterByValues()
    {
        $filterBy = glsr(ColumnFilterbyDefaults::class)->defaults();
        $filterBy = filter_input_array(INPUT_GET, $filterBy);
        return Arr::removeEmptyValues(Arr::consolidate($filterBy));
    }

    /**
     * @return bool
     */
    protected function isListFiltered()
    {
        return !empty($this->filterByValues());
    }

    /**
     * @return bool
     */
    protected function isListOrdered()
    {
        $columns = glsr(ColumnOrderbyDefaults::class)->defaults();
        return array_key_exists(get_query_var('orderby'), $columns);
    }

    /**
     * @return bool
     */
    protected function isOrderbyWithIsNull($column)
    {
        $columns = [
            'email', 'name', 'ip_address', 'type',
        ];
        $columns = glsr()->filterArray('columns/orderby-is-null', $columns);
        return in_array($column, $columns);
    }

    /**
     * @param string $join
     * @return string
     */
    protected function modifyClauseJoin($join, $table, WP_Query $query)
    {
        global $wpdb;
        $join .= " INNER JOIN {$table} ON {$table}.review_id = {$wpdb->posts}.ID ";
        return $join;
    }

    /**
     * @param string $orderby
     * @return string
     */
    protected function modifyClauseOrderby($orderby, $table, WP_Query $query)
    {
        $columns = glsr(ColumnOrderbyDefaults::class)->defaults();
        if ($column = Arr::get($columns, $query->get('orderby'))) {
            $order = $query->get('order');
            $orderby = "{$table}.{$column} {$order}";
            if ($this->isOrderbyWithIsNull($column)) {
                $orderby = "NULLIF({$table}.{$column}, '') IS NULL, {$orderby}";
            }
        }
        return $orderby;
    }

    /**
     * @param string $where
     * @return string
     */
    protected function modifyClauseWhere($where, $table, WP_Query $query)
    {
        foreach ($this->filterByValues() as $key => $value) {
            $where .= " AND {$table}.{$key} = '{$value}' ";
        }
        return $where;
    }
}
