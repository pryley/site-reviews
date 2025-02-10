<?php

namespace GeminiLabs\SiteReviews\Integrations;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Modules\Notice;

abstract class IntegrationHooks extends AbstractHooks
{
    protected function isEnabled(): bool
    {
        return true;
    }

    protected function isInstalled(): bool
    {
        return true;
    }

    protected function isVersionSupported(): bool
    {
        $supportedVersion = sanitize_text_field($this->supportedVersion());
        if (empty($supportedVersion)) {
            return true;
        }
        $version = sanitize_text_field($this->version());
        return version_compare($version, $supportedVersion, '>=');
    }

    protected function notify(string $name): void
    {
        $notice = _x('Update %s to version %s or higher to enable the integration with Site Reviews.', 'admin-text', 'site-reviews');
        $supportedVersion = sanitize_text_field($this->supportedVersion());
        glsr(Notice::class)->addWarning(sprintf($notice, $name, $supportedVersion));
    }

    protected function supportedVersion(): string
    {
        return '';
    }

    protected function version(): string
    {
        return '';
    }
}
