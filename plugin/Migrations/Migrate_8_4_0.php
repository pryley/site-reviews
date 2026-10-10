<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;

class Migrate_8_4_0 implements MigrateContract
{
    public function run(): bool
    {
        return $this->activateLicensesForMappedSite();
    }

    /**
     * A sub-site on a mapped domain now sends its own URL to the licence server instead of the network's.
     */
    public function activateLicensesForMappedSite(): bool
    {
        if (Url::license() === Url::home()) {
            return true; // the URL the server is sent has not changed
        }
        $licenses = array_filter(Arr::consolidate(glsr_get_option('licenses')));
        foreach ($licenses as $addonId => $license) {
            $updater = new Updater((string) $addonId, [
                'force' => true,
                'license' => $license,
            ]);
            $check = $updater->checkLicense();
            if ('unknown' === $check['license']) {
                return false; // no answer: run again next time
            }
            if (!in_array($check['license'], ['inactive', 'site_inactive'], true)) {
                continue;
            }
            if ($check['activations_left'] > 0 || 0 === $check['license_limit']) {
                $updater->activateLicense();
            }
        }
        return true;
    }
}
