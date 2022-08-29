<?php

namespace GeminiLabs\SiteReviews\Tests;

use Faker\Factory;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class ReviewManagerTest extends WP_UnitTestCase
{
    use Setup;

    public $request;
    public $termArgs;

    public function createReview(Request $request)
    {
        if ($review = glsr(ReviewManager::class)->create(new CreateReview($request))) {
            return $review;
        }
        return new Review([]);
    }

    public function set_up()
    {
        parent::set_up();
        $faker = Factory::create();
        $this->request = new Request([
            '_action' => 'submit-review',
            '_ajax_request' => 1,
            '_post_id' => 1,
            '_referer' => $this->referer,
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'excluded' => '',
            'form_id' => $faker->slug,
            'rating' => 5,
            'title' => $faker->sentence,
            'content' => $faker->text,
            'name' => $faker->name,
            'email' => $faker->email,
        ]);
        $this->termArgs = [
            'taxonomy' => glsr()->taxonomy,
        ];
    }

    public function test_assign_post()
    {
        // automatically assign posts
        $posts = [
            self::factory()->post->create(),
            self::factory()->post->create(),
        ];
        $request = $this->request;
        $review = $this->createReview($request->merge([
            'assigned_posts' => implode(',', $posts),
        ]));
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_posts, $posts);
        foreach ($posts as $postId) {
            $this->assertEquals(get_post_meta($postId, '_glsr_average', true), 5);
            $this->assertTrue(get_post_meta($postId, '_glsr_ranking', true) > 0);
            $this->assertEquals(get_post_meta($postId, '_glsr_reviews', true), 1);
        }
        // manually assign post
        $postId = self::factory()->post->create();
        $posts[] = $postId;
        glsr(ReviewManager::class)->assignPost($review, $postId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_posts, $posts);
        $this->assertEquals(get_post_meta($postId, '_glsr_average', true), 5);
        $this->assertTrue(get_post_meta($postId, '_glsr_ranking', true) > 0);
        $this->assertEquals(get_post_meta($postId, '_glsr_reviews', true), 1);
    }

    public function test_assign_term()
    {
        // automatically assign terms
        $terms = [
            self::factory()->term->create($this->termArgs),
            self::factory()->term->create($this->termArgs),
        ];
        $request = $this->request;
        $review = $this->createReview($request->merge([
            'assigned_terms' => implode(',', $terms),
        ]));
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_terms, $terms);
        foreach ($terms as $termId) {
            $this->assertEquals(get_term_meta($termId, '_glsr_average', true), 5);
            $this->assertTrue(get_term_meta($termId, '_glsr_ranking', true) > 0);
            $this->assertEquals(get_term_meta($termId, '_glsr_reviews', true), 1);
        }
        // manually assign term
        $termId = self::factory()->term->create($this->termArgs);
        $terms[] = $termId;
        glsr(ReviewManager::class)->assignTerm($review, $termId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_terms, $terms);
        $this->assertEquals(get_term_meta($termId, '_glsr_average', true), 5);
        $this->assertTrue(get_term_meta($termId, '_glsr_ranking', true) > 0);
        $this->assertEquals(get_term_meta($termId, '_glsr_reviews', true), 1);
    }

    public function test_assign_user()
    {
        // automatically assign users
        $users = [
            self::factory()->user->create(),
            self::factory()->user->create(),
        ];
        $request = $this->request;
        $review = $this->createReview($request->merge([
            'assigned_users' => implode(',', $users),
        ]));
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_users, $users);
        foreach ($users as $userId) {
            $this->assertEquals(get_user_meta($userId, '_glsr_average', true), 5);
            $this->assertTrue(get_user_meta($userId, '_glsr_ranking', true) > 0);
            $this->assertEquals(get_user_meta($userId, '_glsr_reviews', true), 1);
        }
        // manually assign user
        $userId = self::factory()->user->create();
        $users[] = $userId;
        glsr(ReviewManager::class)->assignUser($review, $userId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_users, $users);
        $this->assertEquals(get_user_meta($userId, '_glsr_average', true), 5);
        $this->assertTrue(get_user_meta($userId, '_glsr_ranking', true) > 0);
        $this->assertEquals(get_user_meta($userId, '_glsr_reviews', true), 1);
    }

    public function test_create()
    {
        $review = $this->createReview($this->request);
        $this->assertTrue($review->isValid());
    }

    public function test_create_with_terms()
    {
        // if terms field does not exist, set them to false
        $request = $this->request;
        $request->set('terms_exist', false);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->terms);
        // if terms field does exist but the terms are not accepted, set them to false
        $request = clone $this->request;
        $request->set('terms_exist', true);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->terms);
        // if terms field exists and the terms are accepted, set them to true
        $request = clone $this->request;
        $request->set('terms_exist', true);
        $request->set('terms', true);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertTrue($review->terms);
        // if terms are false (i.e. using the helper function), set them to false
        $request = clone $this->request;
        $request->set('terms', false);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->terms);
        // test the helper function directly
        $options = glsr(OptionManager::class);
        $path = 'settings.general.require.approval';
        $setting = $options->get($path, 'no');
        $options->set($path, 'yes');
        $review = glsr_create_review($this->request->toArray());
        $this->assertTrue($review->isValid());
        $this->assertTrue($review->is_approved);
        $this->assertFalse($review->terms);
        $options->set($path, $setting);
    }

    public function test_unassign_post()
    {
        $postId = self::factory()->post->create();
        $request = $this->request;
        $review = $this->createReview($request->merge([
            'assigned_posts' => $postId,
        ]));
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_posts, [$postId]);
        // unassign post
        glsr(ReviewManager::class)->unassignPost($review, $postId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_posts, []);
        $this->assertEquals(get_post_meta($postId, '_glsr_average', true), 0);
        $this->assertEquals(get_post_meta($postId, '_glsr_ranking', true), 0);
        $this->assertEquals(get_post_meta($postId, '_glsr_reviews', true), 0);
    }

    public function test_unassign_term()
    {
        $termId = self::factory()->term->create($this->termArgs);
        $request = $this->request;
        $review = $this->createReview($request->merge([
            'assigned_terms' => $termId,
        ]));
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_terms, [$termId]);
        // unassign term
        glsr(ReviewManager::class)->unassignTerm($review, $termId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_terms, []);
        $this->assertEquals(get_term_meta($termId, '_glsr_average', true), 0);
        $this->assertEquals(get_term_meta($termId, '_glsr_ranking', true), 0);
        $this->assertEquals(get_term_meta($termId, '_glsr_reviews', true), 0);
    }

    public function test_unassign_user()
    {
        $userId = self::factory()->user->create();
        $request = $this->request;
        $review = $this->createReview($request->merge([
            'assigned_users' => $userId,
        ]));
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_users, [$userId]);
        // unassign user
        glsr(ReviewManager::class)->unassignUser($review, $userId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_users, []);
        $this->assertEquals(get_user_meta($userId, '_glsr_average', true), 0);
        $this->assertEquals(get_user_meta($userId, '_glsr_ranking', true), 0);
        $this->assertEquals(get_user_meta($userId, '_glsr_reviews', true), 0);
    }
}
