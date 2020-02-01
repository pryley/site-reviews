<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate_4_0_2
{
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

    /**
     * @return void
     */
    public function migrateSettings()
    {
        if ($settings = get_option(OptionManager::databaseKey(3))) {
            $multilingual = 'yes' == Arr::get($settings, 'settings.general.support.polylang')
                ? 'polylang'
                : '';
            $settings = Arr::set($settings, 'settings.general.multilingual', $multilingual);
            $settings = Arr::set($settings, 'settings.general.rebusify', 'no');
            $settings = Arr::set($settings, 'settings.general.rebusify_email', '');
            $settings = Arr::set($settings, 'settings.general.rebusify_serial', '');
            $settings = Arr::set($settings, 'settings.reviews.name.format', '');
            $settings = Arr::set($settings, 'settings.reviews.name.initial', '');
            $settings = Arr::set($settings, 'settings.submissions.blacklist.integration', '');
            $settings = Arr::set($settings, 'settings.submissions.limit', '');
            $settings = Arr::set($settings, 'settings.submissions.limit_whitelist.email', '');
            $settings = Arr::set($settings, 'settings.submissions.limit_whitelist.ip_address', '');
            $settings = Arr::set($settings, 'settings.submissions.limit_whitelist.username', '');
            unset($settings['settings']['general']['support']);
            update_option(OptionManager::databaseKey(4), $settings);
        }
    }

    /**
     * @return void
     */
    public function protectMetaKeys()
    {
        global $wpdb;
        $keys = array_keys(glsr(CreateReviewDefaults::class)->defaults());
        $keys = implode("','", $keys);
        $postType = Application::POST_TYPE;
        $wpdb->query("
            UPDATE {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.id = pm.post_id
            SET pm.meta_key = CONCAT('_', pm.meta_key)
            WHERE pm.meta_key IN ('{$keys}')
            AND p.post_type = '{$postType}'
        ");
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->migrateSettings();
        $this->protectMetaKeys();
        $this->deleteSessions();
        delete_transient(Application::ID.'_cloudflare_ips');
    }
}
