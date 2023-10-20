<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Defaults\ColumnOrderbyDefaults;
use GeminiLabs\SiteReviews\Defaults\ListtableFiltersDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Overrides\ReviewsListTable;

class ListTableController extends Controller
{
    /**
     * @param array $response
     * @param array $data
     * @param string $screenId
     * @return array
     * @filter heartbeat_received
     */
    public function filterCheckLockedReviews($response, $data, $screenId)
    {
        $checked = [];
        if (!is_array(Arr::get($data, 'wp-check-locked-posts'))) {
            return $response;
        }
        foreach ($data['wp-check-locked-posts'] as $key) {
            $postId = absint(substr($key, 5));
            $userId = (int) wp_check_post_lock($postId);
            $user = get_userdata($userId);
            if ($user && !glsr()->can('edit_post', $postId) && glsr()->can('respond_to_post', $postId)) {
                $send = ['text' => sprintf(_x('%s is currently editing', 'admin-text', 'site-reviews'), $user->display_name)];
                if (get_option('show_avatars')) {
                    $send['avatar_src'] = get_avatar_url($user->ID, ['size' => 18]);
                    $send['avatar_src_2x'] = get_avatar_url($user->ID, ['size' => 36]);
                }
                $checked[$key] = $send;
            }
        }
        if (!empty($checked)) {
            $response['wp-check-locked-posts'] = $checked;
        }
        return $response;
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_{glsr()->post_type}_posts_columns
     */
    public function filterColumnsForPostType($columns)
    {
        $columns = Arr::consolidate($columns);
        $postTypeColumns = glsr()->retrieveAs('array', 'columns.'.glsr()->post_type, []);
        foreach ($postTypeColumns as $key => &$value) {
            if (array_key_exists($key, $columns) && empty($value)) {
                $value = $columns[$key];
            }
        }
        return array_filter($postTypeColumns, 'strlen');
    }

    /**
     * @param string $status
     * @param \WP_Post $post
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
     * @param \WP_Screen $screen
     * @return array
     * @filter default_hidden_columns
     */
    public function filterDefaultHiddenColumns($hidden, $screen)
    {
        if (Arr::get($screen, 'id') === 'edit-'.glsr()->post_type) {
            $hiddenColumns = glsr()->retrieveAs('array', 'columns_hidden.'.glsr()->post_type, []);
            return array_unique(array_merge(Arr::consolidate($hidden), $hiddenColumns));
        }
        return $hidden;
    }

    /**
     * @return array
     * @filter posts_clauses
     */
    public function filterPostClauses(array $clauses, \WP_Query $query)
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
     * @param \WP_Post $post
     * @return array
     * @filter post_row_actions
     */
    public function filterRowActions($actions, $post)
    {
        if (glsr()->post_type !== Arr::get($post, 'post_type') || 'trash' === $post->post_status) {
            return $actions;
        }
        unset($actions['inline hide-if-no-js']);
        $newActions = ['id' => sprintf('<span>ID: %d</span>', $post->ID)];
        if (glsr()->can('publish_post', $post->ID)) {
            $rowActions = [
                'approve' => _x('Approve', 'admin-text', 'site-reviews'),
                'unapprove' => _x('Unapprove', 'admin-text', 'site-reviews'),
            ];
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
        }
        if (glsr()->can('respond_to_post', $post->ID)) {
            $newActions['respond hide-if-no-js'] = glsr(Builder::class)->button([
                'aria-expanded' => false,
                'aria-label' => esc_attr(sprintf(_x('Respond inline to &#8220;%s&#8221;', 'admin-text', 'site-reviews'), _draft_or_post_title())),
                'class' => 'button-link editinline',
                'text' => _x('Respond', 'admin-text', 'site-reviews'),
                'type' => 'button',
            ]);
        }
        return $newActions + Arr::consolidate($actions);
    }

    /**
     * @param \WP_Screen $screen
     * @return string
     * @filter screen_settings
     */
    public function filterScreenFilters($settings, $screen)
    {
        if ('edit-'.glsr()->post_type === $screen->id) {
            $userId = get_current_user_id();
            $filters = glsr(ListtableFiltersDefaults::class)->defaults();
            if (count(glsr()->retrieveAs('array', 'review_types')) < 2) {
                unset($filters['type']);
            }
            foreach ($filters as $key => &$value) {
                $value = glsr($value)->title();
            }
            ksort($filters);
            $setting = 'edit_'.glsr()->post_type.'_filters';
            $enabled = get_user_meta($userId, $setting, true);
            if (!is_array($enabled)) {
                $enabled = ['rating']; // the default enabled filters
                update_user_meta($userId, $setting, $enabled);
            }
            $settings .= glsr()->build('partials/screen/filters', [
                'enabled' => $enabled,
                'filters' => $filters,
                'setting' => $setting,
            ]);
        }
        return $settings;
    }

    /**
     * @param string $search
     * @return string
     * @action posts_search
     */
    public function filterSearchQuery($search, \WP_Query $query)
    {
        if (!$this->hasQueryPermission($query)) {
            return $search;
        }
        if (!is_numeric($query->get('s')) || empty($search)) {
            return $search;
        }
        global $wpdb;
        $replace = $wpdb->prepare("{$wpdb->posts}.ID = %d", $query->get('s'));
        return str_replace('AND (((', "AND ((({$replace}) OR (", $search);
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_edit-{glsr()->post_type}_sortable_columns
     */
    public function filterSortableColumns($columns)
    {
        $columns = Arr::consolidate($columns);
        $postTypeColumns = glsr()->retrieveAs('array', 'columns.'.glsr()->post_type, []);
        unset($postTypeColumns['cb']);
        foreach ($postTypeColumns as $key => $value) {
            if (!Str::startsWith($key, 'assigned') && !Str::startsWith($key, 'taxonomy')) {
                $columns[$key] = $key;
            }
        }
        return $columns;
    }

    /**
     * @return void
     * @action wp_ajax_inline_save
     */
    public function overrideInlineSaveAjax()
    {
        $screen = filter_input(INPUT_POST, 'screen');
        if ('edit-'.glsr()->post_type !== $screen) {
            return; // don't override
        }
        global $mode;
        check_ajax_referer('inlineeditnonce', '_inline_edit');
        if (empty($postId = filter_input(INPUT_POST, 'post_ID', FILTER_VALIDATE_INT))) {
            wp_die();
        }
        if (!glsr()->can('respond_to_post', $postId)) {
            wp_die(_x('Sorry, you are not allowed to respond to this review.', 'admin-text', 'site-reviews'));
        }
        if ($last = wp_check_post_lock($postId)) {
            $user = get_userdata($last);
            $username = Arr::get($user, 'display_name', _x('Someone', 'admin-text', 'site-reviews'));
            $message = _x('Saving is disabled: %s is currently editing this review.', 'admin-text', 'site-reviews');
            printf($message, esc_html($username));
            wp_die();
        }
        glsr(ReviewManager::class)->updateResponse($postId, filter_input(INPUT_POST, '_response'));
        glsr()->action('cache/flush', glsr_get_review($postId));
        $mode = Str::restrictTo(['excerpt', 'list'], filter_input(INPUT_POST, 'post_view'), 'list');
        $table = new ReviewsListTable(['screen' => convert_to_screen($screen)]);
        $table->display_rows([get_post($postId)], 0);
        wp_die();
    }

    /**
     * @return void
     * @action load-edit.php
     */
    public function overridePostsListTable()
    {
        if ('edit-'.glsr()->post_type === glsr_current_screen()->id
            && glsr()->can('respond_to_posts')) {
            $table = new ReviewsListTable();
            $table->prepare_items();
            add_filter('views_edit-'.glsr()->post_type, function ($views) use ($table) {
                global $wp_list_table;
                $wp_list_table = clone $table;
                echo glsr(Builder::class)->div(glsr(Notice::class)->get(), [
                    'id' => 'glsr-notices',
                ]);
                return $views;
            });
        }
    }

    /**
     * @param string $postType
     * @return void
     * @action restrict_manage_posts
     */
    public function renderColumnFilters($postType)
    {
        if (glsr()->post_type === $postType) {
            $filters = glsr(ListtableFiltersDefaults::class)->defaults();
            $enabledFilters = Arr::consolidate(
                get_user_meta(get_current_user_id(), 'edit_'.glsr()->post_type.'_filters', true)
            );
            foreach ($filters as $filter) {
                echo Cast::toString(glsr()->runIf($filter, $enabledFilters));
            }
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
        $className = Helper::buildClassName(['ColumnValue', $column], 'Controllers\ListTableColumns');
        $className = glsr()->filterString('column/'.$column, $className);
        $value = glsr()->runIf($className, $review);
        $value = glsr()->filterString('columns/'.$column, $value, $postId);
        echo Helper::ifEmpty($value, '&mdash;');
    }

    /**
     * @return void
     * @action pre_get_posts
     */
    public function setQueryForTable(\WP_Query $query)
    {
        if (!$this->hasQueryPermission($query)) {
            return;
        }
        $orderby = $query->get('orderby');
        if ('response' === $orderby) {
            $query->set('meta_key', Str::prefix($orderby, '_'));
            $query->set('orderby', 'meta_value');
        }
        if ($termId = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT)) {
            $taxQuery = ['taxonomy' => glsr()->taxonomy];
            if (-1 === Cast::toInt($termId)) {
                $taxQuery['operator'] = 'NOT EXISTS';
            } else {
                $taxQuery['terms'] = $termId;
            }
            $query->set('tax_query', [$taxQuery]);
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
        $column = Cast::toString(get_query_var('orderby')); // get_query_var output is unpredictable
        return array_key_exists($column, $columns);
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
    protected function modifyClauseJoin($join, $table, \WP_Query $query)
    {
        global $wpdb;
        $join .= " INNER JOIN {$table} ON {$table}.review_id = {$wpdb->posts}.ID ";
        foreach ($this->filterByValues() as $key => $value) {
            if (!in_array($key, ['assigned_post', 'assigned_user'])) {
                continue;
            }
            $assignedTable = glsr(Query::class)->table($key.'s');
            $value = Cast::toInt($value);
            if (0 === $value) {
                $join .= " LEFT JOIN {$assignedTable} ON {$assignedTable}.rating_id = {$table}.ID ";
            } else {
                $join .= " INNER JOIN {$assignedTable} ON {$assignedTable}.rating_id = {$table}.ID ";
            }
        }
        return $join;
    }

    /**
     * @param string $orderby
     * @return string
     */
    protected function modifyClauseOrderby($orderby, $table, \WP_Query $query)
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
    protected function modifyClauseWhere($where, $table, \WP_Query $query)
    {
        global $wpdb;
        foreach ($this->filterByValues() as $key => $value) {
            if (in_array($key, ['assigned_post', 'assigned_user'])) {
                $assignedTable = glsr(Query::class)->table($key.'s');
                $column = Str::suffix(Str::removePrefix($key, 'assigned_'), '_id');
                $value = Cast::toInt($value);
                if (0 === $value) {
                    $where .= " AND {$assignedTable}.{$column} IS NULL ";
                } else {
                    $where .= " AND {$assignedTable}.{$column} = {$value} ";
                }
            } elseif (in_array($key, ['rating', 'terms', 'type'])) {
                $where .= " AND {$table}.{$key} = '{$value}' ";
            } elseif ('author' === $key && '0' === $value) {
                // Filtering by the "author" URL parameter is automatically done
                // by WordPress when the value is not empty
                $where .= " AND {$wpdb->posts}.post_author IN (0) ";
            }
        }
        return $where;
    }
}
