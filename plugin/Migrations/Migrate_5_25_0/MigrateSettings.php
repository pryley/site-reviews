<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class MigrateSettings implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateSettings();
        return true;
    }

    protected function migrateSettings(): void
    {
        $oldSettings = Arr::consolidate(get_option(OptionManager::databaseKey(4)));
        $newSettings = Arr::consolidate(get_option(OptionManager::databaseKey(5)));
        if (empty($oldSettings)) {
            return;
        }
        if (empty($newSettings)) {
            $newSettings = $oldSettings;
        }
        $settings = Arr::flatten($newSettings);
        $mappedKeys = [
            'settings.general.require.login_register' => 'settings.general.require.register',
        ];
        foreach ($mappedKeys as $oldKey => $newKey) {
            if (!empty($settings[$oldKey]) && empty($settings[$newKey])) {
                $settings[$newKey] = $settings[$oldKey];
            }
        }
        if (empty($settings['settings.submissions.captcha'])) {
            $integration = !empty($settings['settings.submissions.recaptcha.integration'])
                ? 'recaptcha_v2_invisible'
                : '';
            $position = Arr::get($newSettings, 'settings.submissions.recaptcha.position', 'bottomleft');
            $usage = Arr::get($newSettings, 'settings.submissions.recaptcha.integration', 'all');
            $settings['settings.submissions.captcha.integration'] = $integration;
            $settings['settings.submissions.captcha.position'] = $position;
            $settings['settings.submissions.captcha.theme'] = 'light';
            $settings['settings.submissions.captcha.usage'] = $usage;
            $settings['settings.submissions.recaptcha_v3.threshold'] = 0.5;
        }
        unset($settings['settings.addons.trustalyze']);
        unset($settings['settings.general.trustalyze']);
        unset($settings['settings.general.trustalyze_email']);
        unset($settings['settings.general.trustalyze_serial']);
        unset($settings['settings.submissions.recaptcha.integration']);
        unset($settings['settings.submissions.recaptcha.position']);
        unset($settings['counts']);
        unset($settings['last_review_count']);
        $settings = Arr::unflatten($settings);
        update_option(OptionManager::databaseKey(5), $settings, true);
    }
}
