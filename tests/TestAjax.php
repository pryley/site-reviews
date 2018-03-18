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
use WP_Ajax_UnitTestCase;
use WPAjaxDieContinueException;
use WPAjaxDieStopException;

/**
 * Test case for the Ajax Controller
 *
 * @group ajax
 */
class TestAjax extends WP_Ajax_UnitTestCase
{
	use Setup;

	public function test_ajax_post_review()
	{
		$response = $this->ajax_response( $this->review );

		$this->assertFalse( $response->errors );
		$this->assertEquals( $response->message, 'Your review has been submitted!' );
	}

	protected function ajax_response( $request )
	{
		$_POST = [
			'_wpnonce' => wp_create_nonce( "{$this->app->id}-ajax-nonce" ),
			'request'  => $request,
		];

		try {
			$this->_handleAjax( "{$this->app->prefix}_action" );
		}
		catch( WPAjaxDieContinueException $e ) {
		}
		catch( WPAjaxDieStopException $e ) {
			error_log( print_r( 'WPAjaxDieStopException', 1 ) );
		}

		$response = json_decode( $this->_last_response );

		// Empty _last_response so we can call ajax_response more than once in the same method.
		$this->_last_response = '';

		$this->assertInternalType( 'object', $response );

		return $response;
	}

	protected function assert_json_error( $request )
	{
		$response = $this->ajax_response( $request );

		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );

		return $response;
	}

	protected function assert_json_success( $request )
	{
		$response = $this->ajax_response( $request );

		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertTrue( $response->success );

		return $response;
	}
}
