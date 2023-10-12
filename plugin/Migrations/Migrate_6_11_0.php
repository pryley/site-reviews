<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;

class Migrate_6_11_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateReviewNotifications();
        return true;
    }

    /**
     * Rename WooCommerce Notification settings key (Review Notifications).
     */
    public function migrateReviewNotifications(): void
    {
        $oldKey = 'woocommerce_glsr_reminder_settings';
        $newKey = 'woocommerce_glsr_customer_review_reminder_settings';
        $old = get_option($oldKey);
        $new = get_option($newKey);
        if (!empty($new)) {
            return;
        }
        if (empty($old)) {
            return;
        }
        update_option($newKey, $old);
        delete_option($oldKey);
    }
}
