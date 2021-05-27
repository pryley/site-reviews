<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database\Query;

class ColumnFilterAssignedUser extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('assigned_user', $enabledFilters)) {
            $this->enabled = true;
        }
        if ($options = $this->options()) {
            $label = $this->label('assigned_user',
                _x('Filter by assigned user', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('assigned_user', $options,
                _x('All assigned users', 'admin-text', 'site-reviews')
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
        $table = glsr(Query::class)->table('assigned_users');
        $userIds = $wpdb->get_col("SELECT DISTINCT user_id FROM {$table}");
        if (empty($userIds)) {
            return [];
        }
        $users = get_users([
            'fields' => ['ID', 'display_name'],
            'include' => $userIds,
            'orderby' => 'display_name',
        ]);
        return wp_list_pluck($users, 'display_name', 'ID');
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
