<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Notices;

use GeminiLabs\SiteReviews\Notices\AbstractNotice;

class NetworkNotice extends AbstractNotice
{
    public function path(): string
    {
        return "integrations/multilingualpress/notices/{$this->key}";
    }

    protected function canLoad(): bool
    {
        if (!is_plugin_active_for_network(glsr()->basename)) {
            return false;
        }
        return parent::canLoad();
    }

    protected function isDismissible(): bool
    {
        return false;
    }

    protected function isNoticeScreen(): bool
    {
        if (!parent::isNoticeScreen()) {
            return false;
        }
        if (!str_ends_with(glsr_current_screen()->id, 'glsr-settings')) {
            return false;
        }
        return true;
    }
}
