<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Database\CountManager;

class Migrate_5_6_0
{
    /**
     * @return void
     */
    public function run()
    {
        glsr(CountManager::class)->recalculate();
    }
}
