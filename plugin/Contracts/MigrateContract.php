<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool;
}
