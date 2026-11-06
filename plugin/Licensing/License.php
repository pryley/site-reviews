<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Licensing\Defaults\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

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
            $check = $this->answerOrLast($addonId, $license, $check);
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

    /**
     * Use the last answer for three days while the licence server cannot be reached.
     */
    protected function answerOrLast(string $addonId, string $license, Check $check): Check
    {
        if ($check->isReachable()) {
            $this->rememberAnswer($addonId, $license, $check);
            return $check;
        }
        $stored = Arr::consolidate(get_option($this->lastAnswerKey($addonId)));
        $age = time() - Cast::toInt($stored['time'] ?? 0);
        if ($age < static::LAST_ANSWER_FOR && Str::hash($license, 8) === ($stored['key'] ?? '')) {
            return new Check(glsr(CheckLicenseDefaults::class)->restrict(Arr::consolidate($stored['check'] ?? [])));
        }
        return $check;
    }

    protected function lastAnswerKey(string $addonId): string
    {
        return glsr()->prefix.'license_answer_'.$addonId;
    }

    protected function rememberAnswer(string $addonId, string $license, Check $check): void
    {
        $key = $this->lastAnswerKey($addonId);
        $stored = Arr::consolidate(get_option($key));
        $hash = Str::hash($license, 8);
        $unchanged = ($stored['check'] ?? null) === $check->toArray() && ($stored['key'] ?? null) === $hash;
        if ($unchanged && time() - Cast::toInt($stored['time'] ?? 0) < \HOUR_IN_SECONDS) {
            return; // status() runs several times a page; one write an hour is enough
        }
        update_option($key, ['check' => $check->toArray(), 'key' => $hash, 'time' => time()]);
    }
}
