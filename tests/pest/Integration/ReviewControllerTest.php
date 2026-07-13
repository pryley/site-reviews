<?php

use GeminiLabs\SiteReviews\Controllers\ReviewController;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Review;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The review itself, as WordPress moves it around.
 *
 * Almost nothing here is called by the plugin. These are WordPress's own hooks —
 * transition_post_status, set_object_terms, post_updated, deleted_post — and the
 * controller's job is to keep the plugin's four custom tables (ratings,
 * assigned_posts, assigned_terms, assigned_users) in step with a post that WordPress
 * is editing, approving, trashing or deleting behind its back.
 *
 * So most of these tests do the WordPress thing — wp_update_post(), wp_delete_user()
 * — and then look at the custom tables, rather than calling the controller. The hooks
 * are registered at boot and Pest.php restores them for every test, so they fire.
 *
 * Two entry points cannot be reached from a CLI process at all, and one is only half
 * reachable. All three are for the same reason, and it is the same one as the GET
 * routes: filter_input(INPUT_GET|INPUT_POST) reads the SAPI's own copy of the
 * request, which does not exist here and cannot be planted.
 *
 *   onApprove() / onUnapprove()  gated on filter_input(INPUT_GET, 'plugin'), so the
 *                                body never runs. Only "it does nothing" is testable.
 *   updateReview()               reads the review's own fields through
 *                                Helper::filterInputArray(), which DOES fall back to
 *                                $_POST — but the assigned post/user ids through a
 *                                bare filter_input(), which does not. So editing a
 *                                review here always looks like "the assignments were
 *                                cleared". Noted at the test that cares.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    // onEditReview() branches on the current screen, and post_updated fires in every
    // test in this file. A screen left behind by another file would put half of them
    // through bulkUpdateReview().
    set_current_screen('front');
});

afterEach(fn () => set_current_screen('front'));

/*
 * The diff. Everything that assigns or unassigns anything goes through it, and it is
 * the difference between "add this page" and "replace every page with this one".
 */

function assignedDiff(array $existing, array $replacements): array
{
    return protectedMethod(ReviewController::class, 'getAssignedDiffs')
        ->invoke(glsr(ReviewController::class), $existing, $replacements);
}

test('what a review is already assigned to is left alone', function () {
    // The whole point. Without this, saving a review that is assigned to three pages
    // would unassign all three and reassign all three — three deletes, three inserts,
    // and three cache flushes, for no change at all.
    expect(assignedDiff([1, 2, 3], [1, 2, 3]))->toBe(['new' => [], 'old' => []]);
});

test('the order they arrive in is not a change', function () {
    // A token field hands them back in whatever order they were dragged into. Both
    // lists are sorted before they are compared, so that is not an edit.
    expect(assignedDiff([3, 1, 2], [1, 2, 3]))->toBe(['new' => [], 'old' => []]);
});

test('only what was added is added, and only what was removed is removed', function () {
    $diff = assignedDiff([1, 2, 3], [2, 3, 4]);

    expect(array_values($diff['new']))->toBe([4]);
    expect(array_values($diff['old']))->toBe([1]);
});

test('assigning to nothing unassigns everything', function () {
    $diff = assignedDiff([1, 2], []);

    expect(array_values($diff['old']))->toBe([1, 2]);
    expect($diff['new'])->toBe([]);
});

/*
 * The assignments, driven the way the plugin drives them.
 */

test('changing the assigned pages assigns the new ones and unassigns the old', function () {
    $oldPost = createPost();
    $newPost = createPost();
    $review = createReview(['assigned_posts' => $oldPost]);
    expect($review->assigned_posts)->toBe([$oldPost]);

    glsr()->action('review/updated/post_ids', $review, [$newPost]);

    expect(glsr_get_review($review->ID)->assigned_posts)->toBe([$newPost]);
});

test('changing the assigned users assigns the new ones and unassigns the old', function () {
    $oldUser = createUser();
    $newUser = createUser();
    $review = createReview(['assigned_users' => $oldUser]);

    glsr()->action('review/updated/user_ids', $review, [$newUser]);

    expect(glsr_get_review($review->ID)->assigned_users)->toBe([$newUser]);
});

