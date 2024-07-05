<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Notices\AbstractNotice;
use GeminiLabs\SiteReviews\Role;

class Migrate_4_5_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateMetaKeys();
        $this->migratePermissions();
        $this->migrateSettings();
        $this->migrateUserMeta();
        $this->cleanup();
        return true;
    }

    protected function cleanup(): void
    {
        global $wpdb;
        $wpdb->query("
            DELETE
            FROM {$wpdb->options}
            WHERE option_name LIKE '_glsr_session%'
        ");
        delete_option('_glsr_rebusify');
        delete_transient(glsr()->id.'_cloudflare_ips');
    }

    protected function migrateMetaKeys(): void
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("
            UPDATE {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON (p.ID = pm.post_id)
            SET pm.meta_key = CONCAT('_', pm.meta_key)
            WHERE pm.meta_key IN ('assigned_to','author','avatar','content','custom','date','email','ip_address','pinned','rating','response','review_id','review_type','title','url')
            AND p.post_type = %s
        ", glsr()->post_type));
    }

    protected function migratePermissions(): void
    {
        glsr(Role::class)->resetAll();
    }

    protected function migrateSettings(): void
    {
        $oldSettings = Arr::consolidate(get_option(OptionManager::databaseKey(3)));
        $newSettings = Arr::consolidate(get_option(OptionManager::databaseKey(4)));
        if (empty($oldSettings)) {
            return;
        }
        if (empty($newSettings)) {
            $newSettings = $oldSettings;
        }
        $newSettings = Arr::flatten($newSettings);
        $newKeys = [
            'settings.general.multilingual',
            'settings.reviews.name.format',
            'settings.reviews.name.initial',
            'settings.submissions.blacklist.integration',
            'settings.submissions.limit',
            'settings.submissions.limit_whitelist.email',
            'settings.submissions.limit_whitelist.ip_address',
            'settings.submissions.limit_whitelist.username',
        ];
        foreach ($newKeys as $key) {
            if (!isset($newSettings[$key])) {
                $newSettings[$key] = '';
                if ('settings.general.multilingual' === $key && 'yes' === Arr::get($oldSettings, 'settings.general.support.polylang')) {
                    $newSettings[$key] = 'polylang';
                }
            }
        }
        unset($newSettings['settings.general.rebusify']);
        unset($newSettings['settings.general.rebusify_email']);
        unset($newSettings['settings.general.rebusify_serial']);
        $newSettings = Arr::unflatten($newSettings);
        update_option(OptionManager::databaseKey(4), $newSettings, true);
    }

    protected function migrateUserMeta(): void
    {
        $metaKey = AbstractNotice::USER_META_KEY;
        $userIds = get_users([
            'fields' => 'ID',
            'meta_compare' => 'EXISTS',
            'meta_key' => $metaKey,
        ]);
        foreach ($userIds as $userId) {
            $meta = (array) get_user_meta($userId, $metaKey, true);
            if (array_key_exists('rebusify', $meta)) {
                unset($meta['rebusify']);
                update_user_meta($userId, $metaKey, $meta);
            }
        }
    }
}
