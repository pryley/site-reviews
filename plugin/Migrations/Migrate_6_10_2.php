<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate_6_10_2 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateSettings();
        return true;
    }

    /**
     * Remove invalid settings.
     */
    public function migrateSettings(): void
    {
        $settings = Arr::consolidate(get_option(OptionManager::databaseKey(6)));
        if (empty($settings)) {
            return;
        }
        unset($settings['settings']['schema']['integration']['types']);
        update_option(OptionManager::databaseKey(6), $settings, true);
    }
}
