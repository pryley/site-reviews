<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\MainController;

class MainHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(MainController::class, [
            ['filterDropTables', 'wpmu_drop_tables', 999], // run last
            ['installOnNewSite', 'wp_initialize_site', 999], // run last
            ['logOnce', 'admin_footer'],
            ['logOnce', 'wp_footer'],
            ['registerAddons', 'plugins_loaded'],
            ['registerLanguages', 'after_setup_theme'],
            ['registerPostMeta', 'init'],
            ['registerPostType', 'init'],
            ['registerReviewTypes', 'init'],
            ['registerShortcodes', 'init'],
            ['registerTaxonomy', 'init'],
            ['registerWidgets', 'widgets_init'],
            ['updateAddons', 'wp_loaded'],
        ]);
    }
}
