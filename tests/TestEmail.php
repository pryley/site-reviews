<?php

/**
 * @package   GeminiLabs\SiteReviews\Tests
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Tests\Setup;
use WP_UnitTestCase;

/**
 * Test case for the Emails
 *
 * @group email
 */
class TestEmail extends WP_UnitTestCase
{
	use Setup;

	public function test_email()
	{
		$args = [
			'to' => 'test@wordpress.dev',
			'subject'  => 'Test Email',
			'template' => 'review-notification',
			'template-tags' => [
				'review_author'  => $this->review['name'],
				'review_content' => $this->review['content'],
				'review_email'   => $this->review['email'],
				'review_ip'      => '127.0.0.1',
				'review_link'    => 'http://...',
				'review_rating'  => $this->review['rating'],
				'review_title'   => $this->review['title'],
			],
		];

		$sent = $this->app->make( 'Email' )->compose( $args )->send();

		$this->assertEquals( $sent, 1 );
	}
}