test('a category added in wordpress\'s own box is assigned in the plugin\'s table', function () {
    // set_object_terms is WordPress's hook, not the plugin's — a category can be added
    // from the Quick Edit, from the metabox, from wp_set_object_terms() in somebody
    // else's code. The assigned_terms table has to follow all of them.
    //
    // READ THE OPEN QUESTION BEFORE TRUSTING THIS TEST. It passes because wp_terms and
    // wp_term_taxonomy are still in step on this database, so a term's term_id and its
    // term_taxonomy_id are the same number. They are not the same thing, and the
    // controller is handed the second and stores it as the first — see the finding
    // reported alongside this batch. On a database where the two have drifted apart,
    // this asserts something different and the plugin stores the wrong id.
    $review = createReview();
    $term = createTerm(['taxonomy' => glsr()->taxonomy]);

    wp_set_object_terms($review->ID, [$term], glsr()->taxonomy);

    expect(glsr_get_review($review->ID)->assigned_terms)->toBe([$term]);

    wp_set_object_terms($review->ID, [], glsr()->taxonomy);

    expect(glsr_get_review($review->ID)->assigned_terms)->toBe([]);
});

/*
 * The status. A review's approval is a post status to WordPress and a column in the
 * ratings table to the plugin, and they have to agree — the ratings table is what the
 * shortcodes read.
 */

test('approving a review in wordpress approves it in the ratings table', function () {
    $review = createReview(['is_approved' => false]);
    expect($review->is_approved)->toBeFalse();

    $approved = new ArrayObject();
    add_action('site-reviews/review/approved', fn ($review) => $approved->append($review->ID));

    wp_update_post(['ID' => $review->ID, 'post_status' => 'publish']);

    expect(glsr_get_review($review->ID)->is_approved)->toBeTrue();
    expect($approved->getArrayCopy())->toBe([$review->ID]);
});

test('unapproving a review in wordpress unapproves it in the ratings table', function () {
    $review = createReview(['is_approved' => true]);

    $unapproved = new ArrayObject();
    add_action('site-reviews/review/unapproved', fn ($review) => $unapproved->append($review->ID));

    wp_update_post(['ID' => $review->ID, 'post_status' => 'pending']);

    expect(glsr_get_review($review->ID)->is_approved)->toBeFalse();
    expect($unapproved->getArrayCopy())->toBe([$review->ID]);
});

test('trashing a review is announced, and it stops counting as approved', function () {
    $review = createReview();

    $trashed = new ArrayObject();
    add_action('site-reviews/review/trashed', fn ($review) => $trashed->append($review->ID));

    wp_trash_post($review->ID);

    expect($trashed->getArrayCopy())->toBe([$review->ID]);
    expect(glsr_get_review($review->ID)->is_approved)->toBeFalse();
});

test('every status change is announced, whatever it was', function () {
    // review/transitioned is the general one an addon hangs off; the three above are
    // the shorthands.
    $review = createReview(['is_approved' => false]);
    $transitions = new ArrayObject();
    add_action('site-reviews/review/transitioned', fn ($r, $new, $old) => $transitions->append("{$old}>{$new}"), 10, 3);

    wp_update_post(['ID' => $review->ID, 'post_status' => 'publish']);
    wp_update_post(['ID' => $review->ID, 'post_status' => 'pending']);

    expect($transitions->getArrayCopy())->toBe(['pending>publish', 'publish>pending']);
});

test('a status change that is not a change is not announced', function () {
    // transition_post_status fires on every save, whether the status moved or not, and
    // on the very first one with an old status of "new". Neither is a transition.
    $review = createReview();
    $transitions = new ArrayObject();
    add_action('site-reviews/review/transitioned', fn () => $transitions->append(1));

    wp_update_post(['ID' => $review->ID, 'post_title' => 'A new title']); // status untouched

    expect($transitions)->toHaveCount(0);
});

test('an ordinary post changing status is not mistaken for a review', function () {
    $postId = createPost();
    $review = createReview(['assigned_posts' => $postId]);
    $transitions = new ArrayObject();
    add_action('site-reviews/review/transitioned', fn () => $transitions->append(1));

    wp_update_post(['ID' => $postId, 'post_status' => 'draft']);

    expect($transitions)->toHaveCount(0);
    expect(glsr_get_review($review->ID)->isValid())->toBeTrue(); // and the review is untouched
});

