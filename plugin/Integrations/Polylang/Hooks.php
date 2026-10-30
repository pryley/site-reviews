<?php

namespace GeminiLabs\SiteReviews\Integrations\Polylang;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Integrations\Polylang\Controllers\Controller;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterSettings', 'site-reviews/settings'],
            ['filterSettingsCallback', 'site-reviews/settings/sanitize', 10, 2],
            ['renderNotice', 'admin_init'],
        ]);
        if ($this->isInstalled()) { // pll_current_language() needs Polylang
            $this->hook(Controller::class, [
                ['filterConfig', 'site-reviews/assets/config', 10, 2],
            ]);
        }
        if ($this->isEnabled()) {
            glsr(Multilingual::class)->register(glsr(Translations::class));
        }
    }

    protected function isEnabled(): bool
    {
        return $this->isInstalled()
            && 'polylang' === $this->option('general.multilingual');
    }

    protected function isInstalled(): bool
    {
        return defined('POLYLANG_VERSION');
    }
}
