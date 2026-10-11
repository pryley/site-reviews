<?php

namespace GeminiLabs\SiteReviews\Licensing;

/**
 * What the licence server's check_license answer means for this site.
 */
class LicenseStatus
{
    public const DISABLED = 'disabled';
    public const EXHAUSTED = 'exhausted'; // not active here, and no activations left
    public const EXPIRED = 'expired';
    public const INACTIVE = 'inactive'; // not active here, with activations left
    public const INVALID = 'invalid';
    public const MISMATCH = 'mismatch'; // another product's key
    public const OTHER = 'other';
    public const UNREACHABLE = 'unreachable';
    public const VALID = 'valid';

    /**
     * A license_limit of 0 is unlimited.
     */
    public static function hasActivationsLeft(array $check): bool
    {
        return $check['activations_left'] > 0 || 0 === $check['license_limit'];
    }

    public static function of(array $check): string
    {
        if ('unknown' === $check['license']) {
            return static::UNREACHABLE;
        }
        if (true !== $check['success']) {
            return static::INVALID;
        }
        if ('disabled' === $check['license']) {
            return static::DISABLED;
        }
        if (in_array($check['license'], ['invalid_item_id', 'item_name_mismatch'], true)) {
            return static::MISMATCH;
        }
        if ('expired' === $check['license']) {
            return static::EXPIRED;
        }
        if ('valid' === $check['license']) {
            return static::VALID;
        }
        if (!in_array($check['license'], ['inactive', 'site_inactive'], true)) {
            return static::OTHER;
        }
        return static::hasActivationsLeft($check) ? static::INACTIVE : static::EXHAUSTED;
    }
}
