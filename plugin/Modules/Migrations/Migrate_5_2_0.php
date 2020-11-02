<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

class Migrate_5_2_0
{
    /**
     * @return void
     */
    public function run()
    {
        wp_clear_scheduled_hook('site-reviews/schedule/session/purge');
    }
}
