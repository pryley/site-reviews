<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class WelcomeNotice extends AbstractNotice
{
    protected int $priority = 0;
    protected string $type = 'popup';

    protected function canLoad(): bool
    {
        if ('0.0.0' !== glsr(OptionManager::class)->get('version_upgraded_from')) {
            return false;
        }
        return parent::canLoad();
    }

    protected function data(): array
    {
        return [
            'icon' => glsr(Svg::class)->get('assets/images/menu-icon.svg', [
                'fill' => '#fff',
                'width' => 26,
            ]),
        ];
    }

    protected function isDismissible(): bool
    {
        return false;
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
