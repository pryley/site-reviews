<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database\OptionManager;

class WelcomeNotice extends AbstractNotice
{
    protected function canLoad(): bool
    {
        if ('0.0.0' !== glsr(OptionManager::class)->get('version_upgraded_from')) {
            return false;
        }
        return parent::canLoad();
    }

    protected function isMonitored(): bool
    {
        return true;
    }

    protected function isNoticeScreen(): bool
    {
        if ('dashboard' === glsr_current_screen()->id) {
            return true;
        }
        return parent::isNoticeScreen();
    }

    protected function deferVersion(): string
    {
        return glsr()->version('minor');
    }
}
