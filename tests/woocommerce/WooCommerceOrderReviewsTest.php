<?php

use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\OrderReviewsController;

use function GeminiLabs\SiteReviews\Tests\enableWooCommerceIntegration;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\wooCompletedOrder;
use function GeminiLabs\SiteReviews\Tests\wooProduct;

/*
 * Converting a review-order comment with a real WooCommerce. CreateReview resets the date and type
 * of a form submission, and the conversion counts as one, so the review has to keep the comment's date.
 */

beforeEach(function () {
    resetPluginState();
    enableWooCommerceIntegration();
});

test('a converted review-order comment keeps its date and is a local review', function () {
    $product = wooProduct();
    $commentId = wp_insert_comment([
        'comment_approved' => '1',
        'comment_author' => 'Jane Doe',
        'comment_author_email' => 'jane@example.org',
        'comment_content' => 'Sturdy, arrived early.',
        'comment_date' => '2024-02-03 04:05:06',
        'comment_date_gmt' => '2024-02-03 04:05:06',
        'comment_post_ID' => $product->get_id(),
        'comment_type' => 'review',
    ]);
    add_comment_meta($commentId, 'rating', 4, true);

    glsr(OrderReviewsController::class)->convertSubmittedReviews(
        wooCompletedOrder($product, 'jane@example.org'),
        [['comment_id' => $commentId, 'status' => 'ok']]
    );

    $review = glsr_get_reviews(['assigned_posts' => $product->get_id()])->reviews[0];
    expect($review->rating)->toBe(4)
        ->and($review->type)->toBe('local')
        ->and(get_post_field('post_date', $review->ID))->toBe('2024-02-03 04:05:06')
        ->and(get_post_field('post_date_gmt', $review->ID))->toBe('2024-02-03 04:05:06');
});
