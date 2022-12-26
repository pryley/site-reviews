<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database\OptionManager;

class RetiredNotice extends AbstractNotice
{
    protected function canRender(): bool
    {
        if (empty(glsr()->retrieveAs('array', 'retired'))) {
            return false;
        }
        return parent::canRender();
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
        $screenIds = ['dashboard', 'plugins', 'update-core'];
        if (in_array(glsr_current_screen()->id, $screenIds)) {
            return true;
        }
        if (get_current_screen()->in_admin('network')) {
            return true;
        }
        return parent::isNoticeScreen();
    }
}
