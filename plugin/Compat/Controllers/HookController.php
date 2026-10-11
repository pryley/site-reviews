<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Review;

class HookController extends AbstractController
{
    /**
     * @filter site-reviews/build/template/review
     */
    public function filterAssignedToTag(string $template): string
    {
        return str_replace('{{ assigned_to }}', '{{ assigned_links }}', $template);
    }

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
     * @filter site-reviews/build/template/review
     */
    public function filterReviewIdTag(string $template): string
    {
        return str_replace('id="{{ review_id }}"', 'id="review-{{ review_id }}"', $template);
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/defaults/review-table-filters
     */
    public function filterReviewTableFilter(array $defaults)
    {
        return apply_filters_deprecated('site-reviews/review-table/filter',
            [$defaults],
            '5.11.0',
            'site-reviews/defaults/review-table-filters'
        );
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/rendered/template/reviews
     */
    public function filterReviewsWrapper(string $html)
    {
        return apply_filters_deprecated('site-reviews/reviews/reviews-wrapper',
            [$html],
            '5.0',
            '',
            'Please use a custom "reviews.php" template instead.'
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
     * @return mixed
     *
     * @filter site-reviews/config/forms/review-form
     */
    public function filterSubmissionFormConfig(array $config)
    {
        return apply_filters_deprecated('site-reviews/config/forms/submission-form',
            [$config],
            '5.0',
            'site-reviews/config/forms/review-form'
        );
    }

    /**
     * @return mixed
     *
     * @filter site-reviews/review-form/fields/order
     */
    public function filterSubmissionFormOrder(array $order)
    {
        return apply_filters_deprecated('site-reviews/submission-form/order',
            [$order],
            '5.0',
            'site-reviews/review-form/fields/order'
        );
    }

    /**
     * @action site-reviews/review/responded
     */
    public function reviewResponse(Review $review, string $response): void
    {
        do_action_deprecated('site-reviews/review/response',
            [$review, $response],
            '5.11.0',
            'site-reviews/review/responded',
            'This hook is documented on the FAQ page.'
        );
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
