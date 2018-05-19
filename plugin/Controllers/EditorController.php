<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Editor\Customization;
use GeminiLabs\SiteReviews\Modules\Editor\Labels;
use GeminiLabs\SiteReviews\Modules\Editor\Metaboxes;
use WP_Post;
use WP_Screen;

class EditorController extends Controller
{
	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 */
	public function customizePostStatusLabels()
	{
		glsr( Labels::class )->customizePostStatusLabels();
	}

	/**
	 * @return array
	 * @filter wp_editor_settings
	 */
	public function filterEditorSettings( array $settings )
	{
		return glsr( Customization::class )->filterEditorSettings( $settings );
	}

	/**
	 * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script
	 * @param string $html
	 * @return string
	 * @filter the_editor
	 */
	public function filterEditorTextarea( $html )
	{
		return glsr( Customization::class )->filterEditorTextarea( $html );
	}

	/**
	 * @param string $translation
	 * @param string $test
	 * @param string $domain
	 * @return string
	 * @filter gettext
	 */
	public function filterPostStatusLabels( $translation, $text, $domain )
	{
		return glsr( Labels::class )->filterPostStatusLabels( $translation, $text, $domain );
	}

	/**
	 * @param string $translation
	 * @param string $test
	 * @param string $domain
	 * @return string
	 * @filter gettext_with_context
	 */
	public function filterPostStatusLabelsWithContext( $translation, $text, $context, $domain )
	{
		return glsr( Labels::class )->filterPostStatusLabels( $translation, $text, $domain );
	}

	/**
	 * @return array
	 * @filter post_updated_messages
	 */
	public function filterUpdateMessages( array $messages )
	{
		return glsr( Labels::class )->filterUpdateMessages( $messages );
	}

	/**
	 * @param array $postData
	 * @param array $meta
	 * @param int $postId
	 * @return void
	 * @action site-reviews/create/review
	 */
	public function onCreateReview( $postData, $meta, $postId )
	{
		glsr( Metaboxes::class )->onCreateReview( $postData, $meta, $postId );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action before_delete_post
	 */
	public function onDeleteReview( $postId )
	{
		glsr( Metaboxes::class )->onDeleteReview( $postId );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action save_post_.static::POST_TYPE
	 */
	public function onSaveReview( $postId, WP_Post $review )
	{
		glsr( Metaboxes::class )->onSaveReview( $postId, $review );
	}

	/**
	 * @param string $postType
	 * @return void
	 * @action add_meta_boxes
	 */
	public function registerMetaBoxes( $postType )
	{
		if( $postType != Application::POST_TYPE )return;
		add_meta_box( Application::ID.'_assigned_to', __( 'Assigned To', 'site-reviews' ), [$this, 'renderAssignedToMetabox'], null, 'side' );
		add_meta_box( Application::ID.'_review', __( 'Details', 'site-reviews' ), [$this, 'renderDetailsMetaBox'], null, 'side' );
		add_meta_box( Application::ID.'_response', __( 'Respond Publicly', 'site-reviews' ), [$this, 'renderResponseMetaBox'], null, 'normal' );
	}

	/**
	 * @return void
	 * @action admin_print_scripts
	 */
	public function removeAutosave()
	{
		glsr( Customization::class )->removeAutosave();
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function removeMetaBoxes()
	{
		glsr( Customization::class )->removeMetaBoxes();
	}

	/**
	 * @return void
	 * @callback add_meta_box
	 */
	public function renderAssignedToMetabox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		$assignedTo = intval( get_post_meta( $post->ID, 'assigned_to', true ));
		wp_nonce_field( 'assigned_to', '_nonce-assigned-to', false );
		glsr()->render( 'edit/metabox-assigned-to', [
			'id' => $assignedTo,
			'template' => $this->buildAssignedToTemplate( $assignedTo ),
		]);
	}

	/**
	 * @return void
	 * @callback add_meta_box
	 */
	public function renderDetailsMetaBox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		$review = glsr( Database::class )->getReview( $post );
		glsr()->render( 'edit/metabox-details', [
			'button' => $this->buildDetailsMetaBoxRevertButton( $review, $post ),
			'metabox' => $this->normalizeDetailsMetaBox( $review ),
		]);
	}

	/**
	 * @return void
	 * @action post_submitbox_misc_actions
	 */
	public function renderPinnedInPublishMetaBox()
	{
		if( !$this->isReviewPostType( get_post() ))return;
		glsr()->render( 'edit/pinned', [
			'pinned' => boolval( get_post_meta( intval( get_the_ID() ), 'pinned', true )),
		]);
	}

	/**
	 * @return void
	 * @callback add_meta_box
	 */
	public function renderResponseMetaBox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		wp_nonce_field( 'response', '_nonce-response', false );
		glsr()->render( 'edit/metabox-response', [
			'response' => glsr( Database::class )->getReview( $post )->response,
		]);
	}

