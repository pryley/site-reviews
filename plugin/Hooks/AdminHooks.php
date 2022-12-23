<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\AdminController;

class AdminHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(AdminController::class, [
            ['enqueueAssets', 'admin_enqueue_scripts'],
            ['filterActionLinks', 'plugin_action_links_'.$this->basename],
            ['filterCapabilities', 'map_meta_cap', 10, 4],
            ['filterDashboardGlanceItems', 'dashboard_glance_items'],
            ['filterExportArgs', 'export_args', 11],
            ['filterScreenOptionsButton', 'screen_options_show_submit', 20],
            ['filterTinymcePlugins', 'mce_external_plugins', 15],
            ['onActivation', 'admin_init'],
            ['onImportEnd', 'import_end'],
            ['printInlineStyle', 'admin_head'],
            ['registerTinymcePopups', 'admin_init'],
            ['renderPageHeader', 'in_admin_header'],
            ['renderTinymceButton', 'media_buttons', 11],
            ['scheduleMigration', 'admin_init'],
            ['searchAssignedPostsAjax', 'site-reviews/route/ajax/filter-assigned_post'],
            ['searchAssignedUsersAjax', 'site-reviews/route/ajax/filter-assigned_user'],
            ['searchAuthorsAjax', 'site-reviews/route/ajax/filter-author'],
            ['searchPostsAjax', 'site-reviews/route/ajax/search-posts'],
            ['searchStringsAjax', 'site-reviews/route/ajax/search-strings'],
            ['searchUsersAjax', 'site-reviews/route/ajax/search-users'],
            ['toggleFiltersAjax', 'site-reviews/route/ajax/toggle-filters'],
            ['togglePinnedAjax', 'site-reviews/route/ajax/toggle-pinned'],
            ['toggleStatusAjax', 'site-reviews/route/ajax/toggle-status'],
            ['toggleVerifiedAjax', 'site-reviews/route/ajax/toggle-verified'],
        ]);
    }
}
