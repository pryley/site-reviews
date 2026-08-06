<?php

use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviews;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\definesWpImporting;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Importing WooCommerce's comment-type product reviews.
 *
 * THIS FILE IS IN THE IMPORT SUITE, AND HAS TO BE: ImportProductReviews::handle()
 * defines WP_IMPORTING, which PHP cannot undo — see AttachmentImportTest for the
 * full account of what that poisons.
 *
 * The author test exists because it failed in the wild before it existed: the
 * import SQL aliased the comment's user_id to a key the create pipeline does not
 * recognize, so every imported review had author_id 0 and grew a junk custom
 * field holding the id it should have been created with. The comment_ID column
 * is selected only to key the result set, and must not leak into custom fields
 * the same way.
 *
 * The loyalty integrations stay hooked on purpose: their maybeEarnPoints()
 * returns early under WP_IMPORTING (an import must not mint rewards), and that
 * guard is what stands between this test and a fatal — their stubs return null
 * where the real plugins return a points object. Remove the guard and the
 * author test fails with that fatal, which is the point.
 */

beforeEach(function () {
    definesWpImporting(); // handle() does, and it is why this file is in this suite
    resetPluginState();
});

function importableWooCommerceComment(array $overrides = []): array
{
    $productId = createPost(['post_type' => 'product']);
    $commentId = wp_insert_comment(wp_slash(array_merge([
        'comment_approved' => '1',
        'comment_author' => 'Jane Doe',
        'comment_author_email' => 'jane@example.org',
        'comment_content' => 'Imported content.',
        'comment_post_ID' => $productId,
        'comment_type' => 'review',
    ], $overrides)));
    add_comment_meta($commentId, 'rating', 5, true);

    return [$productId, $commentId];
}

test('an imported review keeps its author', function () {
    $userId = createUser();
    [$productId, $commentId] = importableWooCommerceComment(['user_id' => $userId]);

    $command = new ImportProductReviews(new Request(['page' => 1, 'per_page' => 25]));
    $command->handle();

    $reviews = glsr_get_reviews(['assigned_posts' => $productId]);
    expect($reviews->total)->toBe(1);
    expect($reviews->reviews[0]->author_id)->toBe($userId);
    expect($reviews->reviews[0]->custom()->toArray())->toBe([]);
    expect(get_comment_meta($commentId, 'imported', true))->toBe('1');
});

test('the import response counts from zero without a warning', function () {
    [$productId] = importableWooCommerceComment();

    $command = new ImportProductReviews(new Request(['page' => 1, 'per_page' => 25]));
    $command->handle();

    expect($command->response())->toHaveKey('imported', 1)
        ->toHaveKey('skipped', 0);
});
