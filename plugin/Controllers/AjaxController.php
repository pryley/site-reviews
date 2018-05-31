<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ChangeStatus;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Logger;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Session;
use GeminiLabs\SiteReviews\Modules\Translator;
use WP_Query;

class AjaxController extends Controller
{
	/**
	 * @return void
	 */
	public function changeAssignedTo( array $request )
	{
		wp_send_json( glsr( Html::class )->renderPartial( 'link', [
			'post_id' => $request['ID'],
		]));
	}

	/**
	 * @return void
	 */
	public function changeReviewStatus( array $request )
	{
		wp_send_json( $this->execute( new ChangeStatus( $request )));
	}

	/**
	 * @return void
	 */
	public function clearLog()
	{
		glsr( AdminController::class )->routerClearLog();
		wp_send_json([
			'logger' => glsr( Logger::class )->get(),
			'notices' => glsr( Notice::class )->get(),
		]);
	}

	/**
	 * @return void
	 */
	public function mceShortcode( array $request )
	{
		$shortcode = $request['shortcode'];
		$response = false;
		if( array_key_exists( $shortcode, glsr()->mceShortcodes )) {
			$data = glsr()->mceShortcodes[$shortcode];
			if( !empty( $data['errors'] )) {
				$data['btn_okay'] = [esc_html__( 'Okay', 'site-reviews' )];
			}
			$response = [
				'body' => $data['fields'],
				'close' => $data['btn_close'],
				'ok' => $data['btn_okay'],
				'shortcode' => $shortcode,
				'title' => $data['title'],
			];
		}
		wp_send_json( $response );
	}

	/**
	 * @return void
	 */
	public function searchPosts( array $request )
	{
		$results = glsr( Database::class )->searchPosts( $request['search'] );
		wp_send_json_success([
			'empty' => '<div>'.__( 'Nothing found.', 'site-reviews' ).'</div>',
			'items' => $results,
		]);
	}

	/**
	 * @return void
	 */
	public function searchTranslations( array $request )
	{
		if( empty( $request['exclude'] )) {
			$request['exclude'] = [];
		}
		$results = glsr( Translator::class )
			->search( $request['search'] )
			->exclude()
			->exclude( $request['exclude'] )
			->renderResults();
		wp_send_json_success([
			'empty' => '<div>'.__( 'Nothing found.', 'site-reviews' ).'</div>',
			'items' => $results,
		]);
	}

	/**
	 * @return void
	 */
	public function submitReview( array $request )
	{
		$response = glsr( PublicController::class )->routerCreateReview( $request );
		$session = glsr( Session::class );
		wp_send_json([
			'errors' => $session->get( $request['form_id'].'-errors', false, true ),
			'message' => $response,
			'recaptcha' => $session->get( $request['form_id'].'-recaptcha', false, true ),
		]);
	}

	/**
	 * @return void
	 */
	public function togglePinned( array $request )
	{
		wp_send_json([
			'notices' => glsr( Notice::class )->get(),
			'pinned' => $this->execute( new TogglePinned( $request )),
		]);
	}
}
