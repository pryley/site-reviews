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
		$request = $this->getRequest();
		if( !$this->isValidPostRequest( $request ))return;
		$this->checkAdminNonce( $request['action'] );
		$this->routeRequest( 'admin', $request['action'], $request );
	}

	/**
	 * All ajax requests in the plugin are triggered by a single action hook (i.e. "glsr_action")
	 * Each route is determined by the request["action"]
	 * @return void
	 */
	public function routeAjaxRequest()
	{
		$request = $this->getRequest();
		if( !isset( $request['action'] )) {
			glsr_log()->error( 'The AJAX request must include an action' )->info( $request );
			wp_die();
		}
		if( !isset( $request['nonce'] )) {
			glsr_log()->error( 'The AJAX request must include a nonce' )->info( $request );
			wp_die();
		}
		if( !wp_verify_nonce( $request['nonce'], $request['action'] )) {
			glsr_log()->error( 'Nonce check failed for ajax request' )->info( $request );
			wp_die( -1, 403 );
		}
		$request['ajax_request'] = true;
		$this->routeRequest( 'ajax', $request['action'], $request );
		wp_die();
	}

	/**
	 * @return void
	 */
	public function routePublicPostRequest()
	{
		if( is_admin() )return;
		$request = $this->getRequest();
		if( !$this->isValidPostRequest( $request ))return;
		if( !wp_verify_nonce( $request['_wpnonce'], $request['action'] )) {
			glsr_log()->error( 'Nonce check failed for public request' )->info( $request );
			return;
		}
		$this->routeRequest( 'public', $request['action'], $request );
	}

	/**
	 * @return void
	 */
	public function routeWebhookRequest()
	{
		$request = filter_input( INPUT_GET, Application::PREFIX.'hook' );
		if( !$request )return;
		// @todo manage webhook here
	}

	/**
	 * @param string $action
	 * @return void
	 * @todo verify the $action-options
	 */
	protected function checkAdminNonce( $action )
	{
		$nonce = filter_input( INPUT_POST, 'option_page' ) == $action
			&& filter_input( INPUT_POST, 'action' ) == 'update'
			? $action.'-options'
			: $action;
		check_admin_referer( $nonce );
	}

	/**
	 * @return array
	 */
	protected function getRequest()
	{
		foreach( ['request', Application::ID] as $key ) {
			$request = filter_input( INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if( !empty( $request ))break;
		}
		if( isset( $request[Application::ID]['action'] )) {
			$request = $request[Application::ID];
		}
		return (array)$request;
	}

	/**
	 * @return bool
	 */
	protected function isValidPostRequest( array $request = [] )
	{
		return !empty( $request['action'] ) && empty( filter_input( INPUT_POST, 'ajax_request' ));
	}

	/**
	 * @param string $type
	 * @param string $action
	 * @return void
	 */
	protected function routeRequest( $type, $action, array $request = [] )
	{
		$controller = glsr( glsr( Helper::class )->buildClassName( $type.'-controller', 'Controllers' ));
		$method = glsr( Helper::class )->buildMethodName( $action, 'router' );
		$request = apply_filters( 'site-reviews/route/request', $request, $action, $type );
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
}
