<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Controllers\ListTableController\Columns;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Post;
use WP_Query;
use WP_Screen;

class ListTableController extends Controller
{
	/**
	 * @return void
	 * @action admin_action_approve
	 */
	public function approve()
	{
		check_admin_referer( 'approve-review_'.( $postId = $this->getPostId() ));
		wp_update_post([
			'ID' => $postId,
			'post_status' => 'publish',
		]);
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * @return array
	 * @filter bulk_post_updated_messages
	 */
	public function filterBulkUpdateMessages( array $messages, array $counts )
	{
		$messages[Application::POST_TYPE] = [
			'updated' => _n( '%s review updated.', '%s reviews updated.', $counts['updated'], 'site-reviews' ),
			'locked' => _n( '%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'site-reviews' ),
			'deleted' => _n( '%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'site-reviews' ),
			'trashed' => _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'site-reviews' ),
			'untrashed' => _n( '%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'site-reviews' ),
		];
		return $messages;
	}

	/**
	 * @return array
	 * @filter manage_.Application::POST_TYPE._posts_columns
	 */
	public function filterColumnsForPostType( array $columns )
	{
		$postTypeColumns = glsr()->postTypeColumns[Application::POST_TYPE];
		foreach( $postTypeColumns as $key => &$value ) {
			if( !array_key_exists( $key, $columns ) || !empty( $value ))continue;
			$value = $columns[$key];
		}
		if( count( glsr( Database::class )->getReviewsMeta( 'review_type' )) < 2 ) {
			unset( $postTypeColumns['review_type'] );
		}
		return array_filter( $postTypeColumns, 'strlen' );
	}

	/**
	 * @param string $status
	 * @return string
	 * @filter post_date_column_status
	 */
	public function filterDateColumnStatus( $status, WP_Post $post )
	{
		if( $post->post_type == Application::POST_TYPE ) {
			$status = __( 'Submitted', 'site-reviews' );
		}
		return $status;
	}

	/**
	 * @return array
	 * @filter default_hidden_columns
	 */
	public function filterDefaultHiddenColumns( array $hidden, WP_Screen $screen )
	{
		if( $screen->id == 'edit-'.Application::POST_TYPE ) {
			$hidden = ['reviewer'];
		}
		return $hidden;
	}

	/**
	 * @return array
	 * @filter display_post_states
	 */
	public function filterPostStates( array $postStates, WP_Post $post ) {
		if( $post->post_type == Application::POST_TYPE
			&& array_key_exists( 'pending', $postStates )) {
			$postStates['pending'] = __( 'Unapproved', 'site-reviews' );
		}
		return $postStates;
	}

	/**
	 * @return array
	 * @filter post_row_actions
	 */
	public function filterRowActions( array $actions, WP_Post $post )
	{
		if( $post->post_type != Application::POST_TYPE || $post->post_status == 'trash' ) {
			return $actions;
		}
		unset( $actions['inline hide-if-no-js'] ); //Remove Quick-edit
		$rowActions = [
			'approve' => esc_attr__( 'Approve', 'site-reviews' ),
			'unapprove' => esc_attr__( 'Unapprove', 'site-reviews' ),
		];
		$newActions = [];
		foreach( $rowActions as $key => $text ) {
			$newActions[$key] = glsr( Builder::class )->a( $text, [
				'aria-label' => sprintf( esc_attr_x( '%s this review', 'Approve the review', 'site-reviews' ), $text ),
				'class' => 'glsr-change-status',
				'href' => wp_nonce_url(
					admin_url( 'post.php?post='.$post->ID.'&action='.$key ),
					$key.'-review_'.$post->ID
				),
			]);
		}
		return $newActions + $actions;
	}

	/**
	 * @return array
	 * @filter manage_edit-.Application::POST_TYPE._sortable_columns
	 */
	public function filterSortableColumns( array $columns )
	{
		$postTypeColumns = glsr()->postTypeColumns[Application::POST_TYPE];
		unset( $postTypeColumns['cb'] );
		foreach( $postTypeColumns as $key => $value ) {
			if( glsr( Helper::class )->startsWith( 'taxonomy', $key ))continue;
			$columns[$key] = $key;
		}
		return $columns;
	}

	/**
	 * Customize the post_type status text
	 * @param string $translation
	 * @param string $single
	 * @param string $plural
	 * @param int $number
	 * @param string $domain
	 * @return string
	 * @filter ngettext
	 */
	public function filterStatusText( $translation, $single, $plural, $number, $domain )
	{
		if( $this->canModifyTranslation( $domain )) {
			$strings = [
				'Published' => __( 'Approved', 'site-reviews' ),
				'Pending' => __( 'Unapproved', 'site-reviews' ),
			];
			foreach( $strings as $search => $replace ) {
				if( strpos( $single, $search ) === false )continue;
				$translation = $this->getTranslation([
					'number' => $number,
					'plural' => str_replace( $search, $replace, $plural ),
					'single' => str_replace( $search, $replace, $single ),
				]);
			}
		}
		return $translation;
	}

	/**
	 * @param string $columnName
	 * @param string $postType
	 * @return void
	 * @action bulk_edit_custom_box
	 */
	public function renderBulkEditFields( $columnName, $postType )
	{
		if( $columnName == 'assigned_to' && $postType == Application::POST_TYPE ) {
			glsr()->render( 'partials/editor/bulk-edit-assigned-to' );
		};
	}

	/**
	 * @param string $postType
	 * @return void
	 * @action restrict_manage_posts
	 */
	public function renderColumnFilters( $postType )
	{
		glsr( Columns::class )->renderFilters( $postType );
	}

	/**
	 * @param string $column
	 * @param string $postId
	 * @return void
	 * @action manage_posts_custom_column
	 */
	public function renderColumnValues( $column, $postId )
	{
		glsr( Columns::class )->renderValues( $column, $postId );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action save_post_.Application::POST_TYPE
	 */
	public function saveBulkEditFields( $postId )
	{
		if( !current_user_can( 'edit_posts' ))return;
		$assignedTo = filter_input( INPUT_GET, 'assigned_to' );
		if( $assignedTo && get_post( $assignedTo )) {
			update_post_meta( $postId, 'assigned_to', $assignedTo );
		}
	}

	/**
	 * @return void
	 * @action pre_get_posts
	 */
	public function setQueryForColumn( WP_Query $query )
	{
		if( !$this->hasPermission( $query ))return;
		$this->setMetaQuery( $query, [
			'rating', 'review_type',
		]);
		$this->setOrderby( $query );
	}

	/**
	 * @return void
	 * @action admin_action_unapprove
	 */
	public function unapprove()
	{
		check_admin_referer( 'unapprove-review_'.( $postId = $this->getPostId() ));
		wp_update_post([
			'ID' => $postId,
			'post_status' => 'pending',
		]);
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Check if the translation string can be modified
	 * @param string $domain
	 * @return bool
	 */
	protected function canModifyTranslation( $domain = 'default' )
	{
		$screen = glsr_current_screen();
		return $domain == 'default'
			&& $screen->base == 'edit'
			&& $screen->post_type == Application::POST_TYPE;
	}

	/**
	 * Get the modified translation string
	 * @return string
	 */
	protected function getTranslation( array $args )
	{
		$defaults = [
			'number' => 0,
			'plural' => '',
			'single' => '',
			'text' => '',
		];
		$args = (object) wp_parse_args( $args, $defaults );
		$translations = get_translations_for_domain( Application::ID );
		return $args->text
			? $translations->translate( $args->text )
			: $translations->translate_plural( $args->single, $args->plural, $args->number );
	}

	/**
	 * @return bool
	 */
	protected function hasPermission( WP_Query $query )
	{
		global $pagenow;
		return is_admin()
			&& $query->is_main_query()
			&& $query->get( 'post_type' ) == Application::POST_TYPE
			&& $pagenow == 'edit.php';
	}

	/**
	 * @return void
	 */
	protected function setMetaQuery( WP_Query $query, array $metaKeys )
	{
		foreach( $metaKeys as $key ) {
			if( !( $value = filter_input( INPUT_GET, $key )))continue;
			$metaQuery = (array)$query->get( 'meta_query' );
			$metaQuery[] = [
				'key' => $key,
				'value' => $value,
			];
			$query->set( 'meta_query', $metaQuery );
		}
	}

	/**
	 * @return void
	 */
	protected function setOrderby( WP_Query $query )
	{
		$orderby = $query->get( 'orderby' );
		$columns = glsr()->postTypeColumns[Application::POST_TYPE];
		unset( $columns['cb'], $columns['title'], $columns['date'] );
		if( in_array( $orderby, array_keys( $columns ))) {
			$query->set( 'meta_key', $orderby );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
