<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class DefaultsTest extends WP_UnitTestCase
{
    public function test_reviews_restrict()
    {
        $postId = self::factory()->post->create();
        $termId = self::factory()->term->create(['taxonomy' => glsr()->taxonomy]);
        $userId = self::factory()->user->create();
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
            'date' => '',
            'email' => '',
            'ip_address' => '',
            'offset' => 0,
            'order' => 'DESC',
            'orderby' => 'date',
            'page' => 2,
            'pagination' => 'ajax',
            'per_page' => 1,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => 0,
            'rating_field' => 'rating',
            'status' => 'approved',
            'terms' => '',
            'type' => 'local',
            'user__in' => [],
            'user__not_in' => [],
        ];
        $test = glsr()->args(glsr(ReviewsDefaults::class)->restrict($args));
        $this->assertEquals(count($test), count(glsr(ReviewsDefaults::class)->defaults()));
        $this->assertEquals($test->toArray(), $expected);
    }

    public function test_site_reviews_restrict()
    {
        $postId = self::factory()->post->create();
        $termId = self::factory()->term->create(['taxonomy' => glsr()->taxonomy]);
        $userId = self::factory()->user->create();
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
        ];
        $test = glsr(SiteReviewsDefaults::class)->restrict($args);
        $this->assertEquals(count($test), count(glsr(SiteReviewsDefaults::class)->defaults()));
        $this->assertTrue(Str::startsWith($test['id'], glsr()->prefix));
        unset($test['id']);
        $this->assertEquals($test, [
            'assigned_posts' => $postId,
            'assigned_terms' => $termId,
            'assigned_users' => $userId,
            'class' => '',
            'debug' => false,
            'display' => 1,
            'hide' => [],
            'offset' => '',
            'page' => 1,
            'pagination' => 'ajax',
            'rating' => 3,
            'rating_field' => 'rating',
            'schema' => true,
            'terms' => '',
            'type' => 'local',
       ]);
    }
}