/*
 * Deletion.
 *
 * WordPress will delete a post or a user without asking the plugin. On InnoDB the
 * assigned_posts, assigned_users and ratings rows have ON DELETE CASCADE foreign keys, so
 * the DATABASE tidies itself up — but it cannot touch the object cache, and the reviews
 * are cached with no expiry (Query::review -> Cache::store). On a site with a persistent
 * object cache a deleted review would sit in Redis for ever, and so would the stale
 * assigned-page list of every review that pointed at a deleted page.
 *
 * So the four deletion hooks are registered on EVERY engine now, and the affected review
 * ids are captured on `before_delete_post` / `delete_user` — because by the time
 * `deleted_post` fires, the cascade has removed the very rows the lookup would join
 * against, and the query comes back empty.
 *
 * These tests therefore assert on BOTH: the table (which the cascade clears) and the
 * cached Review object (which only the plugin can clear).
 */

/**
 * @return int[] straight out of glsr_assigned_posts, no cache in the way
 */
function assignedPostIds(int $reviewId): array
{
    global $wpdb;
    $ratingId = glsr_get_review($reviewId)->rating_id;

    return array_map('intval', $wpdb->get_col($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->prefix}glsr_assigned_posts WHERE rating_id = %d", $ratingId
    )));
}

/**
 * @return int[] straight out of glsr_assigned_users
 */
function assignedUserIds(int $reviewId): array
{
    global $wpdb;
    $ratingId = glsr_get_review($reviewId)->rating_id;

    return array_map('intval', $wpdb->get_col($wpdb->prepare(
        "SELECT user_id FROM {$wpdb->prefix}glsr_assigned_users WHERE rating_id = %d", $ratingId
    )));
}

test('the deletion hooks are registered whatever the storage engine', function () {
    // They used to be registered only on MyISAM, on the reasoning that InnoDB's cascade
    // does the work. It does — the rows go. The CACHE does not, and nothing else purged
    // it. That is the whole of the bug these tests are here for.
    $controller = glsr(ReviewController::class);

    foreach ([
        'before_delete_post' => 'onBeforeDeletePost',
        'deleted_post' => 'onDeletePost',
        'delete_user' => 'onBeforeDeleteUser',
        'deleted_user' => 'onDeleteUser',
    ] as $hook => $method) {
        expect(has_action($hook))->not->toBeFalse("nothing is registered on {$hook}");
        expect(method_exists($controller, $method))->toBeTrue();
    }
});

test('deleting a review deletes its rating, and takes it out of the cache', function () {
    $review = createReview();
    expect($review->rating_id)->toBeGreaterThan(0);
    expect(glsr_get_review($review->ID)->isValid())->toBeTrue(); // and it is cached now

    wp_delete_post($review->ID, true);

    expect(glsr_get_review($review->ID)->isValid())->toBeFalse();
});

test('deleting an assigned page unassigns it from every review that had it', function () {
    $postId = createPost();
    $one = createReview(['assigned_posts' => $postId]);
    $two = createReview(['assigned_posts' => $postId]);
    expect(assignedPostIds($one->ID))->toBe([$postId]);
    expect(glsr_get_review($two->ID)->assigned_posts)->toBe([$postId]); // cache them both

    wp_delete_post($postId, true);

    // the cascade cleared the table…
    expect(assignedPostIds($one->ID))->toBe([])
        ->and(assignedPostIds($two->ID))->toBe([]);
    // …and the plugin cleared the cache, which is the half the cascade cannot do
    expect(glsr_get_review($one->ID)->assigned_posts)->toBe([])
        ->and(glsr_get_review($two->ID)->assigned_posts)->toBe([]);
    expect(glsr_get_review($one->ID)->isValid())->toBeTrue(); // the reviews themselves survive
});

test('deleting an assigned user unassigns them from every review that had them', function () {
    $userId = createUser();
    $review = createReview(['assigned_users' => $userId]);
    expect(glsr_get_review($review->ID)->assigned_users)->toBe([$userId]);

    wp_delete_user($userId);

    expect(assignedUserIds($review->ID))->toBe([]);
    expect(glsr_get_review($review->ID)->assigned_users)->toBe([]);
});

test('deleting an ordinary post that no review is about costs one query and nothing else', function () {
    // before_delete_post fires for every post on the site, and for every REVISION of every
    // post — deleting one page deletes all of its revisions, each firing the hook. A
    // revision cannot be assigned anything, so it is skipped rather than looked up.
    $postId = createPost();

    glsr(ReviewController::class)->onBeforeDeletePost($postId, get_post($postId));
    wp_delete_post($postId, true);

    expect(get_post($postId))->toBeNull();
});

