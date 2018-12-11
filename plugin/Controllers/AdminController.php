<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\SqlQueries;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\System;

class AdminController extends Controller
{
	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		$command = new EnqueueAdminAssets([
			'pointers' => [[
				'content' => __( 'You can pin exceptional reviews so that they are always shown first.', 'site-reviews' ),
				'id' => 'glsr-pointer-pinned',
				'position' => [
					'edge' => 'right',
					'align' => 'middle',
				],
				'screen' => Application::POST_TYPE,
				'target' => '#misc-pub-pinned',
				'title' => __( 'Pin Your Reviews', 'site-reviews' ),
			]],
		]);
		$this->execute( $command );
	}

	/**
	 * @return array
	 * @filter plugin_action_links_site-reviews/site-reviews.php
	 */
	public function filterActionLinks( array $links )
	{
		$links['settings'] = glsr( Builder::class )->a( __( 'Settings', 'site-reviews' ), [
			'href' => admin_url( 'edit.php?post_type='.Application::POST_TYPE.'&page=settings' ),
		]);
		return $links;
	}

	/**
	 * @return array
	 * @filter dashboard_glance_items
	 */
	public function filterDashboardGlanceItems( array $items )
	{
		$postCount = wp_count_posts( Application::POST_TYPE );
		if( empty( $postCount->publish )) {
			return $items;
		}
		$text = _n( '%s Review', '%s Reviews', $postCount->publish, 'site-reviews' );
		$text = sprintf( $text, number_format_i18n( $postCount->publish ));
		$items[] = current_user_can( get_post_type_object( Application::POST_TYPE )->cap->edit_posts )
			? glsr( Builder::class )->a( $text, [
				'class' => 'glsr-review-count',
				'href' => 'edit.php?post_type='.Application::POST_TYPE,
			])
			: glsr( Builder::class )->span( $text, [
				'class' => 'glsr-review-count',
			]);
		return $items;
	}

	/**
	 * @return array
	 * @filter mce_external_plugins
	 */
	public function filterTinymcePlugins( array $plugins )
	{
		if( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' )) {
			$plugins['glsr_shortcode'] = glsr()->url( 'assets/scripts/mce-plugin.js' );
		}
		return $plugins;
	}

	/**
	 * @return void
	 * @action admin_init
	 */
	public function registerTinymcePopups()
	{
		$command = new RegisterTinymcePopups([
			'site_reviews' => esc_html__( 'Recent Reviews', 'site-reviews' ),
			'site_reviews_form' => esc_html__( 'Submit a Review', 'site-reviews' ),
			'site_reviews_summary' => esc_html__( 'Summary of Reviews', 'site-reviews' ),
		]);
		$this->execute( $command );
	}

	/**
	 * @return null|void
	 * @action media_buttons
	 */
	public function renderTinymceButton()
	{
		if( glsr_current_screen()->base != 'post' )return;
		$shortcodes = [];
		foreach( glsr()->mceShortcodes as $shortcode => $values ) {
			$shortcodes[$shortcode] = $values;
		}
		if( empty( $shortcodes ))return;
		glsr()->render( 'partials/editor/tinymce', [
			'shortcodes' => $shortcodes,
		]);
	}

	/**
	 * @return void
	 */
	public function routerClearConsole()
	{
		glsr( Console::class )->clear();
		glsr( Notice::class )->addSuccess( __( 'Console cleared.', 'site-reviews' ));
	}

	/**
	 * @return void
	 */
	public function routerFetchConsole()
	{
		glsr( Notice::class )->addSuccess( __( 'Console reloaded.', 'site-reviews' ));
	}

	/**
	 * @param bool $showNotice
	 * @return void
	 */
	public function routerCountReviews( $showNotice = true )
	{
		$countManager = glsr( CountsManager::class );
		$terms = get_terms([
			'hide_empty' => true,
			'taxonomy' => Application::TAXONOMY,
		]);
		foreach( $terms as $term ) {
			$countManager->setTermCounts( $term->term_id, $countManager->buildTermCounts( $term->term_id ));
		}
		$postIds = glsr( SqlQueries::class )->getReviewsMeta( 'assigned_to' );
		foreach( $postIds as $postId ) {
			$countManager->setPostCounts( $postId, $countManager->buildPostCounts( $postId ));
		}
		$countManager->setCounts( $countManager->buildCounts() );
		if( $showNotice ) {
			glsr( Notice::class )->clear()->addSuccess( __( 'Recalculated rating counts.', 'site-reviews' ));
		}
		glsr( OptionManager::class )->set( 'last_review_count', current_time( 'timestamp' ));
	}

	/**
	 * @return void
	 */
	public function routerDownloadConsole()
	{
		$this->download( Application::ID.'-console.txt', glsr( Console::class )->get() );
	}

	/**
	 * @return void
	 */
	public function routerDownloadSystemInfo()
	{
		$this->download( Application::ID.'-system-info.txt', glsr( System::class )->get() );
	}

	/**
	 * @return void
	 */
	public function routerExportSettings()
	{
		$this->download( Application::ID.'-settings.json', glsr( OptionManager::class )->json() );
	}

	/**
	 * @return void
	 */
	public function routerImportSettings()
	{
		$file = $_FILES['import-file'];
		if( $file['error'] !== UPLOAD_ERR_OK ) {
			return glsr( Notice::class )->addError( $this->getUploadError( $file['error'] ));
		}
		if( $file['type'] !== 'application/json' || !glsr( Helper::class )->endsWith( '.json', $file['name'] )) {
			return glsr( Notice::class )->addError( __( 'Please use a valid Site Reviews settings file.', 'site-reviews' ));
		}
		$settings = json_decode( file_get_contents( $file['tmp_name'] ), true );
		if( empty( $settings )) {
			return glsr( Notice::class )->addWarning( __( 'There were no settings found to import.', 'site-reviews' ));
		}
		glsr( OptionManager::class )->set( glsr( OptionManager::class )->normalize( $settings ));
		glsr( Notice::class )->addSuccess( __( 'Settings imported.', 'site-reviews' ));
	}

	/**
	 * @param int $errorCode
	 * @return string
	 */
	protected function getUploadError( $errorCode )
	{
		$errors = [
			UPLOAD_ERR_INI_SIZE => __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'site-reviews' ),
			UPLOAD_ERR_FORM_SIZE => __( 'The uploaded file is too big.', 'site-reviews' ),
			UPLOAD_ERR_PARTIAL => __( 'The uploaded file was only partially uploaded.', 'site-reviews' ),
			UPLOAD_ERR_NO_FILE => __( 'No file was uploaded.', 'site-reviews' ),
			UPLOAD_ERR_NO_TMP_DIR => __( 'Missing a temporary folder.', 'site-reviews' ),
			UPLOAD_ERR_CANT_WRITE => __( 'Failed to write file to disk.', 'site-reviews' ),
			UPLOAD_ERR_EXTENSION => __( 'A PHP extension stopped the file upload.', 'site-reviews' ),
		];
		return !isset( $errors[$errorCode] )
			? __( 'Unknown upload error.', 'site-reviews' )
			: $errors[$errorCode];
	}
}
