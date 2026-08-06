<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\Controller as WooCommerceController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ExperimentsController;
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

/*
 * The gatekeeper on the settings save. WooCommerce is not installed in this
 * environment, which is a BLOCKING-grade error — the strongest possible
 * grounds for refusal — so what these two tests pin is the invariant: a fresh
 * enable attempt is refused, but an integration that is already enabled is
 * NEVER disabled by saving settings, whatever the gatekeeper thinks.
 */

test('enabling the integration without woocommerce is refused', function () {
    glsr(OptionManager::class)->set('settings.integrations.woocommerce.enabled', 'no');
    $settings = ['settings' => ['integrations' => ['woocommerce' => ['enabled' => 'yes']]]];

    $result = glsr(WooCommerceController::class)->filterSettingsCallback($settings, $settings);

    expect($result['settings']['integrations']['woocommerce']['enabled'])->toBe('no');
});

test('saving settings never disables an enabled integration', function () {
    glsr(OptionManager::class)->set('settings.integrations.woocommerce.enabled', 'yes');
    $settings = ['settings' => ['integrations' => ['woocommerce' => ['enabled' => 'yes']]]];

    $result = glsr(WooCommerceController::class)->filterSettingsCallback($settings, $settings);

    expect($result['settings']['integrations']['woocommerce']['enabled'])->toBe('yes');
});

/*
 * The wp_comments experiment: answer faithfully, or leave the query to
 * WordPress. The comment queries below never run against the database — the
 * controller is called directly, as the comments_pre_query filter would call
 * it — so what is asserted is the translation itself.
 */

function wooCommentQuery(array $vars): \WP_Comment_Query
{
    $query = new \WP_Comment_Query();
    $query->query_vars = $vars;
    return $query;
}

function wooProductReview(int $productId, array $overrides = []): \GeminiLabs\SiteReviews\Review
{
    return glsr_create_review(array_merge([
        'assigned_posts' => $productId,
        'content' => 'Sturdy, arrived early.',
        'email' => 'jane@example.org',
        'is_approved' => true,
        'name' => 'Jane Doe',
        'rating' => 5,
    ], $overrides));
}

test('a comment query with no faithful translation is left to wordpress', function () {
    $query = wooCommentQuery([
        'meta_query' => [['key' => '_review_order_id', 'value' => 123]],
        'type' => 'review',
    ]);

    expect(glsr(ExperimentsController::class)->filterProductCommentsQuery(null, $query))->toBeNull();
});

test('a comment query scoped by author email answers with matching reviews', function () {
    $productId = createPost(['post_type' => 'product']);
    wooProductReview($productId);
    wooProductReview($productId, ['email' => 'sam@example.org', 'name' => 'Sam Doe', 'rating' => 3]);

    $comments = glsr(ExperimentsController::class)->filterProductCommentsQuery(null, wooCommentQuery([
        'author_email' => 'sam@example.org',
        'post_id' => $productId,
        'type' => 'review',
    ]));

    expect($comments)->toHaveCount(1);
    expect($comments[0]->comment_author_email)->toBe('sam@example.org');
});

test('a count comment query answers with an integer', function () {
    $productId = createPost(['post_type' => 'product']);
    wooProductReview($productId);
    wooProductReview($productId, ['email' => 'sam@example.org', 'name' => 'Sam Doe']);

    $count = glsr(ExperimentsController::class)->filterProductCommentsQuery(null, wooCommentQuery([
        'count' => true,
        'post_id' => $productId,
        'type' => 'review',
    ]));

    expect($count)->toBe(2);
});

test('an ids comment query answers with review ids', function () {
    $productId = createPost(['post_type' => 'product']);
    $review = wooProductReview($productId);

    $ids = glsr(ExperimentsController::class)->filterProductCommentsQuery(null, wooCommentQuery([
        'fields' => 'ids',
        'post_id' => $productId,
        'type' => 'review',
    ]));

    expect($ids)->toBe([$review->ID]);
});

test('a hold status comment query answers with unapproved reviews only', function () {
    $productId = createPost(['post_type' => 'product']);
    wooProductReview($productId);
    wooProductReview($productId, ['email' => 'sam@example.org', 'is_approved' => false, 'name' => 'Sam Doe']);

    $comments = glsr(ExperimentsController::class)->filterProductCommentsQuery(null, wooCommentQuery([
        'post_id' => $productId,
        'status' => 'hold',
        'type' => 'review',
    ]));

    expect($comments)->toHaveCount(1);
    expect($comments[0]->comment_approved)->toBe('0');
});
