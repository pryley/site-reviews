<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\Vectorface\Whip\Whip;

class HookController extends AbstractController
{
    /**
     * @action wp_enqueue_scripts
     */
    public function filterOptimizeAsset(): void
    {
        foreach (['css', 'js'] as $type) {
            apply_filters_deprecated("site-reviews/optimize/{$type}", [false], '8.4.0', '',
                'Site Reviews no longer combines assets into one file.'
            );
        }
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/rest-api/reviews/schema/properties
     */
    public function filterRestApiReviewsProperties(array $properties)
    {
        return apply_filters_deprecated('site-reviews/rest-api/reviews/properties',
            [$properties],
            '6.5.0',
            'site-reviews/rest-api/reviews/schema/properties'
        );
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/rest-api/summary/schema/properties
     */
    public function filterRestApiSummaryProperties(array $properties)
    {
        return apply_filters_deprecated('site-reviews/rest-api/summary/properties',
            [$properties],
            '6.5.0',
            'site-reviews/rest-api/summary/schema/properties'
        );
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/review-form/fields/all
     */
    public function filterReviewFormFieldsNormalized(array $fields, Form $form)
    {
        return apply_filters_deprecated('site-reviews/review-form/fields/normalized',
            [$fields, $form],
            '7.0',
            'site-reviews/review-form/fields/all'
        );
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/review-form/fields/order
     */
    public function filterReviewFormOrder(array $order)
    {
        return apply_filters_deprecated('site-reviews/review-form/order',
            [$order],
            '8.0',
            'site-reviews/review-form/fields/order'
        );
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/slack/notification
     */
    public function filterSlackCompose(array $notification)
    {
        return apply_filters_deprecated('site-reviews/slack/compose',
            [$notification],
            '6.9.0',
            'site-reviews/slack/notification'
        );
    }

    /**
     * @filter site-reviews/whip/whitelist
     */
    public function filterWhipWhitelist(array $whitelist): array
    {
        $message = 'Use the Configure IP Address Detection tool to select the proxy HTTP header and enter its trusted proxies.';
        apply_filters_deprecated('site-reviews/whip/methods', [Whip::ALL_METHODS], '7.0.6', '', $message);
        apply_filters_deprecated('site-reviews/whip/whitelist/cloudflare', [false], '7.0.6', '', $message);
        return $whitelist;
    }

    /**
     * @action site-reviews/review/updated
     */
    public function reviewSaved(Review $review, array $data): void
    {
        do_action_deprecated('site-reviews/review/saved',
            [$review, $data],
            '6.7.0',
            'site-reviews/review/updated'
        );
    }
}
