<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Migrate_7_3_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->migrateSettings();
        return true;
    }

    public function migrateSettings(): void
    {
        $settings = get_option(OptionManager::databaseKey());
        if (isset($settings['settings']['form']['captcha']['position'])) {
            $position = $settings['settings']['form']['captcha']['position'];
            if ('inline' === $position) {
                $position = 'inline_below';
            }
            $settings['settings']['form']['captcha']['badge'] = $position;
        }
        update_option(OptionManager::databaseKey(), $settings, true);
    }
}
