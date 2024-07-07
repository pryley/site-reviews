<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Defaults\ColumnOrderbyDefaults;
use GeminiLabs\SiteReviews\Defaults\ListtableFiltersDefaults;
use GeminiLabs\SiteReviews\Defaults\SqlClauseDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Overrides\ReviewsListTable;

class ListTableController extends AbstractController
{
    /**
     * @filter heartbeat_received
     */
    public function filterCheckLockedReviews(array $response, array $data): array
    {
        $checked = [];
        $postIds = Arr::consolidate(Arr::get($data, 'wp-check-locked-posts'));
        foreach ($postIds as $key) {
            $postId = absint(substr($key, 5));
            $userId = (int) wp_check_post_lock($postId);
            $user = get_userdata($userId);
            if (!$user) {
                continue;
            }
            $name = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
            if (!glsr()->can('edit_post', $postId) && glsr()->can('respond_to_post', $postId)) {
                $send = [
                    'text' => sprintf(_x('%s is currently editing', 'admin-text', 'site-reviews'), $name),
                ];
                if (get_option('show_avatars')) {
                    $send['avatar_src'] = get_avatar_url($user->ID, ['size' => 18]);
                    $send['avatar_src_2x'] = get_avatar_url($user->ID, ['size' => 36]);
                }
                $checked[$key] = $send;
            }
            if (glsr()->can('edit_post', $postId)) {
                continue;
            }
            if (!glsr()->can('respond_to_post', $postId)) {
                continue;
            }
            $send = ['text' => sprintf(_x('%s is currently editing', 'admin-text', 'site-reviews'), $name)];
            if (get_option('show_avatars')) {
                $send['avatar_src'] = get_avatar_url($user->ID, ['size' => 18]);
                $send['avatar_src_2x'] = get_avatar_url($user->ID, ['size' => 36]);
            }
            $checked[$key] = $send;
        }
        if (!empty($checked)) {
            $response['wp-check-locked-posts'] = $checked;
        }
        return $response;
    }

    /**
     * @param string[] $columns
     *
     * @filter manage_{glsr()->post_type}_posts_columns
     */
    public function filterColumnsForPostType(array $columns): array
    {
        $postTypeColumns = glsr()->retrieveAs('array', 'columns.'.glsr()->post_type, []);
        foreach ($postTypeColumns as $key => &$value) {
            if (array_key_exists($key, $columns) && empty($value)) {
                $value = $columns[$key];
            }
        }
        return array_filter($postTypeColumns, 'strlen'); // @phpstan-ignore-line
    }

    /**
     * @filter post_date_column_status
     */
    public function filterDateColumnStatus(string $status, \WP_Post $post): string
    {
        if (glsr()->post_type === $post->post_type) {
            return _x('Submitted', 'admin-text', 'site-reviews');
        }
        return $status;
    }

    /**
     * @param string[] $hidden
     *
     * @filter default_hidden_columns
     */
    public function filterDefaultHiddenColumns(array $hidden, \WP_Screen $screen): array
    {
        if ('edit-'.glsr()->post_type === $screen->id) {
            $hiddenColumns = glsr()->retrieveAs('array', 'columns_hidden.'.glsr()->post_type, []);
            return array_unique(array_merge($hidden, $hiddenColumns));
        }
        return $hidden;
    }

    /**
     * @filter wp_list_table_class_name
     */
    public function filterListTableClass(string $className): string
    {
        $screen = glsr_current_screen();
        if (glsr()->post_type !== $screen->post_type) {
            return $className;
        }
        if ('edit' !== $screen->base) {
            return $className;
        }
        return ReviewsListTable::class;
    }

    /**
     * @filter posts_clauses
     */
    public function filterPostClauses(array $postClauses, \WP_Query $query): array
    {
        if (!$this->hasQueryPermission($query)) {
            return $postClauses;
        }
        $clauses = array_fill_keys(array_keys($postClauses), []);
        if ($this->isListFiltered() || $this->isListOrdered()) {
            foreach ($postClauses as $key => $clause) {
                $method = Helper::buildMethodName('modifyClause', $key);
                if (method_exists($this, $method)) {
                    $clauses[$key] = call_user_func([$this, $method], $clause, $query);
                }
            }
        }
        $clauses = glsr()->filterArray('review-table/clauses', $clauses, $postClauses, $query);
        foreach ($clauses as $key => $clause) {
            $clause = glsr(SqlClauseDefaults::class)->restrict($clause);
            if (empty($clause['clauses'])) {
                continue;
            }
            $values = array_values(array_unique($clause['clauses']));
            $values = implode(' ', $values);
            if (!$clause['replace']) {
                $values = trim($postClauses[$key])." {$values}";
            }
            $postClauses[$key] = " {$values} ";
        }
        return $postClauses;
    }

