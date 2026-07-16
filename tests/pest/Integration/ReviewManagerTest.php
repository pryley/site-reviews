<?php

use Faker\Factory;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createPosts;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createTerms;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\createUserAndGet;
use function GeminiLabs\SiteReviews\Tests\createUsers;
use function GeminiLabs\SiteReviews\Tests\referer;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

test('assign post', function () {
    $posts = createPosts(2);
    $postId = createPost();
    $postId_private = createPost(['post_status' => 'private']);
    $postId_protected = (int) createPost(['post_status' => 'protected', 'post_password' => '123']);
    // automatically assign posts
    $review = createTestReview([
        'assigned_posts' => implode(',', $posts),
    ]);
    expect($review->isValid())->toBeTrue();
    expect($review->assigned_posts)->toEqual($posts);
    // manually assign posts
    $posts[] = $postId;
    glsr(ReviewManager::class)->assignPost($review, $postId);
    glsr(ReviewManager::class)->assignPost($review, $postId_private); // should fail
    glsr(ReviewManager::class)->assignPost($review, $postId_protected); // should fail
    $review->refresh();
    expect($review->assigned_posts)->toEqual($posts);
    foreach ($posts as $postId) {
        expect(get_post_meta($postId, '_glsr_average', true))->toEqual(5);
        expect(get_post_meta($postId, '_glsr_ranking', true) > 0)->toBeTrue();
        expect(get_post_meta($postId, '_glsr_reviews', true))->toEqual(1);
    }
});

test('assign term', function () {
    // automatically assign terms
    $terms = createTerms(2, ['taxonomy' => glsr()->taxonomy]);
    $review = createTestReview([
        'assigned_terms' => implode(',', $terms),
    ]);
    expect($review->isValid())->toBeTrue();
    expect($review->assigned_terms)->toEqual($terms);
    foreach ($terms as $termId) {
        expect(get_term_meta($termId, '_glsr_average', true))->toEqual(5);
        expect(get_term_meta($termId, '_glsr_ranking', true) > 0)->toBeTrue();
        expect(get_term_meta($termId, '_glsr_reviews', true))->toEqual(1);
    }
    // manually assign term
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $terms[] = $termId;
    glsr(ReviewManager::class)->assignTerm($review, $termId);
    $review->refresh();
    expect($review->assigned_terms)->toEqual($terms);
    expect(get_term_meta($termId, '_glsr_average', true))->toEqual(5);
    expect(get_term_meta($termId, '_glsr_ranking', true) > 0)->toBeTrue();
    expect(get_term_meta($termId, '_glsr_reviews', true))->toEqual(1);
});

test('assign user', function () {
    // automatically assign users
    $users = createUsers(2);
    $review = createTestReview([
        'assigned_users' => implode(',', $users),
    ]);
    expect($review->isValid())->toBeTrue();
    expect($review->assigned_users)->toEqual($users);
    foreach ($users as $userId) {
        expect(get_user_meta($userId, '_glsr_average', true))->toEqual(5);
        expect(get_user_meta($userId, '_glsr_ranking', true) > 0)->toBeTrue();
        expect(get_user_meta($userId, '_glsr_reviews', true))->toEqual(1);
    }
    // manually assign user
    $userId = createUser();
    $users[] = $userId;
    glsr(ReviewManager::class)->assignUser($review, $userId);
    $review->refresh();
    expect($review->assigned_users)->toEqual($users);
    expect(get_user_meta($userId, '_glsr_average', true))->toEqual(5);
    expect(get_user_meta($userId, '_glsr_ranking', true) > 0)->toBeTrue();
    expect(get_user_meta($userId, '_glsr_reviews', true))->toEqual(1);
});

test('create', function () {
    $review = createTestReview();
    expect($review->isValid())->toBeTrue();
});

test('create with terms', function () {
    // if terms are false (i.e. using the helper function), set them to false
    $review = createTestReview(['terms' => false]);
    expect($review->isValid())->toBeTrue();
    expect($review->terms)->toBeFalse();
    // test the helper function directly
    $options = glsr(OptionManager::class);
    $path = 'settings.general.require.approval';
    $setting = $options->get($path, 'no');
    $options->set($path, 'yes');
    $review = glsr_create_review(reviewRequest()->toArray());
    expect($review)->not->toEqual(false);
    expect($review->isValid())->toBeTrue();
    expect($review->is_approved)->toBeTrue();
    expect($review->terms)->toBeFalse();
    $options->set($path, $setting);
});

