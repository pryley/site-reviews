<?php

namespace GeminiLabs\SiteReviews\Integrations\LPFW;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!function_exists('LPFW') || !class_exists('LPFW')) {
            return;
        }
        if ('yes' !== get_option(\LPFW()->Plugin_Constants->EARN_ACTION_PRODUCT_REVIEW, 'yes')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterEarnPointTypes', 'lpfw_get_point_earn_source_types'],
            ['onApprovedReview', 'site-reviews/review/approved', 20],
            ['onCreatedReview', 'site-reviews/review/created', 20],
        ]);
    }
}
