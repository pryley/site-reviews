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
        if (isset($settings['settings']['forms']['captcha']['position'])) {
            $position = $settings['settings']['forms']['captcha']['position'];
            if ('inline' === $position) {
                $position = 'inline_below'; // "inline" became a choice of two
            }
            $settings['settings']['forms']['captcha']['badge'] = $position;
        }
        update_option(OptionManager::databaseKey(), $settings, true);
    }
}
