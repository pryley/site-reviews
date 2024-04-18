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

    public function test_assign_post()
    {
        $posts = self::factory()->post->create_many(2);
        $postId = self::factory()->post->create();
        $postId_private = self::factory()->post->create(['post_status' => 'private']);
        $postId_protected = (int) self::factory()->post->create(['post_status' => 'protected', 'post_password' => '123']);
        // automatically assign posts
        $review = $this->createReview([
            'assigned_posts' => implode(',', $posts),
        ]);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_posts, $posts);
        // manually assign posts
        $posts[] = $postId;
        glsr(ReviewManager::class)->assignPost($review, $postId);
        glsr(ReviewManager::class)->assignPost($review, $postId_private); // should fail
        glsr(ReviewManager::class)->assignPost($review, $postId_protected); // should fail
        $review = glsr(ReviewManager::class)->get($review->ID);
        $this->assertEquals($review->assigned_posts, $posts);
        foreach ($posts as $postId) {
            $this->assertEquals(get_post_meta($postId, '_glsr_average', true), 5);
            $this->assertTrue(get_post_meta($postId, '_glsr_ranking', true) > 0);
            $this->assertEquals(get_post_meta($postId, '_glsr_reviews', true), 1);
        }
    }

    public function test_assign_term()
    {
        // automatically assign terms
        $terms = self::factory()->term->create_many(2, ['taxonomy' => glsr()->taxonomy]);
        $review = $this->createReview([
            'assigned_terms' => implode(',', $terms),
        ]);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->assigned_terms, $terms);
        foreach ($terms as $termId) {
            $this->assertEquals(get_term_meta($termId, '_glsr_average', true), 5);
            $this->assertTrue(get_term_meta($termId, '_glsr_ranking', true) > 0);
            $this->assertEquals(get_term_meta($termId, '_glsr_reviews', true), 1);
        }
        // manually assign term
        $termId = self::factory()->term->create(['taxonomy' => glsr()->taxonomy]);
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
        $users = self::factory()->user->create_many(2);
        $review = $this->createReview([
            'assigned_users' => implode(',', $users),
        ]);
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
        $review = $this->createReview();
        $this->assertTrue($review->isValid());
    }

    public function test_create_with_terms()
    {
        // if terms are false (i.e. using the helper function), set them to false
        $review = $this->createReview(['terms' => false]);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->terms);
        // test the helper function directly
        $options = glsr(OptionManager::class);
        $path = 'settings.general.require.approval';
        $setting = $options->get($path, 'no');
        $options->set($path, 'yes');
        $review = glsr_create_review($this->request()->toArray());
        $this->assertNotEquals(false, $review);
        $this->assertTrue($review->isValid());
        $this->assertTrue($review->is_approved);
        $this->assertFalse($review->terms);
        $options->set($path, $setting);
    }

    public function test_create_with_no_terms_field()
    {
        $review = $this->createReview(['terms_exist' => false]);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->terms);
    }

    public function test_create_with_accepted_terms()
    {
        $review = $this->createReview([
            'terms_exist' => true,
            'terms' => true,
        ]);
        $this->assertTrue($review->isValid());
        $this->assertTrue($review->terms);
    }

    public function test_create_with_rejected_terms()
    {
        $review = $this->createReview(['terms_exist' => true]);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->terms);
    }

    public function test_ip_address_is_protected()
    {
        $review = $this->createReview(['ip_address' => '111.222.333.444']);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->ip_address, '127.0.0.1');
    }

    public function test_ip_address_is_unprotected_when_using_helper_fn()
    {
        $request = $this->request(['ip_address' => '11.22.33.44']);
        $review = glsr_create_review($request->toArray());
        $this->assertNotEquals(false, $review);
        $this->assertTrue($review->isValid());
        $this->assertEquals($review->ip_address, '11.22.33.44');
    }

    public function test_is_pinned_is_protected()
    {
        $review = $this->createReview(['is_pinned' => true]);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->is_pinned);
    }

    public function test_is_pinned_is_unprotected_when_using_helper_fn()
    {
        $request = $this->request(['is_pinned' => true]);
        $review = glsr_create_review($request->toArray());
        $this->assertNotEquals(false, $review);
        $this->assertTrue($review->isValid());
        $this->assertTrue($review->is_pinned);
    }

    public function test_is_verified_is_protected()
    {
        $review = $this->createReview(['is_verified' => true]);
        $this->assertTrue($review->isValid());
        $this->assertFalse($review->is_verified);
    }

    public function test_is_verified_is_unprotected_when_using_helper_fn()
    {
        $request = $this->request(['is_verified' => true]);
        $review = glsr_create_review($request->toArray());
        $this->assertNotEquals(false, $review);
        $this->assertTrue($review->isValid());
        $this->assertTrue($review->is_verified);
    }

    public function test_unassign_post()
    {
        $postId = (int) self::factory()->post->create();
        $review = $this->createReview(['assigned_posts' => $postId]);
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
        $termId = self::factory()->term->create(['taxonomy' => glsr()->taxonomy]);
        $review = $this->createReview(['assigned_terms' => $termId]);
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
        $review = $this->createReview(['assigned_users' => $userId]);
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

    protected function createReview(array $values = []): Review
    {
        $request = $this->request($values);
        if ($review = glsr(ReviewManager::class)->create(new CreateReview($request))) {
            return $review;
        }
        return new Review([]);
    }

    protected function request(array $overrides = [])
    {
        $faker = Factory::create();
        $request = new Request([
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
        $request->merge($overrides);
        return $request;
    }
}
