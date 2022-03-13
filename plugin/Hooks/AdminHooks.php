<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\AdminController;

class AdminHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(AdminController::class, [
            ['enqueueAssets', 'admin_enqueue_scripts'],
            ['filterActionLinks', "plugin_action_links_{$this->basename}"],
            ['filterCapabilities', 'map_meta_cap', 10, 4],
            ['filterDashboardGlanceItems', 'dashboard_glance_items'],
            ['filterExportArgs', 'export_args', 11],
            ['filterTinymcePlugins', 'mce_external_plugins', 15],
            ['filterUpdatePluginsTransient', 'site_transient_update_plugins'],
            ['onActivation', 'admin_init'],
            ['onImportEnd', 'import_end'],
            ['printInlineStyle', 'admin_head'],
            ['registerTinymcePopups', 'admin_init'],
            ['renderTinymceButton', 'media_buttons', 11],
            ['rollbackPlugin', "update-custom_rollback-{$this->id}"],
            ['rollbackPluginAjax', "site-reviews/route/ajax/rollback-{$this->id}"],
            ['rollbackPluginReactivate', "update-custom_reactivate-{$this->id}"],
            ['scheduleMigration', 'admin_init'],
            ['searchAssignedPostsAjax', 'site-reviews/route/ajax/filter-assigned_post'],
            ['searchAssignedUsersAjax', 'site-reviews/route/ajax/filter-assigned_user'],
            ['searchAuthorsAjax', 'site-reviews/route/ajax/filter-author'],
            ['searchPostsAjax', 'site-reviews/route/ajax/search-posts'],
            ['searchTranslationsAjax', 'site-reviews/route/ajax/search-translations'],
            ['searchUsersAjax', 'site-reviews/route/ajax/search-users'],
            ['toggleFiltersAjax', 'site-reviews/route/ajax/toggle-filters'],
            ['togglePinnedAjax', 'site-reviews/route/ajax/toggle-pinned'],
            ['toggleStatusAjax', 'site-reviews/route/ajax/toggle-status'],
        ]);
    }
}
