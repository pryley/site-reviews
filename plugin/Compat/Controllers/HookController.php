<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;

class HookController extends AbstractController
{
    /**
     * The {{ assigned_to }} tag of the review template was renamed in 5.0.
     *
     * @param mixed $template
     *
     * @return mixed
     *
     * @filter site-reviews/build/template/review
     */
    public function filterAssignedToTag($template)
    {
        return str_replace('{{ assigned_to }}', '{{ assigned_links }}', $template);
    }

    /**
     * @param mixed $properties
     *
     * @return mixed
     *
     * @filter site-reviews/rest-api/reviews/schema/properties
     */
    public function filterRestApiReviewsProperties($properties)
    {
        return apply_filters_deprecated('site-reviews/rest-api/reviews/properties',
            [$properties],
            '6.5.0',
            'site-reviews/rest-api/reviews/schema/properties'
        );
    }

    /**
     * @param mixed $properties
     *
     * @return mixed
     *
     * @filter site-reviews/rest-api/summary/schema/properties
     */
    public function filterRestApiSummaryProperties($properties)
    {
        return apply_filters_deprecated('site-reviews/rest-api/summary/properties',
            [$properties],
            '6.5.0',
            'site-reviews/rest-api/summary/schema/properties'
        );
    }

    /**
     * @param mixed $fields
     * @param mixed $args
     *
     * @return mixed
     *
     * @filter site-reviews/review-form/fields/all
     */
    public function filterReviewFormFieldsNormalized($fields, $args)
    {
        return apply_filters_deprecated('site-reviews/review-form/fields/normalized',
            [$fields, $args],
            '7.0',
            'site-reviews/review-form/fields/all'
        );
    }

    /**
     * @param mixed $order
     *
     * @return mixed
     *
     * @filter site-reviews/review-form/fields/order
     */
    public function filterReviewFormOrder($order)
    {
        return apply_filters_deprecated('site-reviews/review-form/order',
            [$order],
            '8.0',
            'site-reviews/review-form/fields/order'
        );
    }

    /**
     * Since 5.3 the {{ review_id }} tag of the review template is the ID alone.
     *
     * @param mixed $template
     *
     * @return mixed
     *
     * @filter site-reviews/build/template/review
     */
    public function filterReviewIdTag($template)
    {
        return str_replace('id="{{ review_id }}"', 'id="review-{{ review_id }}"', $template);
    }

    /**
     * @param mixed $defaults
     *
     * @return mixed
     *
     * @filter site-reviews/defaults/review-table-filters
     */
    public function filterReviewTableFilter($defaults)
    {
        return apply_filters_deprecated('site-reviews/review-table/filter',
            [$defaults],
            '5.11.0',
            'site-reviews/defaults/review-table-filters'
        );
    }

    /**
     * @param mixed $html
     *
     * @return mixed
     *
     * @filter site-reviews/rendered/template/reviews
     */
    public function filterReviewsWrapper($html)
    {
        return apply_filters_deprecated('site-reviews/reviews/reviews-wrapper',
            [$html],
            '5.0',
            '',
            'Please use a custom "reviews.php" template instead.'
        );
    }

    /**
     * @param mixed $notification
     *
     * @return mixed
     *
     * @filter site-reviews/slack/notification
     */
    public function filterSlackCompose($notification)
    {
        return apply_filters_deprecated('site-reviews/slack/compose',
            [$notification],
            '6.9.0',
            'site-reviews/slack/notification'
        );
    }

    /**
     * @param mixed $config
     *
     * @return mixed
     *
     * @filter site-reviews/config/forms/review-form
     */
    public function filterSubmissionFormConfig($config)
    {
        return apply_filters_deprecated('site-reviews/config/forms/submission-form',
            [$config],
            '5.0',
            'site-reviews/config/forms/review-form'
        );
    }

    /**
     * @param mixed $order
     *
     * @return mixed
     *
     * @filter site-reviews/review-form/fields/order
     */
    public function filterSubmissionFormOrder($order)
    {
        return apply_filters_deprecated('site-reviews/submission-form/order',
            [$order],
            '5.0',
            'site-reviews/review-form/fields/order'
        );
    }

    /**
     * @param mixed $review
     * @param mixed $response
     *
     * @action site-reviews/review/responded
     */
    public function reviewResponse($review, $response): void
    {
        do_action_deprecated('site-reviews/review/response',
            [$review, $response],
            '5.11.0',
            'site-reviews/review/responded',
            'This hook is documented on the FAQ page.'
        );
    }

    /**
     * @param mixed $review
     * @param mixed $data
     *
     * @action site-reviews/review/updated
     */
    public function reviewSaved($review, $data): void
    {
        do_action_deprecated('site-reviews/review/saved',
            [$review, $data],
            '6.7.0',
            'site-reviews/review/updated'
        );
    }
}
