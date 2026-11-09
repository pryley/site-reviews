<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Licensing\LicenseServer;
use GeminiLabs\SiteReviews\Licensing\Verdict;
use GeminiLabs\SiteReviews\Modules\Console;

class Migrate_9_0_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->deleteApiIndex();
        $this->deleteLastAnswers();
        $this->moveConsoleOffDebugLevel();
        return $this->activateLicensesForMappedSite();
    }

    public function activateLicensesForMappedSite(): bool
    {
        $server = glsr(LicenseServer::class);
        if ($server->activationUrl() === Url::home()) {
            return true;
        }
        $licenses = array_filter(Arr::consolidate(glsr_get_option('licenses')));
        foreach ($licenses as $addonId => $license) {
            // Ask now: a cached answer is from before the URL changed.
            $check = $server->check((string) $addonId, $license, true);
            if (!$check->isReachable()) {
                return false; // no answer so run again next time
            }
            if (Verdict::Inactive === $check->verdict) {
                $server->activate((string) $addonId, $license);
            }
        }
        return true;
    }

    /**
     * Delete the list of every cached API response that 8.2.0 to 8.3.x kept in a site option.
     */
    public function deleteApiIndex(): void
    {
        delete_site_option(glsr()->prefix.'api_transients');
    }

    /**
     * Delete the autoloaded option 8.3.6 and 8.3.7 kept per addon for the last answer of the licence server.
     */
    public function deleteLastAnswers(): void
    {
        global $wpdb;
        $options = $wpdb->get_col($wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like(glsr()->prefix.'license_answer_').'%'
        ));
        foreach ($options as $option) {
            delete_option($option); // through the options cache, which a raw delete leaves stale
        }
    }

    /**
     * Move a Console saved at "Level 0: Debugging" to "Level 1: Informational".
     */
    public function moveConsoleOffDebugLevel(): void
    {
        $level = get_option(Console::LOG_LEVEL_KEY);
        if (false !== $level && Console::DEBUG === Cast::toInt($level)) {
            update_option(Console::LOG_LEVEL_KEY, Console::INFO);
        }
    }
}