    /**
     * @param string[] $actions
     *
     * @filter post_row_actions
     */
    public function filterRowActions(array $actions, \WP_Post $post): array
    {
        if (glsr()->post_type !== Arr::get($post, 'post_type') || 'trash' === $post->post_status) {
            return $actions;
        }
        unset($actions['inline hide-if-no-js']);
        $baseurl = admin_url("post.php?post={$post->ID}&plugin=".glsr()->id);
        $newActions = ['id' => sprintf('<span>ID: %d</span>', $post->ID)];
        if (glsr()->can('publish_post', $post->ID)) {
            $newActions['approve'] = glsr(Builder::class)->a([
                'aria-label' => esc_attr(_x('Approve this review', 'admin-text', 'site-reviews')),
                'class' => 'glsr-toggle-status',
                'href' => wp_nonce_url(add_query_arg('action', 'approve', $baseurl), "approve-review_{$post->ID}"),
                'text' => _x('Approve', 'admin-text', 'site-reviews'),
            ]);
            $newActions['unapprove'] = glsr(Builder::class)->a([
                'aria-label' => esc_attr(_x('Unapprove this review', 'admin-text', 'site-reviews')),
                'class' => 'glsr-toggle-status',
                'href' => wp_nonce_url(add_query_arg('action', 'unapprove', $baseurl), "unapprove-review_{$post->ID}"),
                'text' => _x('Unapprove', 'admin-text', 'site-reviews'),
            ]);
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
        return $newActions + $actions;
    }

    /**
     * @filter screen_settings
     */
    public function filterScreenFilters(?string $settings, \WP_Screen $screen): string
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
        return (string) $settings;
    }

    /**
     * @action posts_search
     */
    public function filterSearchQuery(string $search, \WP_Query $query): string
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
     * @filter manage_edit-{glsr()->post_type}_sortable_columns
     */
    public function filterSortableColumns(array $columns): array
    {
        $postTypeColumns = glsr()->retrieveAs('array', 'columns.'.glsr()->post_type, []);
        unset($postTypeColumns['cb']);
        foreach ($postTypeColumns as $key => $value) {
            if (!Str::startsWith($key, ['assigned', 'taxonomy'])) {
                $columns[$key] = $key;
            }
        }
        return $columns;
    }

    /**
     * @action wp_ajax_inline_save
     */
    public function overrideInlineSaveAjax(): void
    {
        $screen = filter_input(INPUT_POST, 'screen');
        if ('edit-'.glsr()->post_type !== $screen) {
            return; // don't override
        }
        check_ajax_referer('inlineeditnonce', '_inline_edit');
        if (empty($postId = filter_input(INPUT_POST, 'post_ID', FILTER_VALIDATE_INT))) {
            wp_die();
        }
        if (!glsr()->can('respond_to_post', $postId)) {
            wp_die(_x('Sorry, you are not allowed to respond to this review.', 'admin-text', 'site-reviews'));
        }
        if ($last = wp_check_post_lock($postId)) {
            $name = _x('Someone', 'admin-text', 'site-reviews');
            $user = get_userdata($last);
            if ($user) {
                $name = glsr(Sanitizer::class)->sanitizeUserName(
                    $user->display_name,
                    $user->user_nicename
                );
            }
            $message = esc_html_x('Saving is disabled: %s is currently editing this review.', 'admin-text', 'site-reviews');
            printf($message, $name);
            wp_die();
        }
        $response = (string) filter_input(INPUT_POST, '_response');
        glsr(ReviewManager::class)->updateResponse($postId, compact('response'));
        glsr()->action('cache/flush', glsr_get_review($postId));
        global $mode;
        $mode = Str::restrictTo(['excerpt', 'list'], (string) filter_input(INPUT_POST, 'post_view'), 'list');
        $table = new ReviewsListTable(['screen' => convert_to_screen($screen)]);
        $table->display_rows([get_post($postId)], 0);
        wp_die();
    }

    /**
     * @action restrict_manage_posts
     */
    public function renderColumnFilters(string $postType): void
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
     * @action manage_{glsr()->post_type}_posts_custom_column
     */
    public function renderColumnValues(string $column, int $postId): void
    {
        $review = glsr(ReviewManager::class)->get((int) $postId);
        if (!$review->isValid()) {
            glsr(Migrate::class)->reset(); // looks like a migration is needed!
            return;
        }
        $className = Helper::buildClassName(['ColumnValue', $column], 'Controllers\ListTableColumns');
        $className = glsr()->filterString("column/{$column}", $className);
        $value = glsr()->runIf($className, $review);
        $value = glsr()->filterString("columns/{$column}", $value, $postId);
        echo Helper::ifEmpty($value, '&mdash;');
    }

