<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\MainController;

class MainHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(MainController::class, [
            ['filterDevmode', 'site-reviews/devmode', 1],
            ['filterDropTables', 'wpmu_drop_tables', 999], // run last
            ['initDefaults', 'init', 2], // run after updateAddons!
            ['installOnNewSite', 'wp_insert_site'],
            ['logOnce', 'admin_footer'],
            ['logOnce', 'wp_footer'],
            ['registerAddons', 'plugins_loaded'],
            ['registerLanguages', 'init', -10], // do this first
            ['registerPostMeta', 'init'],
            ['registerPostType', 'init', 8],
            ['registerReviewTypes', 'init', 7],
            ['registerShortcodes', 'init'],
            ['registerTaxonomy', 'init'],
            ['registerWidgets', 'widgets_init'],
            ['updateAddons', 'init', 1],
        ]);
    }
}
