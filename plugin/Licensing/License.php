<?php

namespace GeminiLabs\SiteReviews\Licensing;

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
        // An installed and registered premium plugin counts as premium.
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
            $check = glsr(LicenseServer::class)->check($addonId, $license); // the day's cache answers a passive check
            if (Verdict::Expired === $check->verdict) {
                $status['expired'] = true;
            }
            if (!in_array($check->verdict, [Verdict::Unreachable, Verdict::Valid], true)) {
                $status['invalid'] = true; // no answer is no verdict
            }
            if ($check->premium) {
                $status['premium'] = true;
                if ($check->isValid() && '' === $status['premium_key']) {
                    $status['premium_key'] = $license;
                }
            }
        }
        return $status;
    }
}
