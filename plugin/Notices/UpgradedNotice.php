<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database\OptionManager;

class UpgradedNotice extends AbstractNotice
{
    protected function canRender(): bool
    {
        if ('0.0.0' === glsr(OptionManager::class)->get('version_upgraded_from')) {
            return false;
        }
        return parent::canRender();
    }

    protected function version(): string
    {
        return glsr()->version('minor');
    }
}
