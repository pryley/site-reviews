<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;

class Migrate_5_2_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        wp_clear_scheduled_hook('site-reviews/schedule/session/purge');
        return true;
    }
}
