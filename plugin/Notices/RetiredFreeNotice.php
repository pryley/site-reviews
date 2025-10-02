<?php

namespace GeminiLabs\SiteReviews\Notices;

class RetiredFreeNotice extends AbstractNotice
{
    protected string $type = 'notice-error';

    protected function canLoad(): bool
    {
        if (empty(glsr()->retrieveAs('array', 'retired'))) {
            return false;
        }
        return parent::canLoad();
    }

    protected function data(): array
    {
        return [
            'addons' => glsr()->retrieveAs('array', 'retired'),
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
