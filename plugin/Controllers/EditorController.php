<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Rating;
use WP_Post;
use WP_Screen;

class EditorController extends Controller
{
	const META_AVERAGE = '_glsr_average';
	const META_RANKING = '_glsr_ranking';
	const META_REVIEW_ID = '_glsr_review_id';

	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 */
	public function customizePostStatusLabels()
	{
		global $wp_scripts;
		$strings = [
			'savePending' => __( 'Save as Unapproved', 'site-reviews' ),
			'published' => __( 'Approved', 'site-reviews' ),
		];
		if( $this->canModifyTranslation() && isset( $wp_scripts->registered['post']->extra['data'] )) {
			$l10n = &$wp_scripts->registered['post']->extra['data'];
			foreach( $strings as $search => $replace ) {
				$l10n = preg_replace( '/("'.$search.'":")([^"]+)/', "$1".$replace, $l10n );
			}
		}
	}

	/**
	 * @return array
	 * @filter wp_editor_settings
	 */
	public function filterEditorSettings( array $settings )
	{
		if( $this->isReviewEditable() ) {
			$settings = [
				'media_buttons' => false,
				'quicktags' => false,
				'textarea_rows' => 12,
				'tinymce' => false,
			];
		}
		return $settings;
	}

	/**
	 * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script
	 * @param string $html
	 * @return string
	 * @filter the_editor
	 */
	public function filterEditorTextarea( $html )
	{
		if( $this->isReviewEditable() ) {
			$html = str_replace( '<textarea', '<div id="ed_toolbar"></div><textarea', $html );
		}
		return $html;
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
		if( $this->canModifyTranslation( $domain )) {
			$replacements = [
				'Pending Review' => __( 'Unapproved', 'site-reviews' ),
				'Pending' => __( 'Unapproved', 'site-reviews' ),
				'Privately Published' => __( 'Privately Approved', 'site-reviews' ),
				'Published' => __( 'Approved', 'site-reviews' ),
				'Save as Pending' => __( 'Save as Unapproved', 'site-reviews' ),
			];
			foreach( $replacements as $search => $replacement ) {
				if( $translation != $search )continue;
				$translation = $replacement;
			}
		}
		return $translation;
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
		return $this->filterPostStatusLabels( $translation, $text, $domain );
	}

	/**
	 * @return array
	 * @filter post_updated_messages
	 */
	public function filterUpdateMessages( array $messages )
	{
		$post = get_post();
		if( !( $post instanceof WP_Post ))return;
		$strings = glsr( Strings::class )->post_updated_messages();
		$restored = filter_input( INPUT_GET, 'revision' );
		if( $revisionTitle = wp_post_revision_title( intval( $restored ), false )) {
			$restored = sprintf( $strings['restored'], $revisionTitle );
		}
		$scheduled_date = date_i18n( 'M j, Y @ H:i', strtotime( $post->post_date ));
		$messages[ Application::POST_TYPE ] = [
			 1 => $strings['updated'],
			 4 => $strings['updated'],
			 5 => $restored,
			 6 => $strings['published'],
			 7 => $strings['saved'],
			 8 => $strings['submitted'],
			 9 => sprintf( $strings['scheduled'], '<strong>'.$scheduled_date.'</strong>' ),
			10 => $strings['draft_updated'],
			50 => $strings['approved'],
			51 => $strings['unapproved'],
			52 => $strings['reverted'],
		];
		return $messages;
	}

