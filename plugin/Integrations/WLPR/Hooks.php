<?php

namespace GeminiLabs\SiteReviews\Integrations\WLPR;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isEnabled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['onApprovedReview', 'site-reviews/review/approved', 20],
            ['onCreatedReview', 'site-reviews/review/created', 20],
        ]);
    }

    protected function isEnabled(): bool
    {
        $settings = get_option('wlpr_settings');
        return Arr::getAs('bool', $settings, 'wlpr_enable_review_reward', true);
    }

    protected function isInstalled(): bool
    {
        return class_exists('Wlpr\App\Helpers\Loyalty')
            && class_exists('Wlpr\App\Helpers\Point')
            && class_exists('Wlpr\App\Models\PointAction');
    }
}
