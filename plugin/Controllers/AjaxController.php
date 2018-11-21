<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ChangeStatus;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Session;
use GeminiLabs\SiteReviews\Modules\Translation;
use WP_Query;

class AjaxController extends Controller
{
	/**
	 * @return void
	 */
	public function routerChangeStatus( array $request )
	{
		wp_send_json_success( $this->execute( new ChangeStatus( $request )));
	}

	/**
	 * @return void
	 */
	public function routerClearConsole()
	{
		glsr( AdminController::class )->routerClearConsole();
		wp_send_json_success([
			'console' => glsr( Console::class )->get(),
			'notices' => glsr( Notice::class )->get(),
		]);
	}

	/**
	 * @return void
	 */
	public function routerCountReviews()
	{
		glsr( AdminController::class )->routerCountReviews();
		wp_send_json_success([
			'notices' => glsr( Notice::class )->get(),
		]);
	}

	/**
	 * @return void
	 */
	public function routerMceShortcode( array $request )
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
		wp_send_json_success( $response );
	}

	/**
	 * @return void
	 */
	public function routerFetchConsole()
	{
		glsr( AdminController::class )->routerFetchConsole();
		wp_send_json_success([
			'console' => glsr( Console::class )->get(),
			'notices' => glsr( Notice::class )->get(),
		]);
	}

	/**
	 * @return void
	 */
	public function routerSearchPosts( array $request )
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
	public function routerSearchTranslations( array $request )
	{
		if( empty( $request['exclude'] )) {
			$request['exclude'] = [];
		}
		$results = glsr( Translation::class )
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
	public function routerSubmitReview( array $request )
	{
		glsr( PublicController::class )->routerSubmitReview( $request );
		$data = [
			'errors' => glsr( Session::class )->get( $request['form_id'].'errors', false, true ),
			'message' => glsr( Session::class )->get( $request['form_id'].'message', '', true ),
			'recaptcha' => glsr( Session::class )->get( $request['form_id'].'recaptcha', false, true ),
			'redirect' => trim( strval( get_post_meta( intval( $request['_post_id'] ), 'redirect_to', true ))),
		];
		if( $data['errors'] === false ) {
			glsr( Session::class )->clear();
			wp_send_json_success( $data );
		}
		wp_send_json_error( $data );
	}

	/**
	 * @return void
	 */
	public function routerTogglePinned( array $request )
	{
		$isPinned = $this->execute( new TogglePinned( $request ));
		wp_send_json_success([
			'notices' => glsr( Notice::class )->get(),
			'pinned' => $isPinned,
		]);
	}
}
