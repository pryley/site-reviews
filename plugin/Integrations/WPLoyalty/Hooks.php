<?php

namespace GeminiLabs\SiteReviews\Integrations\WPLoyalty;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('WPLoyalty');
            return;
        }
        $this->hook(Controller::class, [
            ['filterFieldAfter', 'site-reviews/review-form/fields/visible', 10, 2],
            ['onApprovedReview', 'site-reviews/review/approved', 20],
            ['onCreatedReview', 'site-reviews/review/created', 20],
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('Wlr\App\Helpers\EarnCampaign')
            && class_exists('\Wlr\App\Helpers\Woocommerce')
            && class_exists('\Wlr\App\Premium\Helpers\ProductReview')
            && class_exists('\Wlr\App\Premium\Helpers\Referral');
    }

    protected function supportedVersion(): string
    {
        return '1.4.0';
    }

    protected function version(): string
    {
        return defined('WLR_PLUGIN_VERSION')
            ? (string) \WLR_PLUGIN_VERSION
            : '';
    }
}
