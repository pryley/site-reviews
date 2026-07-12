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
