<?php

use GeminiLabs\SiteReviews\Controllers\RevisionController;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\ReviewManager;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Revisions of a review, which WordPress cannot do on its own.
 *
 * A WordPress revision copies the POST: title, content, excerpt. A review is only half a post — its
 * rating, author name, email, pinned and verified flags all live in the plugin's ratings table,
 * which WordPress knows nothing about. So two things go wrong without this controller, both silent:
 *
 *   nothing is SAVED     changing a rating from 5 to 1 leaves title and content identical, so
 *                        WordPress writes no revision at all. The old rating is gone without a trace.
 *   nothing is SHOWN     even with a revision, the compare screen diffs the post — two identical
 *                        columns, the rating change invisible.
 *
 * The controller fixes both by snapshotting the review's own fields into the revision's meta and
 * rewriting the compare screen from it. The rating is drawn as ★★★★★, not "5", because "5" and "1"
 * side by side tell you nothing.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

/**
 * The revision WordPress writes when a post is saved — and the meta the plugin hangs off it.
 */
function revisionOf(int $reviewId): int
{
    $revisionId = wp_save_post_revision($reviewId);

    return (int) $revisionId;
}

/**
 * NOT `(array) $value`. PostMeta::get() returns an empty STRING for a meta key that is not there,
 * and casting '' to an array gives [''] — one element, not none. Which is precisely why the plugin
 * itself checks `is_array()` before touching it in restoreRevision().
 */
function revisionMeta(int $revisionId): array
{
    $meta = glsr(PostMeta::class)->get($revisionId, 'review');

    return is_array($meta) ? $meta : [];
}

/*
 * Saving.
 */

test('a revision of a review remembers what WordPress does not', function () {
    // The rating, the name, the email — none of which is on the post. Without this, a revision of a
    // review is a copy of half of it, and the half that is missing is the half people edit.
    $review = createReview(['content' => 'The room was lovely.', 'name' => 'Jane', 'rating' => 5]);

    $revisionId = revisionOf($review->ID);
    glsr(RevisionController::class)->saveRevision($revisionId);

    $meta = revisionMeta($revisionId);
    expect($meta)->not->toBeEmpty()
        ->and((string) $meta['rating'])->toBe('5')
        ->and($meta['name'])->toBe('Jane');
});

test('a revision of an ordinary post is left alone', function () {
    // _wp_put_post_revision fires for every revision of every post type on the site.
    $postId = createPost();

    $revisionId = revisionOf($postId);
    glsr(RevisionController::class)->saveRevision($revisionId);

    expect(revisionMeta($revisionId))->toBeEmpty();
});

/*
 * Noticing that something changed.
 */

test('a rating that changed counts as a change, even when the post did not', function () {
    // THE assertion of this file. WordPress compares the post's title, content and excerpt; a
    // review whose rating went from 5 to 1 has an IDENTICAL post, so WordPress writes no revision
    // and the old rating is lost with no trace. This is what tells it otherwise.
    $review = createReview(['content' => 'Unchanged.', 'rating' => 5]);
    $revisionId = revisionOf($review->ID);
    glsr(RevisionController::class)->saveRevision($revisionId);

    glsr(ReviewManager::class)->updateRating($review->ID, ['rating' => 1]);

    $hasChanged = glsr(RevisionController::class)->filterReviewHasChanged(
        false, // WordPress says nothing changed…
        get_post($revisionId),
        get_post($review->ID)
    );

    expect($hasChanged)->toBeTrue(); // …and the plugin says otherwise
});

test('a review that really is unchanged is not saved again', function () {
    // The other direction, and it matters: a revision written on every save with nothing in it
    // fills the database with copies of the same review, and buries the changes that were real.
    $review = createReview(['rating' => 5]);
    $revisionId = revisionOf($review->ID);
    glsr(RevisionController::class)->saveRevision($revisionId);

    $hasChanged = glsr(RevisionController::class)->filterReviewHasChanged(
        false,
        get_post($revisionId),
        get_post($review->ID)
    );

    expect($hasChanged)->toBeFalse();
});

test('somebody else\'s post is judged by WordPress\'s own answer, not the plugin\'s', function () {
    $postId = createPost();
    $revisionId = revisionOf($postId);

    expect(glsr(RevisionController::class)->filterReviewHasChanged(
        true, get_post($revisionId), get_post($postId)
    ))->toBeTrue();

    expect(glsr(RevisionController::class)->filterReviewHasChanged(
        false, get_post($revisionId), get_post($postId)
    ))->toBeFalse();
});

test('a review is always checked for changes, whatever the site has decided', function () {
    // wp_save_post_revision_check_for_changes can be switched off — by a theme, or by a plugin that
    // wants a revision on every save. For a review the check must happen, because the check is the
    // only thing that ever asks the plugin whether the rating moved.
    $review = createReview();
    $revisionId = revisionOf($review->ID);

    expect(glsr(RevisionController::class)->filterCheckForChanges(
        false, get_post($revisionId), get_post($review->ID)
    ))->toBeTrue();
});

