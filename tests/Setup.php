<?php

/**
 * @package   GeminiLabs\SiteReviews\Tests
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Tests;

trait Setup
{
	public function setUp()
	{
		parent::setUp();

		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['SERVER_NAME']     = '';
		$PHP_SELF                   = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

		$this->app = glsr_app();
		$this->db  = glsr_resolve( 'Database' );

		$this->app->activate();

		$this->review = [
			'action'   => 'post-review',
			'content'  => 'abcdefg',
			'email'    => 'jane@doee.com',
			'excluded' => "[]",
			'form_id'  => 'abcdef',
			'name'     => 'Jane doe',
			'rating'   => '5',
			'terms'    => '1',
			'title'    => 'Test Review',
			'_wp_http_referer' => $PHP_SELF,
		];

		// save initial plugin settings here if needed
	}
}
