<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\ListTableController\Columns;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Post;
use WP_Query;
use WP_Screen;

class ListTableController extends Controller
{
    /**
     * @return void
     * @action admin_action_approve
     */
    public function approve()
    {
        if (Application::ID != filter_input(INPUT_GET, 'plugin')) {
            return;
        }
        check_admin_referer('approve-review_'.($postId = $this->getPostId()));
        wp_update_post([
            'ID' => $postId,
            'post_status' => 'publish',
        ]);
        wp_safe_redirect(wp_get_referer());
        exit;
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_.Application::POST_TYPE._posts_columns
     */
    public function filterColumnsForPostType($columns)
    {
        $columns = Arr::consolidateArray($columns);
        $postTypeColumns = glsr()->postTypeColumns[Application::POST_TYPE];
        foreach ($postTypeColumns as $key => &$value) {
            if (!array_key_exists($key, $columns) || !empty($value)) {
                continue;
            }
            $value = $columns[$key];
        }
        if (count(glsr(Database::class)->getReviewsMeta('review_type')) < 2) {
            unset($postTypeColumns['review_type']);
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
        if (Application::POST_TYPE == Arr::get($post, 'post_type')) {
            $status = __('Submitted', 'site-reviews');
        }
        return $status;
    }

    /**
     * @param array $hidden
     * @param WP_Screen $post
     * @return array
     * @filter default_hidden_columns
     */
    public function filterDefaultHiddenColumns($hidden, $screen)
    {
        if (Arr::get($screen, 'id') == 'edit-'.Application::POST_TYPE) {
            $hidden = Arr::consolidateArray($hidden);
            $hidden = array_unique(array_merge($hidden, [
                'email', 'ip_address', 'response', 'reviewer',
            ]));
        }
        return $hidden;
    }

    /**
     * @param array $actions
     * @param WP_Post $post
     * @return array
     * @filter post_row_actions
     */
    public function filterRowActions($actions, $post)
    {
        if (Application::POST_TYPE != Arr::get($post, 'post_type')
            || 'trash' == $post->post_status
            || !user_can(get_current_user_id(), 'edit_post', $post->ID)) {
            return $actions;
        }
        unset($actions['inline hide-if-no-js']); //Remove Quick-edit
        $rowActions = [
            'approve' => esc_attr__('Approve', 'site-reviews'),
            'unapprove' => esc_attr__('Unapprove', 'site-reviews'),
        ];
        $newActions = [];
        foreach ($rowActions as $key => $text) {
            $newActions[$key] = glsr(Builder::class)->a($text, [
                'aria-label' => sprintf(esc_attr_x('%s this review', 'Approve the review', 'site-reviews'), $text),
                'class' => 'glsr-change-status',
                'href' => wp_nonce_url(
                    admin_url('post.php?post='.$post->ID.'&action='.$key.'&plugin='.Application::ID),
                    $key.'-review_'.$post->ID
                ),
            ]);
        }
        return $newActions + Arr::consolidateArray($actions);
    }

    /**
     * @param array $columns
     * @return array
     * @filter manage_edit-.Application::POST_TYPE._sortable_columns
     */
    public function filterSortableColumns($columns)
    {
        $columns = Arr::consolidateArray($columns);
        $postTypeColumns = glsr()->postTypeColumns[Application::POST_TYPE];
        unset($postTypeColumns['cb']);
        foreach ($postTypeColumns as $key => $value) {
            if (Str::startsWith('taxonomy', $key)) {
                continue;
            }
            $columns[$key] = $key;
        }
        return $columns;
    }

    /**
     * @param string $columnName
     * @param string $postType
     * @return void
     * @action bulk_edit_custom_box
     */
    public function renderBulkEditFields($columnName, $postType)
    {
        if ('assigned_to' == $columnName && Application::POST_TYPE == $postType) {
            glsr()->render('partials/editor/bulk-edit-assigned-to');
        }
    }

    /**
     * @param string $postType
     * @return void
     * @action restrict_manage_posts
     */
    public function renderColumnFilters($postType)
    {
        glsr(Columns::class)->renderFilters($postType);
    }

    /**
     * @param string $column
     * @param string $postId
     * @return void
     * @action manage_posts_custom_column
     */
    public function renderColumnValues($column, $postId)
    {
        glsr(Columns::class)->renderValues($column, $postId);
    }

    /**
     * @param int $postId
     * @return void
     * @action save_post_.Application::POST_TYPE
     */
    public function saveBulkEditFields($postId)
    {
        if (!glsr()->can('edit_posts')) {
            return;
        }
        $assignedTo = filter_input(INPUT_GET, 'assigned_to');
        if ($assignedTo && get_post($assignedTo)) {
            glsr(Database::class)->update($postId, 'assigned_to', $assignedTo);
        }
    }

    /**
     * @return void
     * @action pre_get_posts
     */
    public function setQueryForColumn(WP_Query $query)
    {
        if (!$this->hasPermission($query)) {
            return;
        }
        $this->setMetaQuery($query, [
            'rating', 'review_type',
        ]);
        $this->setOrderby($query);
    }

    /**
     * @return void
     * @action admin_action_unapprove
     */
    public function unapprove()
    {
        if (Application::ID != filter_input(INPUT_GET, 'plugin')) {
            return;
        }
        check_admin_referer('unapprove-review_'.($postId = $this->getPostId()));
        wp_update_post([
            'ID' => $postId,
            'post_status' => 'pending',
        ]);
        wp_safe_redirect(wp_get_referer());
        exit;
    }

    /**
     * Check if the translation string can be modified.
     * @param string $domain
     * @return bool
     */
    protected function canModifyTranslation($domain = 'default')
    {
        $screen = glsr_current_screen();
        return 'default' == $domain
            && 'edit' == $screen->base
            && Application::POST_TYPE == $screen->post_type;
    }

    /**
     * @return bool
     */
    protected function hasPermission(WP_Query $query)
    {
        global $pagenow;
        return is_admin()
            && $query->is_main_query()
            && Application::POST_TYPE == $query->get('post_type')
            && 'edit.php' == $pagenow;
    }

    /**
     * @return void
     */
    protected function setMetaQuery(WP_Query $query, array $metaKeys)
    {
        foreach ($metaKeys as $key) {
            $value = (string) filter_input(INPUT_GET, $key);
            if ('' === $value) {
                continue;
            }
            $metaQuery = (array) $query->get('meta_query');
            $metaQuery[] = [
                'key' => Str::prefix('_', $key, '_'),
                'value' => $value,
            ];
            $query->set('meta_query', array_filter($metaQuery));
        }
    }

    /**
     * @return void
     */
    protected function setOrderby(WP_Query $query)
    {
        $orderby = $query->get('orderby');
        $columns = glsr()->postTypeColumns[Application::POST_TYPE];
        unset($columns['cb'], $columns['title'], $columns['date']);
        if (in_array($orderby, array_keys($columns))) {
            if ('reviewer' == $orderby) {
                $orderby = 'author';
            }
            $query->set('meta_key', Str::prefix('_', $orderby, '_'));
            $query->set('orderby', 'meta_value');
        }
    }
}