test('and an ordinary post keeps whatever the site decided', function () {
    $postId = createPost();
    $revisionId = revisionOf($postId);

    expect(glsr(RevisionController::class)->filterCheckForChanges(
        false, get_post($revisionId), get_post($postId)
    ))->toBeFalse();
});

/*
 * Restoring.
 */

test('restoring a revision puts the rating back, not just the words', function () {
    // What the whole feature is for. Somebody changes a 5-star review to 1 star, and the site owner
    // restores it — and if only the post is restored, the review comes back with its original words
    // and its vandalised rating.
    $review = createReview(['content' => 'The room was lovely.', 'rating' => 5]);
    $revisionId = revisionOf($review->ID);
    glsr(RevisionController::class)->saveRevision($revisionId);

    glsr(ReviewManager::class)->updateRating($review->ID, ['rating' => 1]);
    expect(glsr_get_review($review->ID)->rating)->toBe(1);

    glsr(RevisionController::class)->restoreRevision($review->ID, $revisionId);

    expect(glsr_get_review($review->ID)->rating)->toBe(5);
});

test('restoring a revision of an ordinary post touches nothing', function () {
    $postId = createPost();
    $revisionId = revisionOf($postId);
    // "Touches nothing" made checkable: a restore that DID act would update the rating and
    // announce a cache flush for the review — so listen for it.
    $flushed = false;
    add_action('site-reviews/cache/flush', function () use (&$flushed) {
        $flushed = true;
    });

    glsr(RevisionController::class)->restoreRevision($postId, $revisionId);

    expect($flushed)->toBeFalse(); // it ran, and did nothing
    expect(get_post($postId))->not->toBeNull();
});

/*
 * The compare screen.
 */

test('the compare screen shows a rating change as stars, not as a number', function () {
    // WordPress's own diff shows two identical columns, because the post did not change — so these
    // rows are the only place a rating change is visible to a human being at all. And "5" against
    // "1" in a diff tells nobody anything, which is why the number is redrawn as ★★★★★ / ★☆☆☆☆.
    //
    // BOTH sides are revisions. The compare screen never diffs a revision against the live post —
    // and the guard reads $compareTo->post_parent, which is 0 on the review itself and the review's
    // id on a revision of it. So the fixture is two snapshots: 5 stars, then 1.
    $review = createReview(['content' => 'The room was lovely.', 'rating' => 5]);
    $fiveStars = revisionOf($review->ID);
    glsr(RevisionController::class)->saveRevision($fiveStars);

    glsr(ReviewManager::class)->updateRating($review->ID, ['rating' => 1]);

    // wp_update_post() writes a revision ITSELF, on save_post. Calling wp_save_post_revision()
    // again afterwards finds nothing new to save and returns null — so the revision to compare
    // against is the one WordPress has just made, not one asked for a second time.
    wp_update_post(['ID' => $review->ID, 'post_content' => 'The room was awful.']);
    $oneStar = (int) array_key_first(wp_get_post_revisions($review->ID)); // newest first
    glsr(RevisionController::class)->saveRevision($oneStar);

    $diff = glsr(RevisionController::class)->filterRevisionUiDiff(
        [], get_post($fiveStars), get_post($oneStar)
    );

    $rating = current(array_filter($diff, fn ($row) => 'rating' === $row['id']));

    expect($rating)->not->toBeFalse()
        ->and($rating['diff'])->toContain('★')
        ->and($rating['name'])->not->toBeEmpty();
});

test('comparing against nothing at all does not fill the log with deprecations', function () {
    // WordPress passes FALSE for compareFrom when there is nothing on the left-hand side, and
    // reviewFromRevision() answers with an empty Review — every field of which is null.
    // wp_text_diff() hands its arguments to normalize_whitespace(), and trim(null) is deprecated on
    // PHP 8.1, so this screen used to emit a notice per field per comparison.
    $review = createReview(['rating' => 5]);
    $revisionId = revisionOf($review->ID);
    glsr(RevisionController::class)->saveRevision($revisionId);

    $diff = glsr(RevisionController::class)->filterRevisionUiDiff(
        [], false, get_post($revisionId) // false: there is nothing on the left-hand side
    );

    expect($diff)->toBeArray();
});

test('a diff for somebody else\'s post is handed straight back', function () {
    // wp_get_revision_ui_diff fires on the compare screen of every post type there is.
    $postId = createPost();
    $revisionId = revisionOf($postId);

    $diff = glsr(RevisionController::class)->filterRevisionUiDiff(
        ['untouched'], get_post($revisionId), get_post($revisionId)
    );

    expect($diff)->toBe(['untouched']);
});
