<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Email;
use WP_UnitTestCase;

/**
 * Test case for the Emails.
 * @group email
 */
class TestEmail extends WP_UnitTestCase
{
    use Setup;

    public function test_email()
    {
        $args = [
            'to' => 'test@wordpress.dev',
            'subject' => 'Test Email',
            'template' => 'email-notification',
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
