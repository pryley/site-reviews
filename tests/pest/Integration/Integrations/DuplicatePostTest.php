<?php

use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\PostMeta;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Yoast Duplicate Post integration.
 *
 * It registers on every site — there is no isInstalled() — because all it does is
 * hook Duplicate Post's own filters, which simply never fire when the plugin is
 * absent. That is what makes it testable without a stub: the filters can be fired
 * here in its place, with the arguments Duplicate Post passes.
 *
 * The one thing Duplicate Post does that Site Reviews cannot let stand is copying
 * a review as a plain post: a review's rating lives in a custom table, not in post
 * meta, so a copied post would be a review with no rating. duplicateReview() puts
 * the row back.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

/**
 * What Duplicate Post leaves behind, and the state duplicate_post_post_copy fires
 * in: a copy of the POST — content, title, type — with nothing behind it in the
 * ratings table, because Duplicate Post does not know that table is there.
 */
function copiedReviewPost(int $originalId): int
{
    $original = get_post($originalId);

    return (int) wp_insert_post([
        'post_content' => $original->post_content,
        'post_status' => 'draft',
        'post_title' => $original->post_title,
        'post_type' => $original->post_type,
    ], true);
}

test('a copied review gets its review row back', function () {
    $review = createReview(['rating' => 4, 'title' => 'The original']);
    $copyId = copiedReviewPost($review->ID);

    // Duplicate Post has copied the post but knows nothing about the ratings table.
    expect(glsr_get_review($copyId)->isValid())->toBeFalse();

    do_action('duplicate_post_post_copy', $copyId, get_post($review->ID));

    $copy = glsr_get_review($copyId);
    expect($copy->isValid())->toBeTrue()
        ->and($copy->rating)->toBe(4)
        ->and($copy->content)->toBe($review->content);
});

test('a copied review remembers what it was copied from', function () {
    $review = createReview();
    $copyId = copiedReviewPost($review->ID);

    do_action('duplicate_post_post_copy', $copyId, get_post($review->ID));

    expect(glsr(PostMeta::class)->get($copyId, 'duplicated_from'))->toBe((string) $review->ID);
});

test('a copied review is not a submission', function () {
    // submitted and submitted_hash are the record of a real form submission. The
    // copy was made in the admin, so carrying them over would wrongly make the
    // duplicate look like something a visitor sent.
    $review = createReview();
    $copyId = copiedReviewPost($review->ID);
    glsr(PostMeta::class)->set($copyId, 'submitted', ['some' => 'data']);
    glsr(PostMeta::class)->set($copyId, 'submitted_hash', 'abc123');

    do_action('duplicate_post_post_copy', $copyId, get_post($review->ID));

    expect(glsr(PostMeta::class)->exists($copyId, 'submitted'))->toBeFalse()
        ->and(glsr(PostMeta::class)->exists($copyId, 'submitted_hash'))->toBeFalse();
});

test('an ordinary post being copied is left alone', function () {
    $postId = createPost();
    $copyId = createPost();

    do_action('duplicate_post_post_copy', $copyId, get_post($postId));

    expect(glsr_get_review($copyId)->isValid())->toBeFalse()
        ->and(glsr(PostMeta::class)->exists($copyId, 'duplicated_from'))->toBeFalse();
});

test('a failed copy creates nothing', function () {
    // Duplicate Post hands over a WP_Error instead of an ID when the copy fails.
    // Without the is_wp_error() guard that error would be cast to an int and used
    // as a post ID — and HookProxy would swallow whatever went wrong — so the
    // assertion is on the review posts, not on a return value.
    $review = createReview();
    $reviewIds = fn (): array => get_posts([
        'fields' => 'ids',
        'numberposts' => -1,
        'post_status' => 'any',
        'post_type' => glsr()->post_type,
    ]);
    $before = $reviewIds();

    do_action('duplicate_post_post_copy', new WP_Error('failed'), get_post($review->ID));

    expect($reviewIds())->toBe($before);
});

test('the review count meta is never copied', function () {
    // _glsr_reviews and friends are the aggregate counts of the page a review is
    // assigned to. They belong to that page, not to the review, and a copy that
    // carried them would report a count nobody has.
    $metaKeys = apply_filters('duplicate_post_excludelist_filter', ['_edit_lock']);

    expect($metaKeys)->toBe([
        '_edit_lock',
        CountManager::META_AVERAGE,
        CountManager::META_RANKING,
        CountManager::META_REVIEWS,
    ]);
});

test('rewrite and republish is not offered for reviews', function () {
    // Duplicate Post's "Rewrite & Republish" makes a copy, has you edit it, then
    // replaces the original with it — a workflow for pages, not for what a visitor
    // wrote. It is removed from the bulk actions, from the row actions, and from
    // the editor.
    $actions = apply_filters('bulk_actions-edit-'.glsr()->post_type, [
        'edit' => 'Edit',
        'duplicate_post_bulk_rewrite_republish' => 'Rewrite & Republish',
    ]);

    expect($actions)->toBe(['edit' => 'Edit']);
});

test('the rewrite row action is removed from a review but not from a post', function () {
    // post_row_actions is a shared filter and the plugin's own list table adds to
    // it as well (Approve, Unapprove, Respond…), so the assertion is on the key
    // this integration owns, not on the whole array.
    $review = createReview();
    $rowActions = ['edit' => 'Edit', 'rewrite' => 'Rewrite & Republish'];

    $forReview = apply_filters('post_row_actions', $rowActions, get_post($review->ID));
    expect($forReview)->not->toHaveKey('rewrite')
        ->and($forReview)->toHaveKey('edit');

    $forPost = apply_filters('post_row_actions', $rowActions, get_post(createPost()));
    expect($forPost)->toHaveKey('rewrite');
});

/**
 * A stand-in for Duplicate Post's own editor button callback. Only its NAME
 * matters: removeRewriteEditorLink() finds the callback by matching the end of the
 * key WordPress builds for it in $wp_filter, which for a static method is
 * "Class::method". Nothing about the body is asserted.
 */
class DuplicatePostStandIn
{
    public static function add_rewrite_and_republish_post_button(): void
    {
    }
}

test('the rewrite editor button is unhooked on a review', function () {
    $review = createReview();
    $callback = [DuplicatePostStandIn::class, 'add_rewrite_and_republish_post_button'];
    add_action('post_submitbox_start', $callback, 10);
    expect(has_action('post_submitbox_start', $callback))->toBe(10);

    do_action('post_submitbox_start', get_post($review->ID));

    expect(has_action('post_submitbox_start', $callback))->toBeFalse();
});

test('the rewrite editor button is left alone on a post', function () {
    $callback = [DuplicatePostStandIn::class, 'add_rewrite_and_republish_post_button'];
    add_action('post_submitbox_start', $callback, 10);

    do_action('post_submitbox_start', get_post(createPost()));

    expect(has_action('post_submitbox_start', $callback))->toBe(10);
});
