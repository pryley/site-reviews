<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\PremiumLicense;

/**
 * A saved key that includes premium, while premium is not installed.
 */
class LicensePremiumNotice extends AbstractNotice
{
    protected string $type = 'banner';

    public function render(): void
    {
        if ('' === glsr(License::class)->premiumKey()) { // cached daily
            return;
        }
        if (glsr(PremiumLicense::class)->isOnDisk()) {
            return;
        }
        parent::render();
    }

    protected function canLoad(): bool
    {
        if (str_ends_with(glsr_current_screen()->base, '-settings')) {
            return false; // the page it links to
        }
        return parent::canLoad();
    }

    protected function deferVersion(): string
    {
        return glsr()->version('minor');
    }

    protected function hasPermission(): bool
    {
        return current_user_can('install_plugins');
    }

    protected function isMonitored(): bool
    {
        return true;
    }
}
