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
        glsr(OptionManager::class)->set(glsr(DefaultsManager::class)->get());
		$this->review = [
			'_action' => 'submit-review',
            '_nonce' => wp_create_nonce('submit-review'),
			'_post_id' => '13',
			'_referer' => $PHP_SELF,
			'content' => 'abcdefg',
			'email' => 'jane@doe.com',
            'excluded' => '[]',
			'form_id' => 'abcdef',
			'name' => 'Jane Doe',
			'rating' => '5',
			'terms' => '1',
			'title' => 'Test Review',
		];
		// save initial plugin settings here if needed
	}
}
