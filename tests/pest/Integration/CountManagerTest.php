<?php

use GeminiLabs\SiteReviews\Database\CountManager;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The denormalized rating counts: posts(), terms() and users() write the
 * average/ranking/reviews meta for one assignee, and the *Average/*Ranking/*Reviews
 * getters are what themes and the orderby machinery read back.
 */

beforeEach(fn () => resetPluginState());

test('the post, term and user rating meta round-trips through the getters', function () {
    $postId = createPost();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $userId = createUser();
    createReview([
        'assigned_posts' => [$postId],
        'assigned_terms' => [$termId],
        'assigned_users' => [$userId],
        'rating' => 4,
    ]);

    $manager = glsr(CountManager::class);
    $manager->posts($postId);
    $manager->terms($termId);
    $manager->users($userId);

    expect($manager->postsAverage($postId))->toBe(4.0)
        ->and($manager->postsRanking($postId))->toBeGreaterThan(0.0)
        ->and($manager->postsReviews($postId))->toBe(1);
    expect($manager->termsAverage($termId))->toBe(4.0)
        ->and($manager->termsRanking($termId))->toBeGreaterThan(0.0)
        ->and($manager->termsReviews($termId))->toBe(1);
    expect($manager->usersAverage($userId))->toBe(4.0)
        ->and($manager->usersRanking($userId))->toBeGreaterThan(0.0)
        ->and($manager->usersReviews($userId))->toBe(1);
});

test('an assignee with no counted reviews reads back as zero, not an error', function () {
    $manager = glsr(CountManager::class);
    $postId = createPost();

    expect($manager->postsAverage($postId))->toBe(0.0)
        ->and($manager->termsRanking(999999123))->toBe(0.0)
        ->and($manager->usersReviews(999999123))->toBe(0);
});
