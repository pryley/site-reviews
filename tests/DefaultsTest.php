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
        $args = [
            'assigned_to' => 4466,
            'block' => '',
            'category' => 48,
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
            'user' => 1,
        ];
        $expected = [
            'assigned_posts' => 4466,
            'assigned_terms' => 48,
            'assigned_users' => 1,
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
        $args = [
            'assigned_to' => 4466,
            'category' => 103,
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
            'user' => 1,
        ];
        $test = glsr(SiteReviewsDefaults::class)->restrict($args);
        $this->assertEquals(count($test), count(glsr(SiteReviewsDefaults::class)->defaults()));
        $this->assertTrue(Str::startsWith(glsr()->prefix, $test['id']));
        unset($test['id']);
        $this->assertEquals($test, [
            'assigned_posts' => 4466,
            'assigned_terms' => 103,
            'assigned_users' => 1,
            'class' => '',
            'display' => 1,
            'hide' => [],
            'offset' => '',
            'page' => 1,
            'pagination' => 'ajax',
            'rating' => 3,
            'schema' => true,
            'terms' => '',
            'type' => 'local',
       ]);
    }
}