test('create with no terms field', function () {
    $review = createTestReview(['terms_exist' => false]);
    expect($review->isValid())->toBeTrue();
    expect($review->terms)->toBeFalse();
});

test('create with accepted terms', function () {
    $review = createTestReview([
        'terms_exist' => true,
        'terms' => true,
    ]);
    expect($review->isValid())->toBeTrue();
    expect($review->terms)->toBeTrue();
});

test('create with empty name and email', function () {
    $request = [
        'name' => '',
        'email' => '',
    ];
    // User 1 - use the display_name generated by WordPress
    $user1 = createUserAndGet([
        'user_email' => 'test_user@_1mail.com',
        'user_login' => 'test_user_1',
    ]);
    wp_set_current_user($user1->ID);
    $review = createTestReview($request);
    expect($review->isValid())->toBeTrue();
    expect($review->author_id)->toEqual($user1->ID);
    expect($review->email)->toEqual($user1->user_email);
    expect($review->author)->toEqual('testuser1');
    // User 2 - fallback to user_nicename
    $user2 = createUserAndGet([
        'user_email' => 'test_user_2@mail.com',
        'user_login' => 'test_user_2',
        'user_nicename' => 'xx2',
        'display_name' => ' ', // WordPress allows empty space values...
    ]);
    wp_set_current_user($user2->ID);
    $review = createTestReview($request);
    expect($review->isValid())->toBeTrue();
    expect($review->author_id)->toEqual($user2->ID);
    expect($review->email)->toEqual($user2->user_email);
    expect($review->author)->toEqual('xx2');
    // User 3 - use display_name
    $user3 = createUserAndGet([
        'user_email' => 'test_user_3@mail.com',
        'user_login' => 'test_user_3',
        'display_name' => 'xx3',
    ]);
    wp_set_current_user($user3->ID);
    $review = createTestReview($request);
    expect($review->isValid())->toBeTrue();
    expect($review->author_id)->toEqual($user3->ID);
    expect($review->email)->toEqual($user3->user_email);
    expect($review->author)->toEqual('xx3');
    // User 4 - don't expose email
    $user4 = createUserAndGet([
        'display_name' => 'test_user_4@mail.com',
        'user_email' => 'test_user_4@mail.com',
        'user_login' => 'test_user_4@mail.com',
        'user_nicename' => 'test_user_4@mail.com',
    ]);
    wp_set_current_user($user4->ID);
    $review = createTestReview($request);
    expect($review->isValid())->toBeTrue();
    expect($review->author_id)->toEqual($user4->ID);
    expect($review->email)->toEqual($user4->user_email);
    expect($review->author)->toEqual('testuser4');
});

test('create with rejected terms', function () {
    $review = createTestReview(['terms_exist' => true]);
    expect($review->isValid())->toBeTrue();
    expect($review->terms)->toBeFalse();
});

test('ip address is protected', function () {
    $review = createTestReview(['ip_address' => '111.222.333.444']);
    expect($review->isValid())->toBeTrue();
    expect($review->ip_address)->toEqual('127.0.0.1');
});

test('ip address is unprotected when using helper fn', function () {
    $request = reviewRequest(['ip_address' => '11.22.33.44']);
    $review = glsr_create_review($request->toArray());
    expect($review)->not->toEqual(false);
    expect($review->isValid())->toBeTrue();
    expect($review->ip_address)->toEqual('11.22.33.44');
});

test('is pinned is protected', function () {
    $review = createTestReview(['is_pinned' => true]);
    expect($review->isValid())->toBeTrue();
    expect($review->is_pinned)->toBeFalse();
});

test('is pinned is unprotected when using helper fn', function () {
    $request = reviewRequest(['is_pinned' => true]);
    $review = glsr_create_review($request->toArray());
    expect($review)->not->toEqual(false);
    expect($review->isValid())->toBeTrue();
    expect($review->is_pinned)->toBeTrue();
});

test('is verified is protected', function () {
    $review = createTestReview(['is_verified' => true]);
    expect($review->isValid())->toBeTrue();
    expect($review->is_verified)->toBeFalse();
});

