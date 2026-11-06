<?php

namespace GeminiLabs\SiteReviews\Licensing;

class License
{
    protected ?Status $status = null;
    protected string $statusFor = ''; // the keys and addons the status was built from

    public function isExpired(): bool
    {
        return $this->status()->expired;
    }

    public function isInvalid(): bool
    {
        return $this->status()->invalid;
    }

    public function isLicensed(): bool
    {
        return $this->status()->licensed;
    }

    public function isMissing(): bool
    {
        return $this->status()->missing;
    }

    public function isPremium(): bool
    {
        return $this->status()->premium;
    }

    public function premiumKey(): string
    {
        return $this->status()->premiumKey;
    }

    public function reset(): void
    {
        $this->status = null;
    }

    protected function status(): Status
    {
        $licensed = glsr()->retrieveAs('array', 'licensed', []);
        $isInstalled = !is_null(glsr()->addon('site-reviews-premium'));
        $statusFor = md5(serialize([array_keys($licensed), glsr_get_option('licenses', [], 'array'), $isInstalled]));
        if (null !== $this->status && $statusFor === $this->statusFor) {
            return $this->status;
        }
        $status = new Status();
        $status->premium = $isInstalled; // an installed and registered premium plugin counts as premium
        if (!$isInstalled && !empty(glsr_get_option('licenses.site-reviews-premium'))) {
            // The key that the License Key row saved before premium was installed.
            $licensed['site-reviews-premium'] = ['name' => 'site-reviews-premium'];
        }
        foreach ($licensed as $addonId => $addon) {
            $license = glsr_get_option("licenses.{$addonId}");
            $status->licensed = true;
            if (empty($license)) {
                $status->missing = true;
                continue;
            }
            $check = glsr(LicenseServer::class)->check($addonId, $license); // passive checks are cached
            if (Verdict::Expired === $check->verdict) {
                $status->expired = true;
            }
            if (!in_array($check->verdict, [Verdict::Unreachable, Verdict::Valid], true)) {
                $status->invalid = true; // no answer counts as invalid
            }
            $this->markPremium($status, $check, $license);
        }
        $this->markPremiumFromSavedKeys($status, array_keys($licensed));
        $this->status = $status;
        $this->statusFor = $statusFor;
        return $status;
    }

    /**
     * Mark the status premium when the server flags the key.
     */
    protected function markPremium(Status $status, Check $check, string $license): void
    {
        if (!$check->premium) {
            return;
        }
        $status->premium = true;
        if ($check->isValid() && '' === $status->premiumKey) {
            $status->premiumKey = $license;
        }
    }

    /**
     * Look through the saved keys of the addons that are not licensed here.
     *
     * @param string[] $checked the addon ids the licensed loop has already checked
     */
    protected function markPremiumFromSavedKeys(Status $status, array $checked): void
    {
        $saved = array_diff_key(glsr_get_option('licenses', [], 'array'), array_flip($checked));
        foreach ($saved as $addonId => $license) {
            if ('' !== $status->premiumKey) {
                break;
            }
            if (!is_string($license) || '' === $license) {
                continue;
            }
            $this->markPremium($status, glsr(LicenseServer::class)->check($addonId, $license), $license);
        }
    }
}
