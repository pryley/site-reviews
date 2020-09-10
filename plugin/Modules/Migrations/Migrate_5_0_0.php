<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Migrations\Migrate_5_0_0\MigrateReviews;
use GeminiLabs\SiteReviews\Modules\Migrations\Migrate_5_0_0\MigrateSidebars;

class Migrate_5_0_0
{
    /**
     * @return void
     */
    public function migrateSettings()
    {
        if ($settings = get_option(OptionManager::databaseKey(4))) {
            update_option(OptionManager::databaseKey(5), $settings);
        }
        if ($trustalyze = glsr(OptionManager::class)->getWP('_glsr_trustalyze')) {
            update_option(glsr()->prefix.'trustalyze', $trustalyze);
            delete_option('_glsr_trustalyze');
        }
        delete_option('widget_site-reviews');
        delete_option('widget_site-reviews-form');
        delete_option('widget_site-reviews-summary');
        delete_option(glsr()->id.'activated');
        delete_transient(glsr()->id.'_cloudflare_ips');
        delete_transient(glsr()->id.'_remote_post_test');
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->migrateSettings();
        glsr(MigrateSidebars::class)->run();
        glsr(MigrateReviews::class)->run();
    }
}