/*
 * The filters.
 */

test('a review assigned to several things is found by any of them, or by all of them', function () {
    // `strict` means a review has to be assigned to everything the shortcode names;
    // `loose` means any one of them is enough. It is one setting and it changes the
    // operator in the WHERE clause.
    $operator = fn () => glsr(ReviewController::class)->filterSqlClauseOperator('AND');

    glsr(OptionManager::class)->set('settings.reviews.assignment', 'loose');
    expect($operator())->toBe('OR');

    glsr(OptionManager::class)->set('settings.reviews.assignment', 'strict');
    expect($operator())->toBe('AND');
});

test('a rendered review carries what it is, so the javascript can find it again', function () {
    // The pin and verify buttons in the admin, and the "read more" toggles on the
    // front end, all find their review by these attributes.
    $template = '<div id="review-123" class="glsr-review">…</div>';

    $filtered = glsr(ReviewController::class)->filterReviewTemplate($template, [
        'review' => ['ID' => 123, 'type' => 'local', 'is_pinned' => 1, 'is_verified' => 0],
    ]);

    expect($filtered)->toContain('data-id="123"')
        ->toContain('data-type="local"')
        ->toContain('data-pinned="1"')
        ->not->toContain('data-verified'); // array_filter drops the falsy ones
});

test('the posts on a page are handed back exactly as they came', function () {
    // It is a `the_posts` filter and it exists only for its side effect: warming the
    // review cache for every review WordPress is about to render, in one query instead
    // of one per review. Handing back anything but the same posts would break the loop.
    $review = createReview();
    $posts = [get_post($review->ID), get_post(createPost())];

    expect(glsr(ReviewController::class)->filterPostsToCacheReviews($posts))->toBe($posts);
    expect(glsr(ReviewController::class)->filterPostsToCacheReviews([]))->toBe([]);
});

test('the post data of something that is not a review being edited is not touched', function () {
    $data = ['post_author' => 5, 'post_type' => 'post'];

    // not a review
    expect(glsr(ReviewController::class)->filterReviewPostData($data, ['ID' => 1, 'action' => 'editpost', 'post_type' => 'post']))
        ->toBe($data);
    // a review, but not an edit (no ID yet)
    expect(glsr(ReviewController::class)->filterReviewPostData($data, ['action' => 'editpost', 'post_type' => glsr()->post_type]))
        ->toBe($data);
    // a review being saved by something other than the editor (no action)
    expect(glsr(ReviewController::class)->filterReviewPostData($data, ['ID' => 1, 'post_type' => glsr()->post_type]))
        ->toBe($data);
});

/*
 * Editing a review in the admin.
 */

test('editing a review in the editor saves what was typed into it', function () {
    // onEditReview only runs on the plugin's own edit/post screens — WordPress fires
    // post_updated for every post on the site, including every autosave and revision.
    $review = createReview(['rating' => 5, 'name' => 'Jane']);
    set_current_screen(glsr()->post_type); // the review editor: base `post`
    $_POST[glsr()->id] = [
        'is_editing_review' => 1,
        'name' => 'Jane', // unchanged, so the avatar is left alone
        'rating' => 3,
        'is_pinned' => 1,
    ];

    glsr(ReviewController::class)->onEditReview(
        $review->ID, get_post($review->ID), get_post($review->ID)
    );

    $updated = glsr_get_review($review->ID);
    expect($updated->rating)->toBe(3)
        ->and($updated->is_pinned)->toBeTrue();

    // What it does NOT save from here is the assignments: they are read with a bare
    // filter_input(INPUT_POST, 'post_ids', …, FILTER_FORCE_ARRAY), which no test can
    // populate, so they always arrive as "none". Helper::filterInputArray() falls back
    // to $_POST and these fields do not — see the note at the top of the file.
});

test('a review edited from a screen that is not ours is left alone', function () {
    // post_updated fires on every post save on the site. Without the screen check, any
    // plugin calling wp_update_post() on a review would have this controller read
    // $_POST as though it were the review editor's form.
    $review = createReview(['rating' => 5]);
    set_current_screen('front');
    $_POST[glsr()->id] = ['is_editing_review' => 1, 'rating' => 1];

    glsr(ReviewController::class)->onEditReview(
        $review->ID, get_post($review->ID), get_post($review->ID)
    );

    expect(glsr_get_review($review->ID)->rating)->toBe(5);
});

