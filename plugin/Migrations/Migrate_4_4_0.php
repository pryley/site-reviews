<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Role;

class Migrate_4_4_0 implements MigrateContract
{
    /**
     * Run migration
     */
    public function run(): bool
    {
        glsr(Role::class)->resetAll();
        return true;
    }
}
