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
