<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Addons\Updater;

class License
{
    public function isPremium(): bool
    {
        return $this->status()['premium'];
    }

    public function premiumKey(): string
    {
        return $this->status()['premium_key'];
    }

    public function status(): array
    {
        $licensed = glsr()->retrieveAs('array', 'licensed', []);
        $status = array_fill_keys(['expired', 'invalid', 'licensed', 'missing', 'premium'], false);
        $status['premium_key'] = '';
        // An installed and registered premium plugin counts as premium even
        // before a license is entered; the license notices still apply.
        $status['premium'] = !is_null(glsr()->addon('site-reviews-premium'));
        if (!$status['premium'] && !empty(glsr_get_option('licenses.site-reviews-premium'))) {
            // The key the License Key row saved before premium was installed.
            $licensed['site-reviews-premium'] = ['name' => 'site-reviews-premium'];
        }
        foreach ($licensed as $addonId => $addon) {
            $license = glsr_get_option("licenses.{$addonId}");
            $status['licensed'] = true;
            if (empty($license)) {
                $status['missing'] = true;
                continue;
            }
            $updater = new Updater($addonId, [
                'force' => false, // cached once per day
                'license' => $license,
            ]);
            $check = $updater->checkLicense();
            $verdict = LicenseStatus::of($check);
            if (LicenseStatus::EXPIRED === $verdict) {
                $status['expired'] = true;
            }
            if (!in_array($verdict, [LicenseStatus::UNREACHABLE, LicenseStatus::VALID], true)) {
                $status['invalid'] = true; // no answer is no verdict
            }
            if ($check['is_premium_license']) {
                $status['premium'] = true;
                if (LicenseStatus::VALID === $verdict && '' === $status['premium_key']) {
                    $status['premium_key'] = $license;
                }
            }
        }
        return $status;
    }
}
