<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Migrate_6_1_0 implements MigrateContract
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
        if (6 !== (int) glsr()->version('major')) {
            return;
        }
        $defaults = Arr::unflatten(glsr()->defaults());
        $settings = Arr::consolidate(get_option(OptionManager::databaseKey(6)));
        if (empty($defaults['settings']) || empty($settings['settings'])) {
            return;
        }
        $defaultSettings = Arr::flatten($defaults['settings']);
        $dirtySettings = Arr::flatten($settings['settings']);
        $cleanSettings = [];
        foreach ($dirtySettings as $key => $values) {
            if (Str::startsWith($key, ['addons', 'licenses', 'strings'])) {
                $cleanSettings[$key] = $values;
                continue;
            }
            if (array_key_exists($key, $defaultSettings)) {
                $cleanSettings[$key] = $values;
            }
        }
        if (!empty($cleanSettings)) {
            $settings['settings'] = Arr::unflatten($cleanSettings);
            update_option(OptionManager::databaseKey(6), $settings, true);
        }
    }
}
