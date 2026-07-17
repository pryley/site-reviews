<?php
/*
 * FIXTURES for Modules\Migrate::runMigrations(), which resolves classes in the
 * real Migrations namespace by convention — so these must live there too. They
 * are required only by MigrateTest, never by the plugin: version 0.0.x is
 * below every real migration and the filenames here do not match the
 * Migrate_<version>.php pattern the directory scan looks for.
 */

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;

/**
 * Exists, but does not implement MigrateContract: runMigrations() must skip it.
 */
class Migrate_0_0_1
{
}

/**
 * A migration whose run() reports failure: it must stay pending.
 */
class Migrate_0_0_2 implements MigrateContract
{
    public function run(): bool
    {
        return false;
    }
}
