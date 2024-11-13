<?php

namespace GeminiLabs\SiteReviews\Integrations;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

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
        $version = glsr(Sanitizer::class)->sanitizeVersion($this->version());
        $supportedVersion = glsr(Sanitizer::class)->sanitizeVersion($this->supportedVersion());
        if (empty($supportedVersion)) {
            return true;
        }
        return version_compare($version, $supportedVersion, '>=');
    }

    protected function notify(string $name): void
    {
        $notice = _x('Update %s to v%s or higher to enable the integration with Site Reviews.', 'admin-text', 'site-reviews');
        $version = glsr(Sanitizer::class)->sanitizeVersion($this->supportedVersion());
        glsr(Notice::class)->addWarning(sprintf($notice, $name, $version));
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