    /**
     * @action pre_get_posts
     */
    public function setQueryForTable(\WP_Query $query): void
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

    protected function filterByValues(): array
    {
        $filterBy = glsr(ColumnFilterbyDefaults::class)->defaults();
        $filterBy = filter_input_array(INPUT_GET, $filterBy);
        return Arr::removeEmptyValues(Arr::consolidate($filterBy));
    }

    protected function isListFiltered(): bool
    {
        return !empty($this->filterByValues());
    }

    protected function isListOrdered(): bool
    {
        $columns = glsr(ColumnOrderbyDefaults::class)->defaults();
        $column = Cast::toString(get_query_var('orderby')); // get_query_var output is unpredictable
        return array_key_exists($column, $columns);
    }

    protected function isOrderbyWithIsNull(string $column): bool
    {
        $columns = [
            'email', 'name', 'ip_address', 'type',
        ];
        $columns = glsr()->filterArray('columns/orderby-is-null', $columns);
        return in_array($column, $columns);
    }

    protected function modifyClauseJoin(string $join, \WP_Query $query): array
    {
        $clause = glsr(SqlClauseDefaults::class)->restrict([
            'clauses' => [],
            'replace' => false,
        ]);
        $posts = glsr(Tables::class)->table('posts');
        $ratings = glsr(Tables::class)->table('ratings');
        $clause['clauses'][] = "INNER JOIN {$ratings} ON ({$ratings}.review_id = {$posts}.ID)";
        foreach ($this->filterByValues() as $key => $value) {
            if (!in_array($key, ['assigned_post', 'assigned_user'])) {
                continue;
            }
            $assignedTable = glsr(Tables::class)->table($key.'s');
            $value = Cast::toInt($value);
            if (0 === $value) {
                $clause['clauses'][] = "LEFT JOIN {$assignedTable} ON ({$assignedTable}.rating_id = {$ratings}.ID)";
            } else {
                $clause['clauses'][] = "INNER JOIN {$assignedTable} ON ({$assignedTable}.rating_id = {$ratings}.ID)";
            }
        }
        return $clause;
    }

    protected function modifyClauseOrderby(string $orderby, \WP_Query $query): array
    {
        $clause = glsr(SqlClauseDefaults::class)->restrict([
            'clauses' => [],
            'replace' => true,
        ]);
        $columns = glsr(ColumnOrderbyDefaults::class)->defaults();
        $column = Arr::get($columns, $query->get('orderby'));
        if (empty($column)) {
            return $clause;
        }
        $ratings = glsr(Tables::class)->table('ratings');
        if ($this->isOrderbyWithIsNull($column)) {
            $clause['clauses'][] = "NULLIF({$ratings}.{$column}, '') IS NULL, {$orderby}";
        } else {
            $clause['clauses'][] = "{$ratings}.{$column} {$query->get('order')}";
        }
        return $clause;
    }

    protected function modifyClauseWhere(string $where, \WP_Query $query): array
    {
        $clause = glsr(SqlClauseDefaults::class)->restrict([
            'clauses' => [],
            'replace' => false,
        ]);
        $ratings = glsr(Tables::class)->table('ratings');
        $posts = glsr(Tables::class)->table('posts');
        foreach ($this->filterByValues() as $key => $value) {
            if (in_array($key, ['assigned_post', 'assigned_user'])) {
                $assignedTable = glsr(Tables::class)->table($key.'s');
                $column = Str::suffix(Str::removePrefix($key, 'assigned_'), '_id');
                $value = Cast::toInt($value);
                if (0 === $value) {
                    $clause['clauses'][] = "AND {$assignedTable}.{$column} IS NULL";
                } else {
                    $clause['clauses'][] = "AND {$assignedTable}.{$column} = {$value}";
                }
            } elseif (in_array($key, ['rating', 'terms', 'type'])) {
                $clause['clauses'][] = "AND {$ratings}.{$key} = '{$value}'";
            } elseif ('author' === $key && '0' === $value) {
                // Filtering by the "author" URL parameter is automatically done
                // by WordPress when the value is not empty
                $clause['clauses'][] = "AND {$posts}.post_author IN (0)";
            }
        }
        return $clause;
    }
}
