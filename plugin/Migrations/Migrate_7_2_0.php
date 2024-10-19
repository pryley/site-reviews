<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Migrate_7_2_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->migrateSettings();
        return true;
    }

    public function migrateSettings(): void
    {
        $settings = get_option(OptionManager::databaseKey());
        if (isset($settings['settings']['addons']['woocommerce'])) {
            $settings['settings']['integrations']['woocommerce'] = $settings['settings']['addons']['woocommerce'];
            unset($settings['settings']['addons']['woocommerce']);
        }
        update_option(OptionManager::databaseKey(), $settings, true);
    }
}
