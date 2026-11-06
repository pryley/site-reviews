<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Licensing\License;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class LicenseExpiredNotice extends AbstractNotice
{
    protected string $type = 'banner';

    public function render(): void
    {
        $license = glsr(License::class);
        if (!$license->isLicensed() || !$license->isExpired()) {
            return;
        }
        if (!$this->canRender()) {
            return;
        }
        echo glsr(Builder::class)->div([
            'class' => $this->classAttr(),
            'data-notice' => get_class($this),
            'text' => glsr()->build($this->path()),
        ]);
    }

    protected function isMonitored(): bool
    {
        return true;
    }

    protected function deferVersion(): string
    {
        return glsr()->version('minor');
    }
}
