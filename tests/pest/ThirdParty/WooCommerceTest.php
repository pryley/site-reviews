<?php

use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\OrderReviewsController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ProductController;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The WooCommerce integration's answers to the two review pipelines that
 * bypass the plugin.
 *
 * The Product Reviews block: its new inner-blocks structure renders
 * WooCommerce's own comment-based reviews UI, so the integration blanks it and
 * lets the Product Details block's hide-empty pass drop the accordion item —
 * the plugin's reviews arrive through that block's compatibility layer. The
 * legacy structure (no inner blocks) routes through comments_template(), which
 * the integration already owns, and must pass through untouched.
 *
 * The Customer Review Request feature (WooCommerce 10.8+): reviews arrive as
 * comments from WooCommerce's own Review Order page. Each one is converted to
 * a plugin review and the comment is marked as imported, exactly as the batch
 * importer would mark it. Comments are real WordPress here; only the half that
 * would need a WooCommerce return value stays untested (the stubs return
 * nothing).
 */

beforeEach(function () {
    resetPluginState();
});

function convertedComment(array $overrides = []): int
{
    $productId = createPost(['post_type' => 'product']);
    $commentId = wp_insert_comment(wp_slash(array_merge([
        'comment_approved' => '1',
        'comment_author' => 'Jane Doe',
        'comment_author_email' => 'jane@example.org',
        'comment_content' => 'Sturdy, arrived early.',
        'comment_post_ID' => $productId,
        'comment_type' => 'review',
    ], $overrides)));
    add_comment_meta($commentId, 'rating', 4, true);

    return $commentId;
}

test('the legacy product reviews block passes through untouched', function () {
    $html = '<div class="wp-block-woocommerce-product-reviews">legacy</div>';

    expect(glsr(ProductController::class)->filterProductReviewsBlock($html, ['innerBlocks' => []]))
        ->toBe($html);
});

test('the inner-blocks product reviews block is blanked', function () {
    $parsed = ['innerBlocks' => [['blockName' => 'woocommerce/product-reviews-title']]];

    expect(glsr(ProductController::class)->filterProductReviewsBlock('<div>native</div>', $parsed))
        ->toBe('');
});

test('the inner-blocks product reviews block survives when the compatibility layer is off', function () {
    add_filter('woocommerce_disable_compatibility_layer', '__return_true');
    $parsed = ['innerBlocks' => [['blockName' => 'woocommerce/product-reviews-title']]];

    expect(glsr(ProductController::class)->filterProductReviewsBlock('<div>native</div>', $parsed))
        ->toBe('<div>native</div>');

    remove_filter('woocommerce_disable_compatibility_layer', '__return_true');
});

test('a submitted review-order comment becomes a review and is marked imported', function () {
    $commentId = convertedComment();
    $comment = get_comment($commentId);

    glsr(OrderReviewsController::class)->convertSubmittedReviews(new \WC_Order(), [
        ['comment_id' => $commentId, 'status' => 'ok'],
    ]);

    $reviews = glsr_get_reviews(['assigned_posts' => $comment->comment_post_ID]);
    expect($reviews->total)->toBe(1);
    expect($reviews->reviews[0]->content)->toBe('Sturdy, arrived early.')
        ->and($reviews->reviews[0]->rating)->toBe(4)
        ->and($reviews->reviews[0]->email)->toBe('jane@example.org');
    expect(get_comment_meta($commentId, 'imported', true))->toBe('1');
});

test('an edited review-order comment is not converted twice', function () {
    $commentId = convertedComment();
    $comment = get_comment($commentId);
    $results = [['comment_id' => $commentId, 'status' => 'ok']];

    glsr(OrderReviewsController::class)->convertSubmittedReviews(new \WC_Order(), $results);
    glsr(OrderReviewsController::class)->convertSubmittedReviews(new \WC_Order(), $results);

    expect(glsr_get_reviews(['assigned_posts' => $comment->comment_post_ID])->total)->toBe(1);
});

test('eligible items pass through when nothing matches a review', function () {
    $items = ['not-an-order-item'];

    expect(glsr(OrderReviewsController::class)->filterEligibleItems($items, new \WC_Order()))
        ->toBe($items);
});
