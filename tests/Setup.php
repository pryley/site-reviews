<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;

trait Setup
{
	public function setUp()
	{
		parent::setUp();
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['SERVER_NAME'] = '';
		$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';
		glsr()->activate();
		glsr( OptionManager::class )->set( glsr( DefaultsManager::class )->get() );
		$this->review = [
			'action' => 'submit-review',
			'content' => 'abcdefg',
			'email' => 'jane@doe.com',
			'excluded' => "[]",
			'form_id' => 'abcdef',
			'name' => 'Jane Doe',
			'nonce' => wp_create_nonce( 'submit-review' ),
			'rating' => '5',
			'terms' => '1',
			'title' => 'Test Review',
			'referer' => $PHP_SELF,
		];
		// save initial plugin settings here if needed
	}
}
