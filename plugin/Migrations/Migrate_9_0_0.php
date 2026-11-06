<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Licensing\LicenseServer;
use GeminiLabs\SiteReviews\Licensing\Verdict;

class Migrate_9_0_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->deleteApiIndex();
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
}
