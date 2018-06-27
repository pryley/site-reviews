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
		check_admin_referer( $request['action'] );
		$this->routeRequest( 'admin', $request['action'], $request );
	}

	/**
	 * @return void
	 */
	public function routeAjaxRequest()
	{
		$request = $this->getRequest();
		$this->checkAjaxRequest( $request );
		$this->checkAjaxNonce( $request );
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
		if( !$this->isValidPublicNonce( $request ))return;
		$this->routeRequest( 'public', $request['action'], $request );
	}

	/**
	 * @return void
	 */
	protected function checkAjaxNonce( array $request )
	{
		if( !is_user_logged_in() )return;
		if( !isset( $request['nonce'] )) {
			glsr_log()->error( 'The AJAX request must include a nonce' )->info( $request );
			wp_die();
		}
		if( !wp_verify_nonce( $request['nonce'], $request['action'] )) {
			glsr_log()->error( 'Nonce check failed for ajax request' )->info( $request );
			wp_die( -1, 403 );
		}
	}

	/**
	 * @return void
	 */
	protected function checkAjaxRequest( array $request )
	{
		if( !isset( $request['action'] )) {
			glsr_log()->error( 'The AJAX request must include an action' )->info( $request );
			wp_die();
		}
		if( empty( $request['ajax_request'] )) {
			glsr_log()->error( 'The AJAX request look invalid' )->info( $request );
			wp_die();
		}
	}

	/**
	 * All ajax requests in the plugin are triggered by a single action hook: glsr_action,
	 * while each route is determined by $_POST[request][action]
	 * @return array
	 */
	protected function getRequest()
	{
		$request = glsr( Helper::class )->filterInput( 'action' ) == Application::PREFIX.'action'
			? glsr( Helper::class )->filterInputArray( 'request' )
			: glsr( Helper::class )->filterInputArray( Application::ID );
		return $this->normalizeRequest( $request );
	}

	/**
	 * @return bool
	 */
	protected function isValidPostRequest( array $request = [] )
	{
		return !empty( $request['action'] ) && empty( $request['ajax_request'] );
	}

	/**
	 * @return bool
	 */
	protected function isValidPublicNonce( array $request )
	{
		if( is_user_logged_in() && !wp_verify_nonce( $request['nonce'], $request['action'] )) {
			glsr_log()->error( 'Nonce check failed for public request' )->info( $request );
			return false;
		}
		return true;
	}

	/**
	 * @return array
	 */
	protected function normalizeRequest( array $request )
	{
		if( glsr( Helper::class )->filterInput( 'action' ) == Application::PREFIX.'action' ) {
			$request['ajax_request'] = true;
		}
		if( glsr( Helper::class )->filterInput( 'action', $request ) == 'submit-review' ) {
			$request['recaptcha-token'] = glsr( Helper::class )->filterInput( 'g-recaptcha-response' );
		}
		return $request;
	}

	/**
	 * @param string $type
	 * @param string $action
	 * @return void
	 */
	protected function routeRequest( $type, $action, array $request = [] )
	{
		$actionHook = 'site-reviews/route/'.$type.'/request';
		$controller = glsr( glsr( Helper::class )->buildClassName( $type.'-controller', 'Controllers' ));
		$method = glsr( Helper::class )->buildMethodName( $action, 'router' );
		$request = apply_filters( 'site-reviews/route/request', $request, $action, $type );
		do_action( $actionHook, $action, $request );
		if( is_callable( [$controller, $method] )) {
			call_user_func( [$controller, $method], $request );
			return;
		}
		if( did_action( $actionHook ) === 0 ) {
			glsr_log( 'Unknown '.$type.' router request: '.$action );
		}
	}
}
