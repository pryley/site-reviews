<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\License;

class LicenseNotice extends AbstractNotice
{
    public function render(): void
    {
        $licensing = glsr(License::class)->status();
        if ($licensing['isSaved'] && $licensing['isValid']) {
            return;
        }
        if ($this->isDismissed() && $licensing['isValid']) {
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
