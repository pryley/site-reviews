<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database\OptionManager;

class UpgradedNotice extends AbstractNotice
{
    protected function canLoad(): bool
    {
        if ('0.0.0' === glsr(OptionManager::class)->get('version_upgraded_from')) {
            return false;
        }
        return parent::canLoad();
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
