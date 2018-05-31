<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterPointers;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodeButtons;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Handlers\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Logger;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\System;
use WP_Post;

class AdminController extends Controller
{
	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		(new EnqueueAdminAssets)->handle();
	}

	/**
	 * @return array
	 * @filter plugin_action_links_site-reviews/site-reviews.php
	 */
	public function filterActionLinks( array $links )
	{
		$links[] = glsr( Builder::class )->a( __( 'Settings', 'site-reviews' ), [
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
		$postType = Application::POST_TYPE;
		$postCount = wp_count_posts( $postType );
		if( !isset( $postCount->publish ) || !$postCount->publish ) {
			return $items;
		}
		$postTypeObject = get_post_type_object( $postType );
		$text = _n( '%s Review', '%s Reviews', $postCount->publish, 'site-reviews' );
		$text = sprintf( $text, number_format_i18n( $postCount->publish ));
		$items[] = $postTypeObject && current_user_can( $postTypeObject->cap->edit_posts )
			? sprintf( '<a class="glsr-review-count" href="edit.php?post_type=%s">%s</a>', $postType, $text )
			: sprintf( '<span class="glsr-review-count">%s</span>', $text );
		return $items;
	}

	/**
	 * @return array
	 * @filter mce_external_plugins
	 */
	public function filterTinymcePlugins( array $plugins )
	{
		if( user_can_richedit()
			&& ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ))) {
			$plugins['glsr_shortcode'] = glsr()->url( 'assets/scripts/mce-plugin.js' );
		}
		return $plugins;
	}

	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 */
	public function registerPointers()
	{
		$command = new RegisterPointers([[
			'content' => __( 'You can pin exceptional reviews so that they are always shown first.', 'site-reviews' ),
			'id' => 'glsr-pointer-pinned',
			'position' => [
				'edge' => 'right',
				'align' => 'middle',
			],
			'screen' => Application::POST_TYPE,
			'target' => '#misc-pub-pinned',
			'title' => __( 'Pin Your Reviews', 'site-reviews' ),
		]]);
		$this->execute( $command );
	}

	/**
	 * @return void
	 * @action admin_init
	 */
	public function registerShortcodeButtons()
	{
		$command = new RegisterShortcodeButtons([
			'site_reviews' => esc_html__( 'Recent Reviews', 'site-reviews' ),
			'site_reviews_form' => esc_html__( 'Submit a Review', 'site-reviews' ),
			'site_reviews_summary' => esc_html__( 'Summary of Reviews', 'site-reviews' ),
		]);
		$this->execute( $command );
	}

	/**
	 * @return void
	 * @action edit_form_after_title
	 */
	public function renderReviewEditor( WP_Post $post )
	{
		if( !$this->isReviewEditable( $post ) )return;
		glsr()->render( 'partials/editor/review', [
			'post' => $post,
		]);
	}

	/**
	 * @return void
	 * @action edit_form_top
	 */
	public function renderReviewNotice( WP_Post $post )
	{
		if( !$this->isReviewEditable( $post ) )return;
		glsr( Notice::class )->addWarning( __( 'This review is read-only.', 'site-reviews' ));
		glsr()->render( 'partials/editor/notice' );
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
			if( !apply_filters( sanitize_title( $shortcode ).'_condition', true ))continue;
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
	public function routerClearLog()
	{
		glsr( Logger::class )->clear();
		glsr( Notice::class )->addSuccess( __( 'Log cleared.', 'site-reviews' ));
	}

	/**
	 * @return void
	 */
	public function routerDownloadLog()
	{
		$this->download( Application::ID.'-log.txt', glsr( Logger::class )->get() );
	}

	/**
	 * @return void
	 */
	public function routerDownloadSystemInfo()
	{
		$this->download( Application::ID.'-system-info.txt', glsr( System::class )->get() );
	}

	/**
	 * @return bool
	 */
	protected function isReviewEditable( WP_Post $post )
	{
		return $post->post_type == Application::POST_TYPE
			&& post_type_supports( Application::POST_TYPE, 'title' )
			&& get_post_meta( $post->ID, 'review_type', true ) == 'local';
	}
}