test('is verified is unprotected when using helper fn', function () {
    $request = reviewRequest(['is_verified' => true]);
    $review = glsr_create_review($request->toArray());
    expect($review)->not->toEqual(false);
    expect($review->isValid())->toBeTrue();
    expect($review->is_verified)->toBeTrue();
});

test('unassign post', function () {
    $postId = (int) createPost();
    $review = createTestReview(['assigned_posts' => $postId]);
    expect($review->isValid())->toBeTrue();
    expect($review->assigned_posts)->toEqual([$postId]);
    // unassign post
    glsr(ReviewManager::class)->unassignPost($review, $postId);
    $review->refresh();
    expect($review->assigned_posts)->toEqual([]);
    expect(get_post_meta($postId, '_glsr_average', true))->toEqual(0);
    expect(get_post_meta($postId, '_glsr_ranking', true))->toEqual(0);
    expect(get_post_meta($postId, '_glsr_reviews', true))->toEqual(0);
});

test('unassign term', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $review = createTestReview(['assigned_terms' => $termId]);
    expect($review->isValid())->toBeTrue();
    expect($review->assigned_terms)->toEqual([$termId]);
    // unassign term
    glsr(ReviewManager::class)->unassignTerm($review, $termId);
    $review->refresh();
    expect($review->assigned_terms)->toEqual([]);
    expect(get_term_meta($termId, '_glsr_average', true))->toEqual(0);
    expect(get_term_meta($termId, '_glsr_ranking', true))->toEqual(0);
    expect(get_term_meta($termId, '_glsr_reviews', true))->toEqual(0);
});

test('unassign user', function () {
    $userId = createUser();
    $review = createTestReview(['assigned_users' => $userId]);
    expect($review->isValid())->toBeTrue();
    expect($review->assigned_users)->toEqual([$userId]);
    // unassign user
    glsr(ReviewManager::class)->unassignUser($review, $userId);
    $review->refresh();
    expect($review->assigned_users)->toEqual([]);
    expect(get_user_meta($userId, '_glsr_average', true))->toEqual(0);
    expect(get_user_meta($userId, '_glsr_ranking', true))->toEqual(0);
    expect(get_user_meta($userId, '_glsr_reviews', true))->toEqual(0);
});

/*
 * duplicate().
 *
 * It is the one place that feeds an existing review's stored state back through
 * glsr_create_review(), which runs the FORM validators over it — so a value that
 * is legitimate in storage but not legitimate in a form submission stops a review
 * from being duplicated at all. The terms toggle is exactly such a value.
 *
 * MultilingualPress\ReviewCopier::copy() does the same thing, and carries the same
 * guard for the same reason. It is NOT executed by this suite (its stub is
 * deliberately not loaded and it needs multisite) — these tests cover the
 * mechanism it depends on, not the call site itself.
 */

test('duplicates a review that never accepted the terms', function () {
    // A review that was imported, added in the admin, or made through the API
    // stores terms=0 — CreateReviewDefaults defaults it to false — and a false is
    // not empty (Helper::isEmpty), so it used to survive into the request that
    // glsr_create_review() validates, where the form's "accepted" rule refused it.
    // Duplicate Page reported that to the user as "Invalid review."
    wp_set_current_user(createUser(['role' => 'administrator']));
    $review = createTestReview(['rating' => 4, 'title' => 'The original']);
    expect($review->terms)->toBeFalse(); // the precondition the bug needed

    $duplicate = glsr(ReviewManager::class)->duplicate($review->ID);

    expect($duplicate)->toBeInstanceOf(Review::class)
        ->and($duplicate->ID)->not->toBe($review->ID)
        ->and($duplicate->rating)->toBe(4)
        ->and($duplicate->content)->toBe($review->content)
        ->and($duplicate->terms)->toBeFalse(); // the copy tells the same truth
});

test('duplicates a review that did accept the terms', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $review = createTestReview(['terms' => true]);
    expect($review->terms)->toBeTrue();

    $duplicate = glsr(ReviewManager::class)->duplicate($review->ID);

    expect($duplicate)->toBeInstanceOf(Review::class)
        ->and($duplicate->terms)->toBeTrue(); // the acceptance is not thrown away
});

test('refuses to duplicate something that is not a review', function () {
    expect(glsr(ReviewManager::class)->duplicate(createPost()))->toBeFalse();
});

