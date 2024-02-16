<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\ListTableController;

class ListTableHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(ListTableController::class, [
            ['filterCheckLockedReviews', 'heartbeat_received', 20, 2],
            ['filterColumnsForPostType', "manage_{$this->type}_posts_columns"],
            ['filterDateColumnStatus', 'post_date_column_status', 10, 2],
            ['filterDefaultHiddenColumns', 'default_hidden_columns', 10, 2],
            ['filterListTableClass', 'wp_list_table_class_name'],
            ['filterPostClauses', 'posts_clauses', 10, 2],
            ['filterRowActions', 'post_row_actions', 10, 2],
            ['filterScreenFilters', 'screen_settings', 20, 2],
            ['filterSearchQuery', 'posts_search', 10, 2],
            ['filterSortableColumns', "manage_edit-{$this->type}_sortable_columns"],
            ['overrideInlineSaveAjax', 'wp_ajax_inline-save', 0],
            ['renderColumnFilters', 'restrict_manage_posts'],
            ['renderColumnValues', "manage_{$this->type}_posts_custom_column", 10, 2],
            ['setQueryForTable', 'pre_get_posts'],
        ]);
    }
}