test('a review that is being trashed is not treated as an edit', function () {
    $review = createReview(['rating' => 5]);
    $post = get_post($review->ID);
    $trashed = clone $post;
    $trashed->post_status = 'trash';

    $isEdited = protectedMethod(ReviewController::class, 'isEditedReview')
        ->invoke(glsr(ReviewController::class), $trashed, $post);

    expect($isEdited)->toBeFalse();
});

test('a response typed into the metabox is saved with the review', function () {
    // The response has its own nonce, separate from the post's, because it is the one
    // field on the screen that is published to the front end as the site owner.
    $review = createReview();
    set_current_screen(glsr()->post_type);
    $_POST[glsr()->id] = ['is_editing_review' => 1, 'name' => $review->author];
    $_POST['_nonce-response'] = wp_create_nonce('response');
    $_POST['response'] = 'Thank you for the kind words.';

    glsr(ReviewController::class)->onEditReview(
        $review->ID, get_post($review->ID), get_post($review->ID)
    );

    expect(glsr_get_review($review->ID)->response)->toBe('Thank you for the kind words.');
});

test('a response without its own nonce is not saved', function () {
    $review = createReview();
    set_current_screen(glsr()->post_type);
    $_POST[glsr()->id] = ['is_editing_review' => 1, 'name' => $review->author];
    $_POST['response'] = 'Not from the metabox.'; // no _nonce-response

    glsr(ReviewController::class)->onEditReview(
        $review->ID, get_post($review->ID), get_post($review->ID)
    );

    expect(glsr_get_review($review->ID)->response)->toBeEmpty(); // never written, so never read
});

/*
 * The avatar, which is regenerated when the name changes — but only if it was ours.
 */

function refreshedAvatar(array $data, Review $review): string
{
    return protectedMethod(ReviewController::class, 'refreshAvatar')
        ->invoke(glsr(ReviewController::class), $data, $review);
}

test('an avatar somebody uploaded is not thrown away when the name changes', function () {
    // Only the generated initials avatar is regenerated. A real avatar — a gravatar, an
    // uploaded image, one an addon set — is the person's, and a typo in the name is not
    // a reason to delete it.
    $review = createReview(['name' => 'Jane']);
    $uploaded = 'https://example.org/wp-content/uploads/jane.png';

    expect(refreshedAvatar(['name' => 'Janet', 'avatar' => $uploaded], $review))->toBe($uploaded);
});

test('the initials avatar follows the name', function () {
    $review = createReview(['name' => 'Jane Doe']);
    $initials = 'https://example.org/wp-content/uploads/site-reviews/avatars/JD.svg';

    $refreshed = refreshedAvatar(['name' => 'Bob Smith', 'avatar' => $initials], $review);

    expect($refreshed)->not->toBe($initials)
        ->and($refreshed)->not->toBeEmpty();
});

test('an unchanged name leaves the avatar exactly as it was', function () {
    $review = createReview(['name' => 'Jane']);

    expect(refreshedAvatar(['name' => 'Jane', 'avatar' => 'anything at all'], $review))
        ->toBe('anything at all');
});

/*
 * The two that cannot be reached.
 */

test('the fallback approve action does nothing unless it is ours', function () {
    // A no-js fallback for the approve link, on WordPress's admin_action_{action} hook
    // — which is fired for whatever `action` is in the query string, from any plugin.
    // The `plugin` query var is the only thing that says this one is ours, and
    // filter_input(INPUT_GET) cannot be given one from here, so this asserts the guard
    // and nothing past it. The command it would run is covered in CommandTest.
    $review = createReview(['is_approved' => false]);

    glsr(ReviewController::class)->onApprove();
    glsr(ReviewController::class)->onUnapprove();

    expect(get_post_status($review->ID))->toBe('pending'); // no redirect, no exit, no change
});

test('a notification is not queued while the suite is running', function () {
    // Queue::async() short-circuits on GLSR_UNIT_TESTS — Action Scheduler writes to its
    // own tables, and those do not roll back with the transaction. So this exercises
    // the guards and proves nothing is queued; what the notification SAYS is
    // NotificationTest's job.
    glsr(ReviewController::class)->sendNotification(createReview());

    expect(glsr(Queue::class)->isPending('queue/notification'))->toBeFalse();
});
