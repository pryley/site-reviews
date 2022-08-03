<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_0_0\MigrateReviews;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_0_0\MigrateSidebars;

class Migrate_5_0_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateSettings();
        $this->migrateWpOptions();
        glsr(MigrateSidebars::class)->run();
        glsr(MigrateReviews::class)->run();
        return true;
    }

    protected function migrateSettings(): void
    {
        $optionKeys = [
            'settings.general.trustalyze' => 'settings.addons.trustalyze.enabled',
            'settings.general.trustalyze_email' => 'settings.addons.trustalyze.email',
            'settings.general.trustalyze_serial' => 'settings.addons.trustalyze.serial',
        ];
        foreach ($optionKeys as $oldKey => $newKey) {
            $oldValue = glsr(OptionManager::class)->get($oldKey);
            $newValue = glsr(OptionManager::class)->get($newKey);
            if ($oldValue && empty($newValue)) {
                glsr(OptionManager::class)->set($newKey, $oldValue);
            }
            glsr(OptionManager::class)->delete($oldKey);
        }
        glsr(OptionManager::class)->delete('counts');
        glsr(OptionManager::class)->delete('last_review_count');
    }

    protected function migrateWpOptions(): void
    {
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
}
