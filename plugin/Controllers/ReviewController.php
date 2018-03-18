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
use GeminiLabs\SiteReviews\Commands\SubmitReview;
use GeminiLabs\SiteReviews\Controllers\BaseController;
use GeminiLabs\SiteReviews\Strings;
use WP_Post;
use WP_Screen;

class ReviewController extends BaseController
{
	/**
	 * @return void
	 */
	public function approve()
	{
		check_admin_referer( 'approve-review_' . ( $post_id = $this->getPostId() ));

		wp_update_post([
			'ID'          => $post_id,
			'post_status' => 'publish',
		]);

		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Remove the autosave functionality
	 *
	 * @return void
	 * @action admin_print_scripts
	 */
	public function modifyAutosave()
	{
		if( $this->isEditReview() && !$this->canEditReview() ) {
			wp_deregister_script( 'autosave' );
		}
	}

	/**
	 * Modifies the WP_Editor settings
	 *
	 * @return array
	 * @filter wp_editor_settings
	 */
	public function modifyEditor( array $settings )
	{
		if( $this->canEditReview() ) {
			$settings = [
				'textarea_rows' => 12,
				'media_buttons' => false,
				'quicktags'     => false,
				'tinymce'       => false,
			];
		}

		return $settings;
	}

	/**
	 * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script
	 *
	 * @param string $html
	 * @return string
	 * @filter the_editor
	 */
	public function modifyEditorTextarea( $html )
	{
		if( $this->canEditReview() ) {
			$html = str_replace( '<textarea', '<div id="ed_toolbar"></div><textarea', $html );
		}

		return $html;
	}

	/**
	 * Add a variable to query_vars for custom pagination
	 *
	 * @param array $vars
	 * @return array
	 * @filter query_vars
	 * @todo remove dirty hack
	 */
	public function modifyQueryVars( $vars )
	{
		$vars[] = App::PAGED_QUERY_VAR;
		// dirty hack to fix a form submission with a field that has "name" as name
		if( filter_input( INPUT_POST, 'action' ) == 'post-review'
			&& !is_null( filter_input( INPUT_POST, 'gotcha' ))) {
			$index = array_search( 'name', $vars, true );
			if( false !== $index ) {
				unset( $vars[$index] );
			}
		}
		return $vars;
	}

	/**
	 * Remove post_type support for all non-local reviews
	 *
	 * @todo: Move this to addons
	 * @return void
	 * @action current_screen
	 */
	public function modifyFeatures( WP_Screen $screen )
	{
		if( $this->canEditReview()
			|| $screen->post_type != App::POST_TYPE
		)return;

		remove_post_type_support( App::POST_TYPE, 'title' );
		remove_post_type_support( App::POST_TYPE, 'editor' );
	}

	/**
	 * Customize the post_type status text
	 *
	 * @action admin_enqueue_scripts
	 */
	public function modifyLocalizedStatusText()
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
	 * Customize the post_type status text
	 *
	 * @param string $translation
	 * @param string $test
	 * @param string $domain
	 * @return string
	 * @filter gettext
	 */
	public function modifyStatusText( $translation, $text, $domain )
	{
		if( $this->canModifyTranslation( $domain )) {
			$replacements = [
				'Save as Pending' => __( 'Save as Unapproved', 'site-reviews' ),
				'Privately Published' => __( 'Privately Approved', 'site-reviews' ),
				'Published' => __( 'Approved', 'site-reviews' ),
				'Pending' => __( 'Unapproved', 'site-reviews' ),
				'Pending Review' => __( 'Unapproved', 'site-reviews' ),
			];
			foreach( $replacements as $search => $replacement ) {
				if( $translation != $search )continue;
				$translation = $replacement;
			}
		}
		return $translation;
	}

	/**
	 * Customize the post_type status text
	 *
	 * @param string $translation
	 * @param string $test
	 * @param string $domain
	 * @return string
	 * @filter gettext_with_context
	 */
	public function modifyStatusTextWithContext( $translation, $text, $context, $domain )
	{
		return $this->modifyStatusText( $translation, $text, $domain );
	}

	/**
	 * Customize the updated messages array for this post_type
	 *
	 * @return array
	 * @filter post_updated_messages
	 */
	public function modifyUpdateMessages( array $messages )
	{
		$post = get_post();
		if( !( $post instanceof WP_Post ) || !$post->ID )return;

		$strings = glsr_resolve( 'Strings' )->post_updated_messages();

		$restored = filter_input( INPUT_GET, 'revision' );
		if( $revisionTitle = wp_post_revision_title( (int) $restored, false )) {
			$restored = sprintf( $strings['restored'], $revisionTitle );
		}

		$scheduled_date = date_i18n( 'M j, Y @ H:i', strtotime( $post->post_date ));

		$messages[ App::POST_TYPE ] = [
			 1 => $strings['updated'],
			 4 => $strings['updated'],
			 5 => $restored,
			 6 => $strings['published'],
			 7 => $strings['saved'],
			 8 => $strings['submitted'],
			 9 => sprintf( $strings['scheduled'], sprintf( '<strong>%s</strong>', $scheduled_date )),
			10 => $strings['draft_updated'],
			50 => $strings['approved'],
			51 => $strings['unapproved'],
			52 => $strings['reverted'],
		];

		return $messages;
	}

	/**
	 * Customize the bulk updated messages array for this post_type
	 *
	 * @return array
	 * @filter bulk_post_updated_messages
	 */
	public function modifyUpdateMessagesBulk( array $messages, array $counts )
	{
		$messages[ App::POST_TYPE ] = [
			'updated'   => _n( '%s review updated.', '%s reviews updated.', $counts['updated'], 'site-reviews' ),
			'locked'    => _n( '%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'site-reviews' ),
			'deleted'   => _n( '%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'site-reviews' ),
			'trashed'   => _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'site-reviews' ),
			'untrashed' => _n( '%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'site-reviews' ),
		];

		return $messages;
	}

	/**
	 * @param array $post_data
	 * @param array $meta
	 * @param int $post_id
	 * @return void
	 */
	public function onCreateReview( $post_data, $meta, $post_id )
	{
		$review = get_post( $post_id );
		if( !$review || $review->post_type !== App::POST_TYPE )return;
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $post_id
	 * @return void
	 */
	public function onDeleteReview( $post_id )
	{
		$review = get_post( $post_id );
		if( !$review || $review->post_type !== App::POST_TYPE )return;
		$review->post_status = 'deleted'; // important to change the post_status here first!
		$this->updateAssignedToPost( $review );
	}

	/**
	 * @param int $post_id
	 * @param WP_Post $review
	 * @return void
	 */
	public function onSaveReview( $post_id, $review )
	{
		$this->updateAssignedToPost( $review );
	}

	/**
	 * Submit the review form
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function postSubmitReview( array $request )
	{
		$session = $this->app->make( 'Session' );

		// Validation
		$validatedRequest = $this->validateSubmittedReview( $request );
		if( !is_array( $validatedRequest )) {
			return __( 'Please fix the submission errors.', 'site-reviews' );
		}
		// Custom validation
		$customValidation = apply_filters( 'site-reviews/validate/review/submission', true, $validatedRequest );
		if( $customValidation !== true ) {
			$session->set( "{$validatedRequest['form_id']}-errors", [] );
			$session->set( "{$validatedRequest['form_id']}-values", $validatedRequest );
			return is_string( $customValidation )
				? $customValidation
				: __( 'The review submission failed. Please notify the site administrator.', 'site-reviews' );
		}
		// Honeypot validation
		if( !empty( $validatedRequest['gotcha'] )) {
			$session->set( "{$validatedRequest['form_id']}-errors", [] );
			glsr_resolve( 'Log\Logger' )->warning( 'The Honeypot caught a bad submission:' );
			glsr_resolve( 'Log\Logger' )->warning( $validatedRequest );
			return __( 'The review submission failed. Please notify the site administrator.', 'site-reviews' );
		}
		// reCAPTCHA validation
		$validateRecaptcha = $this->validateRecaptcha();
		if( is_null( $validateRecaptcha )) {
			// recaptcha response was empty so it hasn't been set yet
			$session->set( "{$validatedRequest['form_id']}-recaptcha", true );
			return;
		}
		if( !$validateRecaptcha ) {
			$session->set( "{$validatedRequest['form_id']}-errors", [] );
			$session->set( "{$validatedRequest['form_id']}-recaptcha", 'reset' );
			return __( 'The reCAPTCHA verification failed. Please notify the site administrator.', 'site-reviews' );
		}

		$submitReview = new SubmitReview( $validatedRequest );

		// Blacklist validation
		if( $this->app->make( 'GeminiLabs\SiteReviews\Blacklist' )->isBlacklisted( $submitReview )) {
			$blacklistAction = glsr_get_option( 'reviews-form.blacklist.action' );
			if( $blacklistAction == 'unapprove' ) {
				$submitReview->blacklisted = true;
			}
			if( $blacklistAction == 'reject' ) {
				$session->set( "{$validatedRequest['form_id']}-errors", [] );
				glsr_resolve( 'Log\Logger' )->warning( 'Blacklisted submission detected:' );
				glsr_resolve( 'Log\Logger' )->warning( $validatedRequest );
				return __( 'Your review cannot be submitted at this time.', 'site-reviews' );
			}
		}

		// Akismet validation
		if( $this->app->make( 'GeminiLabs\SiteReviews\Akismet' )->isSpam( $submitReview )) {
			$session->set( "{$validatedRequest['form_id']}-errors", [] );
			glsr_resolve( 'Log\Logger' )->warning( 'Akismet caught a spam submission:' );
			glsr_resolve( 'Log\Logger' )->warning( $validatedRequest );
			return __( 'Your review cannot be submitted at this time. Please try again later.', 'site-reviews' );
		}

		return $this->execute( $submitReview );
	}

	/**
	 * @return int|float
	 */
	public function recalculatePostAverage( array $reviews )
	{
		return apply_filters( 'site-reviews/average/rating',
			$this->app->make( 'Rating' )->getAverage( $reviews ),
			$reviews
		);
	}

	/**
	 * @return int|float
	 */
	public function recalculatePostRanking( array $reviews )
	{
		return apply_filters( 'site-reviews/bayesian/ranking',
			$this->app->make( 'Rating' )->getRankingImdb( $reviews ),
			$reviews
		);
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function removeMetaBoxes()
	{
		remove_meta_box( 'slugdiv', App::POST_TYPE, 'advanced' );
	}

	/**
	 * @param string $columnName
	 * @param string $postType
	 * @return void
	 * @action bulk_edit_custom_box
	 */
	public function renderBulkEditFields( $columnName, $postType )
	{
		if( $columnName == 'assigned_to' && $postType == App::POST_TYPE ) {
			$this->render( 'edit/bulk-edit-assigned-to' );
		};
	}

	/**
	 * @return void
	 */
	public function revert()
	{
		check_admin_referer( 'revert-review_' . ( $post_id = $this->getPostId() ));

		$this->db->revertReview( $post_id );

		$this->redirect( $post_id, 52 );
	}

	/**
	 * @param int $post_id
	 * @return mixed
	 */
	public function saveAssignedToMetabox( $post_id )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-assigned-to' ), 'assigned_to' ))return;
		$assignedTo = filter_input( INPUT_POST, 'assigned_to' );
		$assignedTo || $assignedTo = '';
		if( get_post_meta( $post_id, 'assigned_to', true ) != $assignedTo ) {
			$this->onDeleteReview( $post_id );
		}
		update_post_meta( $post_id, 'assigned_to', $assignedTo );
	}

	/**
	 * @param int $post_id
	 * @return void
	 * @action save_post_{static::POST_TYPE}
	 */
	public function saveBulkEditFields( $post_id )
	{
		$assignedTo = filter_input( INPUT_GET, 'assigned_to' );
		if( !current_user_can( 'edit_posts' ))return;
		if( $assignedTo && get_post( $assignedTo )) {
			update_post_meta( $post_id, 'assigned_to', $assignedTo );
		}
	}

	/**
	 * @param int $post_id
	 * @return mixed
	 */
	public function saveEditedReview( $post_id )
	{
		$this->saveAssignedToMetabox( $post_id );
		$this->saveResponseMetabox( $post_id );
	}

	/**
	 * @param int $post_id
	 * @return mixed
	 */
	public function saveResponseMetabox( $post_id )
	{
		if( !wp_verify_nonce( filter_input( INPUT_POST, '_nonce-response' ), 'response' ))return;
		$response = filter_input( INPUT_POST, 'response' );
		$response || $response = '';
		update_post_meta( $post_id, 'response', trim( wp_kses( $response, [
			'a' => ['href' => [], 'title' => []],
			'em' => [],
			'strong' => [],
		])));
	}

	/**
	 * Set/persist custom permissions for the post_type
	 *
	 * @return void
	 */
	public function setPermissions()
	{
		foreach( wp_roles()->roles as $role => $value ) {
			wp_roles()->remove_cap( $role, sprintf( 'create_%s', App::POST_TYPE ));
		}
	}

	/**
	 * @return void
	 */
	public function unapprove()
	{
		check_admin_referer( 'unapprove-review_' . ( $post_id = $this->getPostId() ));

		wp_update_post([
			'ID'          => $post_id,
			'post_status' => 'pending',
		]);

		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * @return bool
	 */
	protected function canEditReview()
	{
		$postId = filter_input( INPUT_GET, 'post' );

		$reviewType = get_post_meta( $postId, 'review_type', true );

		return $postId > 0
			&& $reviewType == 'local'
			&& $this->isEditReview();
	}

	/**
	 * Check if the translation string can be modified
	 *
	 * @param string $domain
	 * @return bool
	 */
	protected function canModifyTranslation( $domain = 'default' )
	{
		return glsr_current_screen()->post_type == App::POST_TYPE
			&& in_array( glsr_current_screen()->base, ['edit', 'post'] )
			&& $domain == 'default';
	}

	/**
	 * @return bool
	 */
	protected function isEditReview()
	{
		return glsr_current_screen()->post_type == App::POST_TYPE
			&& glsr_current_screen()->id == App::POST_TYPE
			&& glsr_current_screen()->base == 'post';
	}

	/**
	 * @param int $post_id
	 * @return WP_Post|false
	 */
	protected function getAssignedToPost( $post_id )
	{
		if( $assignedTo = get_post_meta( $post_id, 'assigned_to', true )) {
			$assignedToPost = get_post( (int) $assignedTo );
		}
		return !empty( $assignedToPost )
			? $assignedToPost
			: false;
	}

	/**
	 * @return int
	 */
	protected function getPostId()
	{
		return (int) filter_input( INPUT_GET, 'post' );
	}

	/**
	 * Get the modified translation string
	 *
	 * @return string
	 */
	protected function getTranslation( array $args )
	{
		$defaults = [
			'number' => 0,
			'plural' => '',
			'single' => '',
			'text'   => '',
		];

		$args = (object) wp_parse_args( $args, $defaults );

		$translations = get_translations_for_domain( 'site-reviews' );

		return $args->text
			? $translations->translate( $args->text )
			: $translations->translate_plural( $args->single, $args->plural, $args->number );
	}

	/**
	 * @param int $post_id
	 * @param int $message_index
	 * @return void
	 */
	protected function redirect( $post_id, $message_index )
	{
		$referer = wp_get_referer();

		$hasReferer = !$referer
			|| strpos( $referer, 'post.php' ) !== false
			|| strpos( $referer, 'post-new.php' ) !== false;


		$redirect = !$hasReferer
			? add_query_arg( ['message' => $message_index ], get_edit_post_link( $post_id, '' ))
			: add_query_arg( ['message' => $message_index ], remove_query_arg( ['trashed', 'untrashed', 'deleted', 'ids'], $referer ));

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * @return void
	 */
	protected function updateAssignedToPost( WP_Post $review )
	{
		if( !( $post = $this->getAssignedToPost( $review->ID )))return;
		$reviewIds = get_post_meta( $post->ID, '_glsr_review_id' );
		if( !is_array( $reviewIds ))return;
		$this->updateReviewIdOfPost( $post, $review, $reviewIds );
		$updatedReviewIds = array_filter( (array) get_post_meta( $post->ID, '_glsr_review_id' ));
		if( empty( $updatedReviewIds )) {
			delete_post_meta( $post->ID, '_glsr_ranking' );
			delete_post_meta( $post->ID, '_glsr_review_id' );
		}
		else if( !$this->app->make( 'Helper' )->compareArrays( $reviewIds, $updatedReviewIds )) {
			$reviews = $this->db->getReviews([
				'count' => -1,
				'post__in' => $updatedReviewIds,
			]);
			update_post_meta( $post->ID, '_glsr_average', $this->recalculatePostAverage( $reviews->reviews ));
			update_post_meta( $post->ID, '_glsr_ranking', $this->recalculatePostRanking( $reviews->reviews ));
		}
	}

	/**
	 * @return void
	 */
	protected function updateReviewIdOfPost( WP_Post $post, WP_Post $review, array $reviewIds )
	{
		if( $review->post_status != 'publish' ) {
			delete_post_meta( $post->ID, '_glsr_review_id', $review->ID );
		}
		else if( !in_array( $review->ID, $reviewIds )) {
			add_post_meta( $post->ID, '_glsr_review_id', $review->ID );
		}
	}

	/**
	 * @return bool
	 */
	protected function validateCustomRecaptcha( $recaptchaResponse )
	{
		$response = wp_remote_get( add_query_arg([
			'remoteip' => $this->app->make( 'Helper' )->getIpAddress(),
			'response' => $recaptchaResponse,
			'secret' => glsr_get_option( 'reviews-form.recaptcha.secret' ),
		], 'https://www.google.com/recaptcha/api/siteverify' ));
		if( is_wp_error( $response )) {
			glsr_resolve( 'Log\Logger' )->error( 'reCAPTCHA: '.$response->get_error_message() );
			return false;
		}
		$result = json_decode( wp_remote_retrieve_body( $response ));
		if( !empty( $result->success )) {
			return $result->success;
		}
		$errorCodes = [
			'missing-input-secret' => 'The secret parameter is missing.',
			'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
			'missing-input-response' => 'The response parameter is missing.',
			'invalid-input-response' => 'The response parameter is invalid or malformed.',
			'bad-request' => 'The request is invalid or malformed.',
		];
		foreach( $result->{'error-codes'} as $error ) {
			glsr_resolve( 'Log\Logger' )->error( 'reCAPTCHA: '.$errorCodes[$error] );
		}
		return false;
	}

	/**
	 * @return bool
	 */
	protected function validateRecaptcha()
	{
		$integration = glsr_get_option( 'reviews-form.recaptcha.integration' );
		$recaptchaResponse = filter_input( INPUT_POST, 'g-recaptcha-response' );

		if( !$integration ) {
			return true;
		}
		// if response is empty we need to return null
		if( empty( $recaptchaResponse ))return;

		if( $integration == 'custom' ) {
			return $this->validateCustomRecaptcha( $recaptchaResponse );
		}
		if( $integration == 'invisible-recaptcha' ) {
			// if plugin is inactive, return true
			return apply_filters( 'google_invre_is_valid_request_filter', true );
		}
		return false;
	}

	/**
	 * @return false|array
	 */
	protected function validateSubmittedReview( array $request )
	{
		$minContentLength = apply_filters( 'site-reviews/local/review/content/minLength', '0' );

		$defaultRules = apply_filters( 'site-reviews/validation/rules', [
			'content' => 'required|min:' . $minContentLength,
			'email'   => 'required|email|min:5',
			'name'    => 'required',
			'rating'  => 'required|numeric|between:1,5',
			'terms'   => 'accepted',
			'title'   => 'required',
		]);

		$rules = array_intersect_key(
			$defaultRules,
			array_flip( array_merge( ['rating','terms'], glsr_get_option( 'reviews-form.required', [] )))
		);

		$excluded = isset( $request['excluded'] )
			? json_decode( $request['excluded'] )
			: [];

		// only use the rules for non-excluded values
		$rules = array_diff_key( $rules, array_flip( $excluded ));

		$user = wp_get_current_user();

		$defaults = [
			'assign_to' => '',
			'category'  => '',
			'content'   => '',
			'email'     => ( $user->exists() ? $user->user_email : '' ),
			'form_id'   => '',
			'name'      => ( $user->exists() ? $user->display_name : '' ),
			'rating'    => '0',
			'terms'     => '',
			'title'     => '',
		];

		if( !$this->validate( $request, $rules )) {
			return false;
		}

		if( empty( $request['title'] )) {
			$request['title'] = __( 'No Title', 'site-reviews' );
		}

		return array_merge( $defaults, $request );
	}
}
