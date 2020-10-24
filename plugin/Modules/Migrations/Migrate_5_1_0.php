<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Modules\Console;

class Migrate_5_1_0
{
    /**
     * @return void
     */
    public function run()
    {
        if (!glsr(SqlSchema::class)->isInnodb('posts')
            || !glsr(SqlSchema::class)->isInnodb('terms')
            || !glsr(SqlSchema::class)->isInnodb('users')) {
            glsr(Console::class)->clear(); // clear all of the contraint errors
        }
    }
}