test('refuses to duplicate a review post with no review behind it', function () {
    // A post of the review type with no row in the ratings table.
    $postId = (int) wp_insert_post([
        'post_status' => 'publish',
        'post_title' => 'Not really a review',
        'post_type' => glsr()->post_type,
    ], true);

    expect(glsr(ReviewManager::class)->duplicate($postId))->toBeFalse();
});

/*
 * The paths a live site hits when something is wrong: the post that cannot be inserted,
 * the ratings table that cannot be written, the update that cannot be saved. Database
 * failures are stood in by a fake swapped into the container (Database is stateless);
 * post-insert failures by core's own wp_insert_post_empty_content filter.
 */

function withFakeDatabase(GeminiLabs\SiteReviews\Database $fake, callable $callback)
{
    $original = glsr(GeminiLabs\SiteReviews\Database::class);
    glsr()->alias(GeminiLabs\SiteReviews\Database::class, $fake);
    try {
        return $callback();
    } finally {
        glsr()->alias(GeminiLabs\SiteReviews\Database::class, $original);
    }
}

test('a review whose post cannot be inserted is a refusal, not a half-made review', function () {
    add_filter('wp_insert_post_empty_content', '__return_true');
    try {
        $review = createTestReview();
    } finally {
        remove_filter('wp_insert_post_empty_content', '__return_true');
    }

    expect($review->isValid())->toBeFalse();
});

test('a review post can be adopted, and a non-review cannot', function () {
    $postId = (int) wp_insert_post([
        'post_content' => 'Adopted content',
        'post_status' => 'publish',
        'post_title' => 'Adopted title',
        'post_type' => glsr()->post_type,
    ], true);

    $review = glsr(ReviewManager::class)->createFromPost($postId, reviewRequest(['rating' => 4])->toArray());
    expect($review)->toBeInstanceOf(Review::class)
        ->and($review->ID)->toBe($postId)
        ->and($review->rating)->toBe(4);

    expect(glsr(ReviewManager::class)->createFromPost(createPost()))->toBeFalse();
});

test('a review whose rating row cannot be written is refused and swept away', function () {
    // onCreateReview deletes the orphaned post when the ratings insert fails, so a broken
    // ratings table does not leave title-only "reviews" in the admin.
    $postId = (int) wp_insert_post([
        'post_content' => 'Doomed content',
        'post_status' => 'publish',
        'post_title' => 'Doomed',
        'post_type' => glsr()->post_type,
    ], true);
    $fake = new class() extends GeminiLabs\SiteReviews\Database {
        public function insert(string $table, array $data)
        {
            return 'ratings' === $table ? false : parent::insert($table, $data);
        }
    };

    $result = withFakeDatabase($fake, fn () => glsr(ReviewManager::class)
        ->createFromPost($postId, reviewRequest()->toArray()));

    expect($result)->toBeFalse();
    expect(get_post($postId))->toBeNull();
});

test('deleting the revisions of a review deletes them all', function () {
    $review = createTestReview();
    wp_update_post(['ID' => $review->ID, 'post_content' => 'Edited once']);
    wp_save_post_revision($review->ID);
    expect(wp_get_post_revisions($review->ID))->not->toBeEmpty();

    glsr(ReviewManager::class)->deleteRevisions($review->ID);

    expect(wp_get_post_revisions($review->ID))->toBeEmpty();
});

test('a duplicate carries the original\'s custom meta, but not its submission fingerprint', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $review = createTestReview();
    update_post_meta($review->ID, '_custom_color', 'blue');
    update_post_meta($review->ID, '_submitted', ['fingerprint']);
    // the cached Review memoised its meta() before the lines above; make duplicate() re-read
    glsr(GeminiLabs\SiteReviews\Database\Cache::class)->delete($review->ID, 'reviews');

    $duplicate = glsr(ReviewManager::class)->duplicate($review->ID);

    expect(get_post_meta($duplicate->ID, '_custom_color', true))->toBe('blue');
    // the duplicate's _submitted is its OWN creation record, not the original's copied over
    expect(get_post_meta($duplicate->ID, '_submitted', true))->not->toBe(['fingerprint']);
});

test('a duplicate that cannot be created is a refusal', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $review = createTestReview();

    add_filter('wp_insert_post_empty_content', '__return_true');
    try {
        expect(glsr(ReviewManager::class)->duplicate($review->ID))->toBeFalse();
    } finally {
        remove_filter('wp_insert_post_empty_content', '__return_true');
    }
});

