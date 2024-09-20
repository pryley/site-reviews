<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\License;

class LicenseNotice extends AbstractNotice
{
    public function render(): void
    {
        $licensing = glsr(License::class)->status();
        if ($licensing['licensed'] && !$licensing['invalid'] && !$licensing['missing']) {
            return; // license is valid and saved
        }
        if ($this->isDismissed()) {
            return;
        }
        glsr()->render('partials/notices/license', $licensing);
    }

    protected function canRender(): bool
    {
        if (!$this->hasPermission()) {
            return false;
        }
        if (!$this->isNoticeScreen()) {
            return false;
        }
        return true;
    }

    protected function isMonitored(): bool
    {
        return false;
    }

    protected function version(): string
    {
        return glsr()->version('minor');
    }
}
