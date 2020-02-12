<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Role;

class Migrate_4_4_0
{
    /**
     * @return void
     */
    public function run()
    {
        glsr(Role::class)->resetAll();
    }
}
