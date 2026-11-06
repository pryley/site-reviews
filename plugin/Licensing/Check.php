<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * The licence server's answer for a license key check.
 */
final class Check
{
    public readonly int $activationsLeft;
    public readonly string $connectUrl; // the store page that installs premium
    public readonly string $expires; // Y-m-d H:i:s or empty
    public readonly bool $lifetime;
    public readonly int $licenseLimit; // 0 is unlimited
    public readonly bool $premium; // if the key includes Site Reviews Premium
    public readonly string $renewalUrl;
    public readonly string $subscription;
    public readonly Verdict $verdict;

    public function __construct(private array $values)
    {
        $this->activationsLeft = Cast::toInt($values['activations_left'] ?? 0);
        $this->connectUrl = Cast::toString($values['connect_url'] ?? '');
        $this->expires = Cast::toString($values['expires'] ?? '');
        $this->lifetime = Cast::toBool($values['lifetime'] ?? false);
        $this->licenseLimit = Cast::toInt($values['license_limit'] ?? 0);
        $this->premium = Cast::toBool($values['is_premium_license'] ?? false);
        $this->renewalUrl = Cast::toString($values['renewal_url'] ?? '');
        $this->subscription = Cast::toString($values['subscription'] ?? '');
        $this->verdict = $this->verdict($values);
    }

    /**
     * Tell whether the key expires within thirty days.
     */
    public function expiresSoon(): bool
    {
        $expires = strtotime($this->expires);
        return !$this->lifetime && $expires > time() && $expires - time() < 30 * \DAY_IN_SECONDS;
    }

    /**
     * A license_limit of 0 is unlimited.
     */
    public function hasActivationsLeft(): bool
    {
        return $this->activationsLeft > 0 || 0 === $this->licenseLimit;
    }

    public function isReachable(): bool
    {
        return Verdict::Unreachable !== $this->verdict;
    }

    public function isValid(): bool
    {
        return Verdict::Valid === $this->verdict;
    }

    public function toArray(): array
    {
        return $this->values;
    }

    private function verdict(array $values): Verdict
    {
        $license = Cast::toString($values['license'] ?? '');
        if ('unknown' === $license) {
            return Verdict::Unreachable;
        }
        if (true !== ($values['success'] ?? false)) {
            return Verdict::Invalid;
        }
        if ('disabled' === $license) {
            return Verdict::Disabled;
        }
        if (in_array($license, ['invalid_item_id', 'item_name_mismatch'], true)) {
            return Verdict::Mismatch;
        }
        if ('expired' === $license) {
            return Verdict::Expired;
        }
        if ('valid' === $license) {
            return Verdict::Valid;
        }
        if (!in_array($license, ['inactive', 'site_inactive'], true)) {
            return Verdict::Other;
        }
        return $this->hasActivationsLeft() ? Verdict::Inactive : Verdict::Exhausted;
    }
}
