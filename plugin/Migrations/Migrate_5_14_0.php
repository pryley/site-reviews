<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;

class Migrate_5_14_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        glsr(Database::class)->deleteInvalidReviews();
        return true;
    }
}
