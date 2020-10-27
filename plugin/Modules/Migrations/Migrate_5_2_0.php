<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Modules\Console;

class Migrate_5_2_0
{
    /**
     * @return void
     */
    public function run()
    {
        if (!empty(get_option(glsr()->prefix.'db_version'))) {
            add_option(glsr()->prefix.'db_version', '5.2');
        }
    }
}
