<?php

namespace GeminiLabs\SiteReviews\Notices;

class PremiumNotice extends AbstractNotice
{
    protected function canRender(): bool
    {
        if (empty(glsr()->retrieveAs('array', 'site-reviews-premium'))) {
            return false;
        }
        return parent::canRender();
    }

    protected function data(): array
    {
        return [
            'addons' => glsr()->retrieveAs('array', 'site-reviews-premium'),
        ];
    }

    protected function hasPermission(): bool
    {
        return true;
    }

    protected function isDismissible(): bool
    {
        return false;
    }

    protected function isNoticeScreen(): bool
    {
        $screen = glsr_current_screen();
        $screenIds = ['dashboard', 'plugins', 'update-core'];
        if (in_array($screen->id, $screenIds)) {
            return true;
        }
        if (method_exists($screen, 'in_admin') && $screen->in_admin('network')) {
            return true;
        }
        return parent::isNoticeScreen();
    }
}
