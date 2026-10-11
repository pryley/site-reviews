<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Licensing\License;

class Migrate_8_4_0 implements MigrateContract
{
    public function run(): bool
    {
        return $this->activateLicensesForMappedSite();
    }

    public function activateLicensesForMappedSite(): bool
    {
        if (glsr(License::class)->siteUrl() === Url::home()) {
            return true;
        }
        $licenses = array_filter(Arr::consolidate(glsr_get_option('licenses')));
        foreach ($licenses as $addonId => $license) {
            $updater = new Updater((string) $addonId, [
                'force' => true,
                'license' => $license,
            ]);
            $check = $updater->checkLicense();
            if ('unknown' === $check['license']) {
                return false; // no answer so run again next time
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
