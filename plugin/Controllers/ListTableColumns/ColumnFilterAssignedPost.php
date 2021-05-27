<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database\Query;

class ColumnFilterAssignedPost extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('assigned_post', $enabledFilters)) {
            $this->enabled = true;
        }
        if ($options = $this->options()) {
            $label = $this->label('assigned_post',
                _x('Filter by assigned post', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('assigned_post', $options,
                _x('All assigned posts', 'admin-text', 'site-reviews')
            );
            return $label.$filter;
        }
    }

    /**
     * @return array
     */
    protected function options()
    {
        global $wpdb;
        $table = glsr(Query::class)->table('assigned_posts');
        $postIds = $wpdb->get_col("SELECT DISTINCT post_id FROM {$table}");
        if (empty($postIds)) {
            return [];
        }
        $posts = get_posts([
            'order' => 'ASC',
            'orderby' => 'post_title',
            'post_type' => 'any',
            'posts_per_page' => -1,
            'post__in' => $postIds,
        ]);
        return wp_list_pluck($posts, 'post_title', 'ID');
    }

    /**
     * @param string $id
     * @return int|string
     */
    protected function value($id)
    {
        return filter_input(INPUT_GET, $id, FILTER_SANITIZE_NUMBER_INT);
    }
}
