<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate_6_2_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateAddonWoocommerce();
        return true;
    }

    public function migrateAddonWoocommerce(): void
    {
        $settings = Arr::consolidate(get_option(OptionManager::databaseKey(6)));
        if (empty($settings)) {
            return;
        }
        if (empty($settings['settings']['addons']['woocommerce'])) {
            return;
        }
        if (!empty($settings['settings']['integrations']['woocommerce'])) {
            return;
        }
        $woocommerce = $settings['settings']['addons']['woocommerce'];
        if ('yes' === Arr::get($woocommerce, 'experiments') && 'active' === Arr::get($woocommerce, 'experiment.wp_comments')) {
            $woocommerce['wp_comments'] = 'yes';
        }
        unset($woocommerce['experiments']);
        unset($woocommerce['experiment']);
        $settings['settings']['addons']['woocommerce'] = $woocommerce;
        update_option(OptionManager::databaseKey(6), $settings, true);
    }
}
