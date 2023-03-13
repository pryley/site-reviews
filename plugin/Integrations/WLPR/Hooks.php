<?php

namespace GeminiLabs\SiteReviews\Integrations\WLPR;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!class_exists('Wlpr\App\Helpers\Loyalty')
            || !class_exists('Wlpr\App\Helpers\Point')
            || !class_exists('Wlpr\App\Models\PointAction')) {
            return;
        }
        $settings = get_option('wlpr_settings');
        if (!Arr::getAs('bool', $settings, 'wlpr_enable_review_reward', true)) {
            return;
        }
        $this->hook(Controller::class, [
            ['onApprovedReview', 'site-reviews/review/approved', 20],
            ['onCreatedReview', 'site-reviews/review/created', 20],
        ]);
    }
}
