<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets as Command;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class EnqueueAdminAssets
{
	/**
	 * @var array
	 */
	protected $pointers;

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$this->generatePointers( $command->pointers );
		$this->enqueueAssets();
		$this->localizeAssets();
	}

	/**
	 * @return void
	 */
	public function enqueueAssets()
	{
		wp_enqueue_style(
			Application::ID,
			glsr()->url( 'assets/styles/'.Application::ID.'-admin.css' ),
			[],
			glsr()->version
		);
		if( !$this->isCurrentScreen() )return;
		wp_enqueue_script(
			Application::ID,
			glsr()->url( 'assets/scripts/'.Application::ID.'-admin.js' ),
			$this->getDependencies(),
			glsr()->version,
			true
		);
		if( !empty( $this->pointers )) {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
		}
	}

	/**
	 * @return void
	 */
	public function localizeAssets()
	{
		$variables = [
			'action' => Application::PREFIX.'action',
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'hideoptions' => [
				'site_reviews' => glsr( SiteReviewsShortcode::class )->getHideOptions(),
				'site_reviews_form' => glsr( SiteReviewsFormShortcode::class )->getHideOptions(),
				'site_reviews_summary' => glsr( SiteReviewsSummaryShortcode::class )->getHideOptions(),
			],
			'nameprefix' => Application::ID,
			'nonce' => [
				'change-status' => wp_create_nonce( 'change-status' ),
				'clear-console' => wp_create_nonce( 'clear-console' ),
				'count-reviews' => wp_create_nonce( 'count-reviews' ),
				'fetch-console' => wp_create_nonce( 'fetch-console' ),
				'mce-shortcode' => wp_create_nonce( 'mce-shortcode' ),
				'sync-reviews' => wp_create_nonce( 'sync-reviews' ),
				'toggle-pinned' => wp_create_nonce( 'toggle-pinned' ),
			],
			'pointers' => $this->pointers,
			'shortcodes' => [],
			'tinymce' => [
				'glsr_shortcode' => glsr()->url( 'assets/scripts/mce-plugin.js' ),
			],
		];
		if( user_can_richedit() ) {
			$variables['shortcodes'] = $this->localizeShortcodes();
		}
		$variables = apply_filters( 'site-reviews/enqueue/admin/localize', $variables );
		wp_localize_script( Application::ID, 'GLSR', $variables );
	}

	/**
	 * @return array
	 */
	protected function getDependencies()
	{
		$dependencies = apply_filters( 'site-reviews/enqueue/admin/dependencies', [] );
		$dependencies = array_merge( $dependencies, [
			'jquery', 'jquery-ui-sortable', 'underscore', 'wp-util',
		]);
		return $dependencies;
	}

	/**
	 * @return array
	 */
	protected function generatePointer( array $pointer )
	{
		return [
			'id' => $pointer['id'],
			'options' => [
				'content' => '<h3>'.$pointer['title'].'</h3><p>'.$pointer['content'].'</p>',
				'position' => $pointer['position'],
			],
			'screen' => $pointer['screen'],
			'target' => $pointer['target'],
		];
	}

	/**
	 * @return void
	 */
	protected function generatePointers( array $pointers )
	{
		$dismissedPointers = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
		$dismissedPointers = explode( ',', (string)$dismissedPointers );
		$generatedPointers = [];
		foreach( $pointers as $pointer ) {
			if( $pointer['screen'] != glsr_current_screen()->id )continue;
			if( in_array( $pointer['id'], $dismissedPointers ))continue;
			$generatedPointers[] = $this->generatePointer( $pointer );
		}
		$this->pointers = $generatedPointers;
	}

	/**
	 * @return bool
	 */
	protected function isCurrentScreen()
	{
		$screen = glsr_current_screen();
		return $screen && ( $screen->post_type == Application::POST_TYPE
			|| $screen->base == 'post'
			|| $screen->id == 'dashboard'
			|| $screen->id == 'widgets'
		);
	}

	/**
	 * @return array
	 */
	protected function localizeShortcodes()
	{
		$variables = [];
		foreach( glsr()->mceShortcodes as $tag => $args ) {
			if( empty( $args['required'] ))continue;
			$variables[$tag] = $args['required'];
		}
		return $variables;
	}
}
