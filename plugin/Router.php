<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Helper;

class Router
{
	/**
	 * @return void
	 */
	public function routeAdminPostRequest()
	{
		$request = filter_input( INPUT_POST, Application::ID, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if( !isset( $request['action'] ))return;
		$this->checkNonce( $request['action'] );
		switch( $request['action'] ) {
			case 'clear-log':
				glsr( AdminController::class )->routerClearLog();
				break;
			case 'download-log':
				glsr( AdminController::class )->routerDownloadLog();
				break;
			case 'download-system-info':
				glsr( AdminController::class )->routerDownloadSystemInfo();
				break;
			case 'submit-review':
				glsr( PublicController::class )->routerSubmitReview( $request );
				break;
			default:
				do_action( 'site-reviews/route/admin/post/requests', $request['action'], $request );
		}
	}

	/**
	 * @return void
	 */
	public function routeAjaxRequest()
	{
		$request = $this->normalizeAjaxRequest();
		if( !wp_verify_nonce( $request['nonce'], $request['action'] )) {
			glsr_log()->error( 'Nonce check failed for ajax request' )->info( $request );
			wp_die( -1, 403 );
		}
		$controller = glsr( AjaxController::class );
		$method = glsr( Helper::class )->buildMethodName( $request['action'] );
		if( is_callable( [$controller, $method] )) {
			call_user_func( [$controller, $method], $request );
		}
		else {
			do_action( 'site-reviews/route/ajax/requests', $method, $request );
		}
		wp_die();
	}

	/**
	 * @return void
	 */
	public function routePublicPostRequest()
	{
		switch( filter_input( INPUT_POST, 'action' )) {
			case 'submit-review':
				glsr( PublicController::class )->routerSubmitReview( $this->normalize( $_POST ));
				break;
		}
	}

	/**
	 * @return void
	 */
	public function routeWebhookRequest()
	{
		$request = filter_input( INPUT_GET, sprintf( '%s-hook', Application::ID ));
		if( !$request )return;
		// @todo manage webhook here
	}

	/**
	 * @param string $action
	 * @return void
	 */
	protected function checkNonce( $action )
	{
		$nonce = filter_input( INPUT_POST, 'option_page' ) == $action
			&& filter_input( INPUT_POST, 'action' ) == 'update'
			? $action.'-options'
			: $action;
		check_admin_referer( $nonce );
	}

	/**
	 * Undo damage done by javascript: encodeURIComponent() and sanitize values
	 * @return array
	 */
	protected function normalize( array $request )
	{
		array_walk_recursive( $request, function( &$value ) {
			$value = stripslashes( $value );
		});
		return $request;
	}

	/**
	 * All ajax requests in the plugin are triggered by a single action hook
	 * Each route is determined by the request["action"]
	 * @return array|void
	 */
	protected function normalizeAjaxRequest()
	{
		$request = filter_input( INPUT_POST, 'request', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if( isset( $request[Application::ID]['action'] )) {
			$request = $request[Application::ID];
		}
		if( !isset( $request['action'] )) {
			glsr_log()->error( 'The AJAX request must include an action' )->info( $request );
			wp_die();
		}
		if( !isset( $request['nonce'] )) {
			glsr_log()->error( 'The AJAX request must include a nonce' )->info( $request );
			wp_die();
		}
		$request['ajax_request'] = true;
		return $this->normalize( $request );
	}
}
