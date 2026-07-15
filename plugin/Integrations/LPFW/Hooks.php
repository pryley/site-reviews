<?php

namespace GeminiLabs\SiteReviews\Integrations\LPFW;

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
            ['filterEarnPointTypes', 'lpfw_get_point_earn_source_types'],
            ['onApprovedReview', 'site-reviews/review/approved', 20],
            ['onCreatedReview', 'site-reviews/review/created', 20],
        ]);
    }

    protected function isEnabled(): bool
    {
        if (!$this->isInstalled()) {
            return false;
        }
        $option = \LPFW()?->Plugin_Constants->EARN_ACTION_PRODUCT_REVIEW ?? '';
        return !empty($option) && 'yes' === get_option($option, 'yes');
    }

    protected function isInstalled(): bool
    {
        return function_exists('LPFW') && class_exists('LPFW');
    }
}
