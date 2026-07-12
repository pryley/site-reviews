<?php

use GeminiLabs\SiteReviews\Controllers\ToolsController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Role;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The repair tools, driven through the controller.
 *
 * These are reached in production as `site-reviews/route/admin/{action}` — the
 * hook Router::post() fires from a routed admin POST — and each handler simply
 * executes its command (AbstractController::execute). The non-ajax handlers are
 * used here because the ajax ones end in wp_send_json(), which dies.
 *
 * Each is asserted on the damage it repairs, not on its own success flag:
 * break the state, run the tool, prove the state is fixed.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

test('repairs the review capabilities of a role', function () {
    // RepairPermissions -> Role::resetAll() -> addCapabilities() for every role in
    // Role::roles(). The editor role is granted the review capabilities on install.
    $capability = 'edit_others_site-reviews';
    expect(get_role('editor')->has_cap($capability))->toBeTrue();

    get_role('editor')->remove_cap($capability);
    expect(get_role('editor')->has_cap($capability))->toBeFalse();

    glsr(ToolsController::class)->repairPermissions(new Request());

    expect(get_role('editor')->has_cap($capability))->toBeTrue();
});

test('refuses to repair permissions without the edit_users capability', function () {
    // RepairPermissions::handle() gates on glsr()->can('edit_users').
    $capability = 'edit_others_site-reviews';
    get_role('editor')->remove_cap($capability);
    wp_set_current_user(createUser(['role' => 'editor'])); // an editor cannot edit_users

    glsr(ToolsController::class)->repairPermissions(new Request());

    expect(get_role('editor')->has_cap($capability))->toBeFalse(); // not repaired
});

test('removes rating rows that no longer belong to a review', function () {
    // RepairReviewRelations -> TableRatings::removeInvalidRows(), which deletes every
    // rating row whose review_id is not a post of the review post type.
    $review = createReview();
    $postId = createPost(); // an ordinary post, not a review
    glsr(Database::class)->insert('ratings', [
        'review_id' => $postId, // the foreign key is satisfied: the post exists
        'rating' => 5,
    ]);
    $table = glsr(Tables::class)->table('ratings');
    $countRow = fn (int $id) => (int) glsr(Database::class)->dbGetVar(
        "SELECT COUNT(*) FROM {$table} WHERE review_id = {$id}"
    );
    expect($countRow($postId))->toBe(1);

    glsr(ToolsController::class)->repairReviewRelations();

    expect($countRow($postId))->toBe(0);        // the orphan is gone
    expect($countRow($review->ID))->toBe(1);    // the real review is untouched
});

/*
 * The counts are recalculated with raw SQL, so the assertions below deliberately
 * read them back through get_*_meta() WITHOUT clearing any cache: that is what a
 * plugin, a theme or the next request would do, and it is exactly what used to
 * return the stale value.
 */

test('recalculates the rating counts assigned to a post', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 5]);
    expect(get_post_meta($postId, CountManager::META_REVIEWS, true))->toBe('1');

    delete_post_meta($postId, CountManager::META_REVIEWS);
    delete_post_meta($postId, CountManager::META_AVERAGE);
    expect(get_post_meta($postId, CountManager::META_REVIEWS, true))->toBeEmpty();

    glsr(ToolsController::class)->resetAssignedMeta();

    expect(get_post_meta($postId, CountManager::META_REVIEWS, true))->toBe('1');
    expect((float) get_post_meta($postId, CountManager::META_AVERAGE, true))->toBe(5.0);
});

test('recalculates the rating counts assigned to a category', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    createReview(['assigned_terms' => $termId, 'rating' => 4]);
    expect(get_term_meta($termId, CountManager::META_REVIEWS, true))->toBe('1');

    delete_term_meta($termId, CountManager::META_REVIEWS);
    expect(get_term_meta($termId, CountManager::META_REVIEWS, true))->toBeEmpty();

    glsr(ToolsController::class)->resetAssignedMeta();

    expect(get_term_meta($termId, CountManager::META_REVIEWS, true))->toBe('1');
    expect((float) get_term_meta($termId, CountManager::META_AVERAGE, true))->toBe(4.0);
});

test('recalculates the rating counts assigned to a user', function () {
    $userId = createUser();
    createReview(['assigned_users' => $userId, 'rating' => 3]);
    expect(get_user_meta($userId, CountManager::META_REVIEWS, true))->toBe('1');

    delete_user_meta($userId, CountManager::META_REVIEWS);
    expect(get_user_meta($userId, CountManager::META_REVIEWS, true))->toBeEmpty();

    glsr(ToolsController::class)->resetAssignedMeta();

    expect(get_user_meta($userId, CountManager::META_REVIEWS, true))->toBe('1');
    expect((float) get_user_meta($userId, CountManager::META_AVERAGE, true))->toBe(3.0);
});
