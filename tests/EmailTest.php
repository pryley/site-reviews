<?php

namespace GeminiLabs\SiteReviews\Tests;

use Faker\Factory;
use GeminiLabs\SiteReviews\Modules\Email;
use WP_UnitTestCase;

/**
 * Test case for the Emails.
 * @group email
 */
class EmailTest extends WP_UnitTestCase
{
    use Setup;

    protected $review;

    public function setUp()
    {
        parent::setUp();
        $faker = Factory::create();
        $this->review = [
            'content' => $faker->text,
            'email' => $faker->email,
            'name' => $faker->name,
            'rating' => '5',
            'title' => $faker->sentence,
        ];
    }

    public function test_email()
    {
        $args = [
            'to' => 'test@wordpress.dev',
            'subject' => 'Test Email',
            'template' => 'default',
            'template-tags' => [
                'review_author' => $this->review['name'],
                'review_content' => $this->review['content'],
                'review_email' => $this->review['email'],
                'review_ip' => '127.0.0.1',
                'review_link' => 'http://...',
                'review_rating' => $this->review['rating'],
                'review_title' => $this->review['title'],
            ],
        ];
        $sent = glsr(Email::class)->compose($args)->send();
        $this->assertEquals($sent, 1);
    }
}
