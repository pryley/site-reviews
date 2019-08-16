<?php

namespace GeminiLabs\SiteReviews\Modules\Upgrader;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Upgrade_4_0_0
{
    public function __construct()
    {
        glsr(OptionManager::class)->set('settings.submissions.blacklist.integration', '');
        $this->deleteSessions();
        delete_transient(Application::ID.'_cloudflare_ips');
    }

    /**
     * @return void
     */
    public function deleteSessions()
    {
        global $wpdb;
        $wpdb->query("
            DELETE
            FROM {$wpdb->options}
            WHERE option_name LIKE '_glsr_session%'
        ");
    }
}