	/**
	 * @param array $postData
	 * @param array $meta
	 * @param int $postId
	 * @return void
	 */
	public function onCreateReview( $postData, $meta, $postId )
	{
		if( !$this->isReviewPostType( $review = get_post( $postId )))return;
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action before_delete_post
	 */
	public function onDeleteReview( $postId )
	{
		if( !$this->isReviewPostType( $review = get_post( $postId )))return;
		$review->post_status = 'deleted'; // important to change the post_status here first!
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action save_post_.static::POST_TYPE
	 */
	public function onSaveReview( $postId, WP_Post $review )
	{
		$this->updateAssignedToPost( $review );
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
		if( $this->isReviewEditor() && !$this->isReviewEditable() ) {
			wp_deregister_script( 'autosave' );
		}
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function removeMetaBoxes()
	{
		remove_meta_box( 'slugdiv', Application::POST_TYPE, 'advanced' );
	}

	/**
	 * @return void
	 * @callback add_meta_box
	 */
	public function renderAssignedToMetabox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		$assignedTo = get_post_meta( $post->ID, 'assigned_to', true );
		$template = '';
		if( $assignedPost = get_post( $assignedTo )) {
			ob_start();
			glsr( Html::class )->renderTemplate( 'edit/assigned-post', [
				'context' => [
					'url' => (string)get_permalink( $assignedPost ),
					'title' => get_the_title( $assignedPost ),
				],
			]);
			$template = ob_get_clean();
		}
		wp_nonce_field( 'assigned_to', '_nonce-assigned-to', false );
		glsr()->render( 'edit/metabox-assigned-to', [
			'id' => $assignedTo,
			'template' => $template,
		]);
	}

	/**
	 * @return void
	 * @callback add_meta_box
	 */
	public function renderDetailsMetaBox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		$review = glsr_db()->getReview( $post );
		glsr()->render( 'edit/metabox-details', [
			'button' => $this->getMetaboxButton( $review, $post ),
			'metabox' => $this->getMetaboxDetails( $review ),
		]);
	}

	/**
	 * @return void
	 * @action post_submitbox_misc_actions
	 */
	public function renderMetaBoxPinned()
	{
		if( !$this->isReviewPostType( get_post() ))return;
		$pinned = get_post_meta( get_the_ID(), 'pinned', true );
		glsr()->render( 'edit/pinned', [
			'pinned' => $pinned,
		]);
	}

	/**
	 * @return void
	 * @callback add_meta_box
	 */
	public function renderResponseMetaBox( WP_Post $post )
	{
		if( !$this->isReviewPostType( $post ))return;
		$review = glsr_db()->getReview( $post );
		wp_nonce_field( 'response', '_nonce-response', false );
		glsr()->render( 'edit/metabox-response', [
			'response' => $review->response,
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
			'tax_name' => esc_attr( Application::TAXONOMY ),
			'taxonomy' => get_taxonomy( Application::TAXONOMY ),
		]);
	}

	/**
	 * @return void
	 * @see $this->filterUpdateMessages()
	 * @action admin_action_revert
	 */
	public function revert()
	{
		check_admin_referer( 'revert-review_'.( $postId = $this->getPostId() ));
		glsr_db()->revertReview( $postId );
		$this->redirect( $postId, 52 );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action save_post_.Application::POST_TYPE
	 */
	public function saveMetaboxes( $postId )
	{
		$this->saveAssignedToMetabox( $postId );
		$this->saveResponseMetabox( $postId );
	}

	/**
	 * @param string $domain
	 * @return bool
	 */
	protected function canModifyTranslation( $domain = 'default' )
	{
		return glsr_current_screen()->post_type == Application::POST_TYPE
			&& in_array( glsr_current_screen()->base, ['edit', 'post'] )
			&& $domain == 'default';
	}

	/**
	 * @param int $postId
	 * @return int|false
	 */
	protected function getAssignedToPostId( $postId )
	{
		$assignedTo = get_post_meta( $postId, 'assigned_to', true );
		if(( $post = get_post( $assignedTo )) instanceof WP_Post ) {
			return $post->ID;
		}
		return false;
	}

	/**
	 * @param object $review
	 * @return string
	 */
	protected function getMetaboxButton( $review, WP_Post $post )
	{
		$modified = false;
		if( $post->post_title !== $review->title
			|| $post->post_content !== $review->content
			|| $post->post_date !== $review->date ) {
			$modified = true;
		}
		$revertUrl = wp_nonce_url(
			admin_url( 'post.php?post='.$post->ID.'&action=revert' ),
			'revert-review_'.$post->ID
		);
		return !$modified
			? sprintf( '<button id="revert" class="button button-large" disabled>%s</button>', __( 'Nothing to Revert', 'site-reviews' ))
			: sprintf( '<a href="%s" id="revert" class="button button-large">%s</a>', $revertUrl, __( 'Revert Changes', 'site-reviews' ));
	}

	/**
	 * @param object $review
	 * @return array
	 */
	protected function getMetaboxDetails( $review )
	{
		$reviewer = empty( $review->user_id )
			? __( 'Unregistered user', 'site-reviews' )
			: $this->generateLink( get_the_author_meta( 'display_name', $review->user_id ), [
				'href' => get_author_posts_url( $review->user_id ),
			]);
		$email = empty( $review->email )
			? '&mdash;'
			: $this->generateLink( $review->email, [
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
	 * @param object $review
	 * @return string
	 */
	protected function getReviewType( $review )
	{
		$reviewType = $review->review_type;
		$reviewTypeFallback = !empty( $review->review_type )
			? ucfirst( $review->review_type )
			: __( 'Unknown', 'site-reviews' );
		if( !empty( $review->url )) {
			$reviewType = $this->generateLink( $reviewType, [
				'href' => $review->url,
				'target' => '_blank',
			]);
		}
		return sprintf( __( '%s review', 'site-reviews' ),
			glsr( Strings::class )->review_types( $reviewType, $reviewTypeFallback )
		);
	}

	/**
	 * @return bool
	 */
	protected function isReviewEditable()
	{
		$postId = intval( filter_input( INPUT_GET, 'post' ));
		return $postId > 0
			&& get_post_meta( $postId, 'review_type', true ) == 'local'
			&& $this->isReviewEditor();
	}

	/**
	 * @return bool
	 */
	protected function isReviewEditor()
	{
		return glsr_current_screen()->base == 'post'
			&& glsr_current_screen()->id == Application::POST_TYPE
			&& glsr_current_screen()->post_type == Application::POST_TYPE;
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
	 * @return int
	 */
	protected function recalculatePostAverage( array $reviews )
	{
		return glsr( Rating::class )->getAverage( $reviews );
	}

	/**
	 * @return int
	 */
	protected function recalculatePostRanking( array $reviews )
	{
		return glsr( Rating::class )->getRanking( $reviews );
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

	/**
	 * @param int $postId
	 * @return void
	 */
	protected function saveAssignedToMetabox( $postId )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-assigned-to' ), 'assigned_to' ))return;
		$assignedTo = filter_input( INPUT_POST, 'assigned_to' );
		$assignedTo || $assignedTo = '';
		if( get_post_meta( $postId, 'assigned_to', true ) != $assignedTo ) {
			$this->onDeleteReview( $postId );
		}
		update_post_meta( $postId, 'assigned_to', $assignedTo );
	}

	/**
	 * @param int $postId
	 * @return mixed
	 */
	protected function saveResponseMetabox( $postId )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-response' ), 'response' ))return;
		$response = filter_input( INPUT_POST, 'response' );
		$response || $response = '';
		update_post_meta( $postId, 'response', trim( wp_kses( $response, [
			'a' => ['href' => [], 'title' => []],
			'em' => [],
			'strong' => [],
		])));
	}

	/**
	 * @return void
	 */
	protected function updateAssignedToPost( WP_Post $review )
	{
		if( !( $postId = $this->getAssignedToPostId( $review->ID )))return;
		$reviewIds = get_post_meta( $postId, static::META_REVIEW_ID );
		$updatedReviewIds = array_filter( (array)get_post_meta( $postId, static::META_REVIEW_ID ));
		$this->updateReviewIdOfPost( $postId, $review, $reviewIds );
		if( empty( $updatedReviewIds )) {
			delete_post_meta( $postId, static::META_RANKING );
			delete_post_meta( $postId, static::META_REVIEW_ID );
		}
		else if( !glsr( Helper::class )->compareArrays( $reviewIds, $updatedReviewIds )) {
			$reviews = glsr_db()->getReviews([
				'count' => -1,
				'post__in' => $updatedReviewIds,
			]);
			update_post_meta( $postId, static::META_AVERAGE, $this->recalculatePostAverage( $reviews->results ));
			update_post_meta( $postId, static::META_RANKING, $this->recalculatePostRanking( $reviews->results ));
		}
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	protected function updateReviewIdOfPost( $postId, WP_Post $review, array $reviewIds )
	{
		if( $review->post_status != 'publish' ) {
			delete_post_meta( $postId, static::META_REVIEW_ID, $review->ID );
		}
		else if( !in_array( $review->ID, $reviewIds )) {
			add_post_meta( $postId, static::META_REVIEW_ID, $review->ID );
		}
	}
}