	/**
	 * @return void
	 * @see glsr_categories_meta_box()
	 * @callback register_taxonomy
	 */
	public function renderTaxonomyMetabox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		glsr()->render( 'edit/metabox-categories', [
			'post' => $post,
			'tax_name' => Application::TAXONOMY,
			'taxonomy' => get_taxonomy( Application::TAXONOMY ),
		]);
	}

	/**
	 * @return void
	 * @see $this->filterUpdateMessages()
	 * @action admin_action_revert
	 */
	public function revertReview()
	{
		check_admin_referer( 'revert-review_'.( $postId = $this->getPostId() ));
		glsr( Database::class )->revertReview( $postId );
		$this->redirect( $postId, 52 );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action save_post_.Application::POST_TYPE
	 */
	public function saveMetaboxes( $postId )
	{
		glsr( Metaboxes::class )->saveAssignedToMetabox( $postId );
		glsr( Metaboxes::class )->saveResponseMetabox( $postId );
	}

	/**
	 * @param int $assignedTo
	 * @return string
	 */
	protected function buildAssignedToTemplate( $assignedTo )
	{
		$assignedPost = get_post( $assignedTo );
		if( !( $assignedPost instanceof WP_Post ))return;
		return glsr( Html::class )->buildTemplate( 'edit/assigned-post', [
			'context' => [
				'url' => (string)get_permalink( $assignedPost ),
				'title' => get_the_title( $assignedPost ),
			],
		]);
	}

	/**
	 * @param object $review
	 * @return string
	 */
	protected function buildDetailsMetaBoxRevertButton( $review, WP_Post $post )
	{
		$isModified = !glsr( Helper::class )->compareArrays(
			[$review->title, $review->content, $review->date],
			[$post->post_title, $post->post_content, $post->post_date]
		);
		if( $isModified ) {
			$revertUrl = wp_nonce_url( admin_url( 'post.php?post='.$post->ID.'&action=revert' ),
				'revert-review_'.$post->ID
			);
			return glsr( Builder::class )->a( __( 'Revert Changes', 'site-reviews' ), [
				'class' => 'button button-large',
				'href' => $revertUrl,
				'id' => 'revert',
			]);
		}
		return glsr( Builder::class )->button( __( 'Nothing to Revert', 'site-reviews' ), [
			'class' => 'button button-large',
			'disabled' => true,
			'id' => 'revert',
		]);
	}

	/**
	 * @param object $review
	 * @return string
	 */
	protected function getReviewType( $review )
	{
		$reviewType = $review->review_type;
		if( !empty( $review->url )) {
			$reviewType = glsr( Builder::class )->a( $reviewType, [
				'href' => $review->url,
				'target' => '_blank',
			]);
		}
		return in_array( $reviewType, glsr()->reviewTypes )
			? glsr()->reviewTypes[$reviewType]
			: __( 'Unknown', 'site-reviews' );
	}

	/**
	 * @param mixed $post
	 * @return bool
	 */
	protected function isReviewPostType( $post )
	{
		return $post instanceof WP_Post && $post->post_type == Application::POST_TYPE;
	}

	/**
	 * @param object $review
	 * @return array
	 */
	protected function normalizeDetailsMetaBox( $review )
	{
		$reviewer = empty( $review->user_id )
			? __( 'Unregistered user', 'site-reviews' )
			: glsr( Builder::class )->a( get_the_author_meta( 'display_name', $review->user_id ), [
				'href' => get_author_posts_url( $review->user_id ),
			]);
		$email = empty( $review->email )
			? '&mdash;'
			: glsr( Builder::class )->a( $review->email, [
				'href' => 'mailto:'.$review->email.'?subject='.esc_attr( __( 'RE:', 'site-reviews' ).' '.$review->title ),
			]);
		$metabox = [
			__( 'Rating', 'site-reviews' ) => glsr( Html::class )->renderPartial( 'star-rating', ['rating' => $review->rating] ),
			__( 'Type', 'site-reviews' ) => $this->getReviewType( $review ),
			__( 'Date', 'site-reviews' ) => get_date_from_gmt( $review->date, 'F j, Y' ),
			__( 'Reviewer', 'site-reviews' ) => $reviewer,
			__( 'Name', 'site-reviews' ) => $review->author,
			__( 'Email', 'site-reviews' ) => $email,
			__( 'IP Address', 'site-reviews' ) => $review->ip_address,
			__( 'Avatar', 'site-reviews' ) => sprintf( '<img src="%s" width="96">', $review->avatar ),
		];
		return apply_filters( 'site-reviews/metabox/details', $metabox, $review );
	}

	/**
	 * @param int $postId
	 * @param int $messageIndex
	 * @return void
	 */
	protected function redirect( $postId, $messageIndex )
	{
		$referer = wp_get_referer();
		$hasReferer = !$referer
			|| strpos( $referer, 'post.php' ) !== false
			|| strpos( $referer, 'post-new.php' ) !== false;
		$redirectUri = $hasReferer
			? remove_query_arg( ['deleted', 'ids', 'trashed', 'untrashed'], $referer )
			: get_edit_post_link( $postId, false );
		wp_safe_redirect( add_query_arg( ['message' => $messageIndex], $redirectUri ));
		exit;
	}
}
