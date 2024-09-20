<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Addons\Updater;

class License
{
    public function isPremium(): bool
    {
        return $this->status()['premium'];
    }

    public function status(): array
    {
        $licensed = glsr()->retrieveAs('array', 'licensed', []);
        $status = array_fill_keys(['expired', 'invalid', 'licensed', 'missing', 'premium'], false);
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
            if ('expired' === $check['license']) {
                $status['expired'] = true;
            }
            if ('valid' !== $check['license']) {
                $status['invalid'] = true;
            }
            if ($check['is_premium_license']) {
                $status['premium'] = true;
            }
        }
        return $status;
    }
}
