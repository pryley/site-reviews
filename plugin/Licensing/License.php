<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Defaults\Updater\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;

class License
{
    // How long the last answer stands in for a server that cannot be reached.
    protected const LAST_ANSWER_FOR = 3 * \DAY_IN_SECONDS;

    public function forgetAnswer(string $addonId): void
    {
        delete_option($this->lastAnswerKey($addonId));
    }

    public function isPremium(): bool
    {
        return $this->status()['premium'];
    }

    public function premiumKey(): string
    {
        return $this->status()['premium_key'];
    }

    /**
     * The URL a licence is activated for.
     */
    public function siteUrl(): string
    {
        if (!is_multisite()) {
            return Url::home();
        }
        $network = strtolower((string) preg_replace('|^www\.|', '', (string) get_network()->domain));
        $site = strtolower((string) get_site()->domain);
        if ($site === $network || str_ends_with($site, '.'.$network)) {
            return Url::home();
        }
        return trailingslashit(home_url());
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
            $updater = new Updater($addonId, [
                'force' => false, // cached once per day
                'license' => $license,
            ]);
            $check = $this->answerOrLast($addonId, $license, $updater->checkLicense());
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

    /**
     * Use the last answer for three days while the licence server cannot be reached.
     */
    protected function answerOrLast(string $addonId, string $license, array $check): array
    {
        if (LicenseStatus::UNREACHABLE !== LicenseStatus::of($check)) {
            $this->rememberAnswer($addonId, $license, $check);
            return $check;
        }
        $stored = Arr::consolidate(get_option($this->lastAnswerKey($addonId)));
        $age = time() - Cast::toInt($stored['time'] ?? 0);
        if ($age < static::LAST_ANSWER_FOR && Str::hash($license, 8) === ($stored['key'] ?? '')) {
            return glsr(CheckLicenseDefaults::class)->restrict(Arr::consolidate($stored['check'] ?? []));
        }
        return $check;
    }

    protected function lastAnswerKey(string $addonId): string
    {
        return glsr()->prefix.'license_answer_'.$addonId;
    }

    protected function rememberAnswer(string $addonId, string $license, array $check): void
    {
        $key = $this->lastAnswerKey($addonId);
        $stored = Arr::consolidate(get_option($key));
        $hash = Str::hash($license, 8);
        $unchanged = ($stored['check'] ?? null) === $check && ($stored['key'] ?? null) === $hash;
        if ($unchanged && time() - Cast::toInt($stored['time'] ?? 0) < \HOUR_IN_SECONDS) {
            return; // status() runs several times a page; one write an hour is enough
        }
        update_option($key, ['check' => $check, 'key' => $hash, 'time' => time()]);
    }
}
