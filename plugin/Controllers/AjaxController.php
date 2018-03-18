<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Commands\ChangeStatus;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use WP_Query;

class AjaxController extends BaseController
{
	/**
	 * Change the assigned Post ID of a review
	 */
	public function ajaxChangeAssignedTo( $request )
	{
		wp_send_json( $this->html->renderPartial( 'link', [
			'post_id' => $request['ID'],
		]));
	}

	/**
	 * Change a review status
	 *
	 * @since 2.0.0
	 */
	public function ajaxChangeReviewStatus( $request )
	{
		$response = $this->execute( new ChangeStatus( $request ));
		wp_send_json( $response );
	}

	/**
	 * Clears the log
	 */
	public function ajaxClearLog()
	{
		$this->app->make( 'Controllers\MainController' )->postClearLog();
		wp_send_json([
			'log' => __( 'Log is empty', 'site-reviews' ),
			'notices' => $this->notices->show( false ),
		]);
	}

	/**
	 * Dismisses the outdated notice
	 */
	public function ajaxDismissOutdatedNotice()
	{
		$this->db->setOption( 'upgrade_notice_dismissed', true );
		wp_send_json_success();
	}

	/**
	 * Load the shortcode dialog fields
	 *
	 * @param array $request
	 */
	public function ajaxMceShortcode( $request )
	{
		$shortcode = $request['shortcode'];
		$response = false;
		if( array_key_exists( $shortcode, glsr_app()->mceShortcodes )) {
			$data = glsr_app()->mceShortcodes[ $shortcode ];
			if( !empty( $data['errors'] )) {
				$data['btn_okay'] = [ esc_html__( 'Okay', 'site-reviews' ) ];
			}
			$response = [
				'body'      => $data['fields'],
				'close'     => $data['btn_close'],
				'ok'        => $data['btn_okay'],
				'shortcode' => $shortcode,
				'title'     => $data['title'],
			];
		}
		wp_send_json( $response );
	}

	/**
	 * Submit a review
	 */
	public function ajaxPostReview( $request )
	{
		$response = $this->app->make( 'Controllers\ReviewController' )->postSubmitReview( $request );
		$session = $this->app->make( 'Session' );
		wp_send_json([
			'errors' => $session->get( "{$request['form_id']}-errors", false, true ),
			'message' => $response,
			'recaptcha' => $session->get( "{$request['form_id']}-recaptcha", false, true ),
		]);
	}

	/**
	 * Search available language strings
	 */
	public function ajaxSearchPosts( $request )
	{
		wp_send_json_success([
			'empty' => sprintf( '<div>%s</div>', __( 'Nothing found.', 'site-reviews' )),
			'items' => $this->db->searchPosts( $request['search'] ),
		]);
	}

	/**
	 * Search available language strings
	 */
	public function ajaxSearchTranslations( $request )
	{
		if( empty( $request['exclude'] )) {
			$request['exclude'] = [];
		}
		$results = $this->app->make( 'Translator' )
			->search( $request['search'] )
			->exclude()
			->exclude( $request['exclude'] )
			->renderResults();
		wp_send_json_success([
			'empty' => sprintf( '<div>%s</div>', __( 'Nothing found.', 'site-reviews' )),
			'items' => $results,
		]);
	}

	/**
	 * Toggle the pinned status of a review
	 */
	public function ajaxTogglePinned( $request )
	{
		$response = $this->execute( new TogglePinned( $request ));

		wp_send_json([
			'notices' => $this->notices->show( false ),
			'pinned' => (bool) $response,
		]);
	}
}
