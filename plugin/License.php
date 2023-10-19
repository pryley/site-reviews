<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Addons\Updater;

class License
{
    public function isLicensed(): bool
    {
        $addons = glsr()->addons;
        $status = $this->status();
        if (empty($addons)) {
            return false;
        }
        return $status['isValid'] && $status['isSaved'];
    }

    public function status(): array
    {
        $isFree = true; // priority 1
        $isValid = true; // priority 2
        $isSaved = true; // priority 3
        foreach (glsr()->updated as $addonId => $addon) {
            if (!$addon['licensed']) {
                continue; // this is a free addon
            }
            $isFree = false; // there are premium addons installed
            if (empty(glsr_get_option('licenses.'.$addonId))) {
                $isSaved = false;
                continue; // the license has not been saved in the settings
            }
            $licenseStatus = get_option(glsr()->prefix.$addonId);
            if (empty($licenseStatus)) { // the license status has not been stored
                $license = glsr_get_option('licenses.'.$addonId);
                $updater = new Updater($addon['updateUrl'], $addon['file'], $addonId, compact('license'));
                if (!$updater->isLicenseValid()) {
                    $isValid = false;
                    break;
                }
            }
        }
        return compact('isFree', 'isSaved', 'isValid');
    }
}
