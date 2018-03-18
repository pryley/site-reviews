<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Commands\SubmitReview;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Post;
use WP_Screen;
use WP_Query;

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
		if( count( glsr( Database::class )->getReviewsMeta( 'type' )) < 2 ) {
			unset( $postTypeColumns['review_type'] );
		}
		return array_filter( $postTypeColumns, 'strlen' );
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
		foreach( $rowActions as $key => $text ) {
			$actions[$key] = glsr( Builder::class )->a( $text, [
				'aria-label' => sprintf( esc_attr_x( '%s this review', 'Approve the review', 'site-reviews' ), text ),
				'class' => 'change-'.Application::POST_TYPE.'-status',
				'href' => wp_nonce_url(
					admin_url( 'post.php?post='.$post->ID.'&action='.$key ),
					$key.'-review_'.$post->ID
				),
			]);
		}
		return $actions;
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
			$this->render( 'edit/bulk-edit-assigned-to' );
		};
	}

	/**
	 * @param string $post_type
	 * @return void
	 * @action restrict_manage_posts
	 */
	public function renderColumnFilters( $post_type )
	{
		if( $post_type !== Application::POST_TYPE )return;
		if( !( $status = filter_input( INPUT_GET, 'post_status' ))) {
			$status = 'publish';
		}
		$ratings = glsr( Database::class )->getReviewsMeta( 'rating', $status );
		$types = glsr( Database::class )->getReviewsMeta( 'type', $status );
		$this->renderFilterRatings( $ratings );
		$this->renderFilterTypes( $types );
	}

	/**
	 * @param string $column
	 * @return void
	 * @action manage_posts_custom_column
	 */
	public function renderColumnValues( $column, $postId )
	{
		if( glsr_current_screen()->id != Application::POST_TYPE )return;
		global $wp_version;
		$method = glsr( Helper::class )->buildMethodName( $column, 'buildColumn' );
		echo method_exists( $this, $method )
			? call_user_func( [$this, $method], $postId )
			: apply_filters( 'site-reviews/columns/'.$column, '', $postId );
	}

	/**
	 * @param int $postId
	 * @return void
	 * @action save_post_.Application::POST_TYPE
	 */
	public function saveBulkEditFields( $postId )
	{
		if( !current_user_can( 'edit_posts' ))return;
		if( $assignedTo = filter_input( INPUT_GET, 'assigned_to' ) && get_post( $assignedTo )) {
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
	 * @param int $postId
	 * @return string
	 */
	protected function buildColumnAssignedTo( $postId )
	{
		$post = get_post( glsr( Database::class )->getReviewMeta( $postId )->assigned_to );
		if( !( $post instanceof WP_Post ) || $post->post_status != 'publish' ) {
			return '&mdash;';
		}
		return glsr( Builder::class )->a( get_the_title( $post->ID ), [
			'href' => (string)get_the_permalink( $post->ID ),
		]);
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function buildColumnReviewer( $postId )
	{
		return glsr( Database::class )->getReviewMeta(  $postId  )->author;
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function buildColumnStars( $postId )
	{
		return glsr( Html::class )->buildPartial( 'star-rating', [
			'rating' => glsr( Database::class )->getReviewMeta( $postId )->rating,
		]);
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function buildColumnSticky( $postId )
	{
		$pinned = glsr( Database::class )->getReviewMeta( $postId )->pinned
			? ' pinned'
			: '';
		return glsr( Builder::class )->i([
			'class' => trim( 'dashicons dashicons-sticky '.$pinned ),
			'data-id' => $postId,
		]);
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function buildColumnType( $postId )
	{
		$reviewMeta = glsr( Database::class )->getReviewMeta( $postId );
		return isset( glsr()->reviewTypes[$reviewMeta->review_type] )
			? glsr()->reviewTypes[$reviewMeta->review_type]
			: $reviewMeta->review_type;
	}

	/**
	 * Check if the translation string can be modified
	 * @param string $domain
	 * @return bool
	 */
	protected function canModifyTranslation( $domain = 'default' )
	{
		return $domain == 'default'
			&& glsr_current_screen()->base == 'edit'
			&& glsr_current_screen()->post_type == Application::POST_TYPE;
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
	 * @param array $ratings
	 * @return void
	 */
	protected function renderFilterRatings( $ratings )
	{
		if( empty( $ratings )
			|| apply_filters( 'site-reviews/disable/filter/ratings', false )
		)return;
		$ratings = array_flip( array_reverse( $ratings ));
		array_walk( $ratings, function( &$value, $key ) {
			$label = _n( '%s star', '%s stars', $key, 'site-reviews' );
			$value = sprintf( $label, $key );
		});
		$ratings = [__( 'All ratings', 'site-reviews' )] + $ratings;
		printf( '<label class="screen-reader-text" for="rating">%s</label>', __( 'Filter by rating', 'site-reviews' ));
		glsr( Html::class )->renderPartial( 'filterby', [
			'name' => 'rating',
			'values' => $ratings,
		]);
	}

	/**
	 * @param array $types
	 * @return void
	 */
	protected function renderFilterTypes( $types )
	{
		if( count( $types ) < 1
			|| ( count( $types ) == 1 && $types[0] == 'local' )
			|| apply_filters( 'site-reviews/disable/filter/types', false )
		)return;
		$reviewTypes = [__( 'All types', 'site-reviews' )];
		foreach( $types as $type ) {
			$reviewTypes[$type] = glsr( Strings::class )->review_types( $type, ucfirst( $type ));
		}
		printf( '<label class="screen-reader-text" for="type">%s</label>', __( 'Filter by type', 'site-reviews' ));
		glsr( Html::class )->renderPartial( 'filterby', [
			'name' => 'review_type',
			'values' => $reviewTypes,
		]);
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
