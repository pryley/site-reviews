<?php

use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;

uses()->group('plugin');

test('reviews restrict', function () {
    $postId = createPost();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $userId = createUser();
    $args = [
        'assigned_to' => $postId,
        'block' => '',
        'category' => $termId,
        'class' => '',
        'display' => 1,
        'hide' => [],
        'id' => '',
        'offset' => '',
        'page' => 2,
        'pageUrl' => 'http://site-reviews.test/reviews/',
        'pagination' => 'ajax',
        'schema' => '',
        'terms' => '',
        'type' => 'local',
        'user' => $userId,
    ];
    $expected = [
        'assigned_posts' => [$postId],
        'assigned_posts_types' => [],
        'assigned_terms' => [$termId],
        'assigned_users' => [$userId],
        'content' => '',
        'date' => [
            'after' => '',
            'before' => '',
            'day' => '',
            'inclusive' => '',
            'month' => '',
            'year' => '',
        ],
        'email' => '',
        'integration' => '',
        'ip_address' => '',
        'offset' => 0,
        'order' => 'DESC',
        'orderby' => 'p.post_date',
        'page' => 2,
        'per_page' => 1,
        'post__in' => [],
        'post__not_in' => [],
        'rating' => 0,
        'rating_field' => 'rating',
        'status' => 1,
        'terms' => -1,
        'type' => 'local',
        'user__in' => [],
        'user__not_in' => [],
        'verified' => -1,
    ];
    $test = glsr()->args(glsr(ReviewsDefaults::class)->restrict($args));
    expect(count($test))->toEqual(count(glsr(ReviewsDefaults::class)->defaults()));
    expect($test->toArray())->toEqual($expected);
});

test('site reviews restrict', function () {
    $postId = createPost();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $userId = createUser();
    $args = [
        'assigned_to' => $postId,
        'category' => $termId,
        'class' => '',
        'className' => '',
        'display' => 1,
        'hide' => '',
        'id' => '',
        'pagination' => 'ajax',
        'post_id' => 4466,
        'rating' => 3,
        'schema' => 1,
        'terms' => '',
        'type' => 'local',
        'user' => $userId,
        'verified' => '',
    ];
    $test = glsr(SiteReviewsDefaults::class)->restrict($args);
    expect(count($test))->toEqual(count(glsr(SiteReviewsDefaults::class)->defaults()));
    expect(str_starts_with($test['id'], glsr()->prefix))->toBeTrue();
    unset($test['id']);
    expect($test)->toEqual([
        'assigned_posts' => $postId,
        'assigned_terms' => $termId,
        'assigned_users' => $userId,
        'author' => 0,
        'class' => '',
        'debug' => false,
        'display' => 1,
        'hide' => [],
        'offset' => 0,
        'page' => 1,
        'pagination' => 'ajax',
        'rating' => 3,
        'rating_field' => 'rating',
        'schema' => true,
        'terms' => '',
        'type' => 'local',
        'verified' => '',
   ]);
});
