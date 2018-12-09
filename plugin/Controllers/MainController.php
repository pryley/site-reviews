<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\RestCategoryController;
use GeminiLabs\SiteReviews\Controllers\RestReviewController;

class MainController extends Controller
{
	/**
	 * @return void
	 * @action init
	 */
	public function registerPostType()
	{
		if( !glsr()->hasPermission() )return;
		$command = new RegisterPostType([
			'capabilities'=> ['create_posts' => 'create_'.Application::POST_TYPE],
			'columns' => [
				'title' => '',
				'category' => '',
				'assigned_to' => __( 'Assigned To', 'site-reviews' ),
				'reviewer' => __( 'Author', 'site-reviews' ),
				'review_type' => __( 'Type', 'site-reviews' ),
				'rating' => __( 'Rating', 'site-reviews' ),
				'pinned' => __( 'Pinned', 'site-reviews' ),
				'date' => '',
			],
			'menu_icon' => 'dashicons-star-half',
			'menu_name' => glsr()->name,
			'map_meta_cap' => true,
			'plural' => __( 'Reviews', 'site-reviews' ),
			'post_type' => Application::POST_TYPE,
			'rest_controller_class' => RestReviewController::class,
			'show_in_rest' => true,
			'single' => __( 'Review', 'site-reviews' ),
		]);
		$this->execute( $command );
	}

	/**
	 * @return void
	 * @action init
	 */
	public function registerShortcodes()
	{
		$command = new RegisterShortcodes([
			'site_reviews',
			'site_reviews_form',
			'site_reviews_summary',
		]);
		$this->execute( $command );
	}

	/**
	 * @return void
	 * @action init
	 */
	public function registerTaxonomy()
	{
		$command = new RegisterTaxonomy([
			'hierarchical' => true,
			'meta_box_cb' => [glsr( EditorController::class ), 'renderTaxonomyMetabox'],
			'public' => false,
			'rest_controller_class' => RestCategoryController::class,
			'show_admin_column' => true,
			'show_in_rest' => true,
			'show_ui' => true,
		]);
		$this->execute( $command );
	}

	/**
	 * @return void
	 * @action widgets_init
	 */
	public function registerWidgets()
	{
		$command = new RegisterWidgets([
			'site-reviews' => [
				'class' => 'glsr-widget glsr-widget-site-reviews',
				'description' => __( 'Your site’s most recent reviews.', 'site-reviews' ),
				'title' => __( 'Recent Reviews', 'site-reviews' ),
			],
			'site-reviews-form' => [
				'class' => 'glsr-widget glsr-widget-site-reviews-form',
				'description' => __( 'A "submit a review" form for your site.', 'site-reviews' ),
				'title' => __( 'Submit a Review', 'site-reviews' ),
			],
			'site-reviews-summary' => [
				'class' => 'glsr-widget glsr-widget-site-reviews-summary',
				'description' => __( 'A summary of your site’s reviews.', 'site-reviews' ),
				'title' => __( 'Summary of Reviews', 'site-reviews' ),
			],
		]);
		$this->execute( $command );
	}
}
