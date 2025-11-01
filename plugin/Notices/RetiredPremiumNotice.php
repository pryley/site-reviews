<?php

namespace GeminiLabs\SiteReviews\Notices;

class RetiredPremiumNotice extends AbstractNotice
{
    protected string $type = 'notice-warning';

    protected function canLoad(): bool
    {
        if (empty(glsr()->retrieveAs('array', 'site-reviews-premium'))) {
            return false;
        }
        return parent::canLoad();
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
        if (in_array($screen->id, ['dashboard', 'plugins', 'update-core'])) {
            return true;
        }
        if (is_a($screen, 'WP_Screen') && $screen->in_admin('network')) {
            return true;
        }
        return parent::isNoticeScreen();
    }
}
