<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate_5_25_0
{
    /**
     * @return bool
     */
    public function run()
    {
        return $this->migrateSettings();
    }

    /**
     * @return bool
     */
    public function migrateSettings()
    {
        if ($settings = get_option(OptionManager::databaseKey(5))) {
            if (!empty(Arr::get($settings, 'settings.submissions.captcha'))) {
                return true;
            }
            $integration = !empty(Arr::get($settings, 'settings.submissions.recaptcha.integration'))
                ? 'recaptcha_v2_invisible'
                : '';
            $position = Arr::get($settings, 'settings.submissions.recaptcha.position', 'bottomleft');
            $usage = Arr::get($settings, 'settings.submissions.recaptcha.integration', 'all');
            $settings = Arr::set($settings, 'settings.submissions.captcha.integration', $integration);
            $settings = Arr::set($settings, 'settings.submissions.captcha.position', $position);
            $settings = Arr::set($settings, 'settings.submissions.captcha.theme', 'light');
            $settings = Arr::set($settings, 'settings.submissions.captcha.usage', $usage);
            $settings = Arr::set($settings, 'settings.submissions.recaptcha_v3.threshold', 0.5);
            unset($settings['settings']['submissions']['recaptcha']['integration']);
            unset($settings['settings']['submissions']['recaptcha']['position']);
            update_option(OptionManager::databaseKey(5), $settings);
            delete_transient(glsr()->prefix.'system_info');
            glsr()->discard('settings');
        }
        return true;
    }
}
