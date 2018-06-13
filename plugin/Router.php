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
		$this->routeRequest( 'admin', $request['action'], $request );
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
		$this->routeRequest( 'ajax', $request['action'], $request );
		wp_die();
	}

	/**
	 * @return void
	 */
	public function routePublicPostRequest()
	{
		$request = filter_input( INPUT_POST, Application::ID, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if( !isset( $request['action'] ))return;
		if( !wp_verify_nonce( $request['_wpnonce'], $request['action'] )) {
			glsr_log()->error( 'Nonce check failed for public request' )->info( $request );
			return;
		}
		$request = $this->normalize( $request );
		$this->routeRequest( 'public', $request['action'], $request );
	}

	/**
	 * @param string $type
	 * @param string $action
	 * @return void
	 */
	public function routeRequest( $type, $action, array $request = [] )
	{
		$controller = glsr( glsr( Helper::class )->buildClassName( $type.'-controller', 'Controllers' ));
		$method = glsr( Helper::class )->buildMethodName( $action, 'router' );
		if( is_callable( [$controller, $method] )) {
			call_user_func( [$controller, $method], $request );
			return;
		}
		$actionHook = 'site-reviews/route/'.$type.'/request';
		do_action( $actionHook, $action, $request );
		if( did_action( $actionHook ) === 0 ) {
			glsr_log( 'Unknown '.$type.' router request: '.$action );
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
	 * @todo verify the $action-options
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