test('a post the user may not touch cannot be unassigned either', function () {
    $review = createTestReview();
    $privateId = (int) createPost(['post_status' => 'private']);

    expect(glsr(ReviewManager::class)->unassignPost($review, $privateId))->toBeFalse();
});

test('an update can change the review and its assignments in one call', function () {
    $review = createTestReview();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $postId = createPost();
    $userId = createUser();

    $updated = glsr(ReviewManager::class)->update($review->ID, [
        'assigned_posts' => [$postId],
        'assigned_terms' => [$termId],
        'assigned_users' => [$userId],
        'rating' => 2,
        'title' => 'Retitled',
    ]);

    expect($updated)->toBeInstanceOf(Review::class)
        ->and($updated->title)->toBe('Retitled')
        ->and($updated->rating)->toBe(2);
});

test('an update is refused when the rating row cannot be written', function () {
    $review = createTestReview();
    $fake = new class() extends GeminiLabs\SiteReviews\Database {
        public function update(string $table, array $data, array $where)
        {
            return false;
        }
    };

    $result = withFakeDatabase($fake, fn () => glsr(ReviewManager::class)
        ->update($review->ID, ['rating' => 3]));

    expect($result)->toBeFalse();
});

test('an update is refused on a post that is not a review', function () {
    expect(glsr(ReviewManager::class)->update(createPost(), ['title' => 'Not a review']))->toBeFalse();
});

test('updates with nothing relevant to say change nothing', function () {
    $review = createTestReview();
    $manager = glsr(ReviewManager::class);

    expect($manager->updateRating($review->ID, ['irrelevant' => 'x']))->toBe(0);
    expect($manager->updateReview($review->ID, ['irrelevant' => 'x']))->toBe(0);
    expect($manager->updateResponse($review->ID, ['response' => '']))->toBe(0); // no response before or after
});

test('a review update that wordpress refuses is an error, not a lie', function () {
    $review = createTestReview();

    add_filter('wp_insert_post_empty_content', '__return_true');
    try {
        expect(glsr(ReviewManager::class)->updateReview($review->ID, ['title' => 'New title']))->toBe(-1);
    } finally {
        remove_filter('wp_insert_post_empty_content', '__return_true');
    }
});

test('geolocation is retried only when the ip actually changed, and only when wanted', function () {
    $options = glsr(OptionManager::class);
    $review = createTestReview(); // its ip_address is 127.0.0.1 (protected)
    glsr(GeminiLabs\SiteReviews\Database\PostMeta::class)->set($review->ID, 'geolocation', ['lat' => 1]);

    $manager = glsr(ReviewManager::class);
    $geodata = fn () => glsr(GeminiLabs\SiteReviews\Database\PostMeta::class)->get($review->ID, 'geolocation');

    $manager->updateGeolocation($review->ID, []); // no ip in the update: not our business
    $manager->updateGeolocation($review->ID, ['ip_address' => '127.0.0.1']); // unchanged: nothing to do
    expect($geodata())->not->toBeEmpty();

    // the ip changed: the stale geolocation is dropped, but the setting is off so no lookup
    $manager->updateGeolocation($review->ID, ['ip_address' => '203.0.113.9']);
    expect($geodata())->toBeEmpty();

    $options->set('settings.reviews.geolocation', 'yes');
    $manager->updateGeolocation($review->ID, ['ip_address' => '']); // no usable ip: no lookup
    $manager->updateGeolocation($review->ID, ['ip_address' => '203.0.113.10']); // queued (NullQueue in the suite)
});

function createTestReview(array $values = []): Review
{
    $request = reviewRequest($values);
    if ($review = glsr(ReviewManager::class)->create(new CreateReview($request))) {
        return $review;
    }
    return new Review([]);
}

function reviewRequest(array $overrides = [])
{
    $faker = Factory::create();
    $request = new Request([
        '_action' => 'submit-review',
        '_ajax_request' => 1,
        '_post_id' => 1,
        '_referer' => referer(),
        'assigned_posts' => '',
        'assigned_terms' => '',
        'assigned_users' => '',
        'excluded' => '',
        'form_id' => $faker->slug(),
        'rating' => 5,
        'title' => $faker->sentence(),
        'content' => $faker->text(),
        'name' => $faker->name(),
        'email' => $faker->email(),
    ]);
    $request->merge($overrides);
    return $request;
}
