<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\CountManager;

class Migrate_5_10_3 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        glsr(CountManager::class)->recalculate();
        return true;
    }
}
