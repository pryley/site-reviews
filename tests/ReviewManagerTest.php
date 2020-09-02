<?php

namespace GeminiLabs\SiteReviews\Tests;

use Faker\Factory;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Request;
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
        return glsr(ReviewManager::class)->create(new CreateReview($request));
    }

    public function setUp()
    {
        parent::setUp();
        $faker = Factory::create();
        $this->request = new Request([
            '_action' => 'submit-review',
            '_post_id' => 1,
            '_referer' => '',
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'excluded' => '',
            'form_id' => 'glsr_123',
            'rating' => 5,
            'title' => $faker->sentence,
            'content' => $faker->text,
            'name' => $faker->name,
            'email' => $faker->email,
            '_ajax_request' => 1,
        ]);
        $this->termArgs = [
            'taxonomy' => glsr()->taxonomy,
        ];
    }

    public function test_assign_post()
    {
        $posts = [
            self::factory()->post->create(),
            self::factory()->post->create(),
        ];
        $request = $this->request;
        $request->set('assigned_posts', implode(',', $posts));
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_posts, $posts);
        $postId = self::factory()->post->create();
        $posts[] = $postId;
        glsr(ReviewManager::class)->assignPost($review, $postId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_posts, $posts);
    }

    public function test_assign_term()
    {
        $terms = [
            self::factory()->term->create($this->termArgs),
            self::factory()->term->create($this->termArgs),
        ];
        $request = $this->request;
        $request->set('assigned_terms', implode(',', $terms));
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_terms, $terms);
        $termId = self::factory()->term->create($this->termArgs);
        $terms[] = $termId;
        glsr(ReviewManager::class)->assignTerm($review, $termId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_terms, $terms);
    }

    public function test_assign_user()
    {
        $users = [
            self::factory()->user->create(),
            self::factory()->user->create(),
        ];
        $request = $this->request;
        $request->set('assigned_users', implode(',', $users));
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_users, $users);
        $userId = self::factory()->user->create();
        $users[] = $userId;
        glsr(ReviewManager::class)->assignUser($review, $userId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_users, $users);
    }

    public function test_create()
    {
        $review = $this->createReview($this->request);
        $this->assertTrue($review->isValid());
    }

    public function test_unassign_post()
    {
        $postId = self::factory()->post->create();
        $request = $this->request;
        $request->set('assigned_posts', $postId);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_posts, [$postId]);
        glsr(ReviewManager::class)->unassignPost($review, $postId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_posts, []);
    }

    public function test_unassign_term()
    {
        $termId = self::factory()->term->create($this->termArgs);
        $request = $this->request;
        $request->set('assigned_terms', $termId);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_terms, [$termId]);
        glsr(ReviewManager::class)->unassignTerm($review, $termId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_terms, []);
    }

    public function test_unassign_user()
    {
        $userId = self::factory()->user->create();
        $request = $this->request;
        $request->set('assigned_users', $userId);
        $review = $this->createReview($request);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_users, [$userId]);
        glsr(ReviewManager::class)->unassignUser($review, $userId);
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_users, []);
    }
}
