<?php

namespace GeminiLabs\SiteReviews\Modules\ListTable;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Post;

class Columns
{
	/**
	 * @param int $postId
	 * @return string
	 */
	public function buildColumnAssignedTo( $postId )
	{
		$assignedPost = glsr( Database::class )->getAssignedToPost( $postId );
		$column = '&mdash;';
		if( $assignedPost instanceof WP_Post && $assignedPost->post_status == 'publish' ) {
			$column = glsr( Builder::class )->a( get_the_title( $assignedPost->ID ), [
				'href' => (string)get_the_permalink( $assignedPost->ID ),
			]);
		}
		return $column;
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	public function buildColumnPinned( $postId )
	{
		$pinned = glsr( Database::class )->getReviewMeta( $postId )->pinned
			? 'pinned '
			: '';
		return glsr( Builder::class )->i([
			'class' => $pinned.'dashicons dashicons-sticky',
			'data-id' => $postId,
		]);
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	public function buildColumnReviewer( $postId )
	{
		return glsr( Database::class )->getReviewMeta( $postId )->author;
	}

	/**
	 * @param int $postId
	 * @param null|int $rating
	 * @return string
	 */
	public function buildColumnRating( $postId, $rating = null )
	{
		if( is_null( $rating )) {
			$rating = glsr( Database::class )->getReviewMeta( $postId )->rating;
		}
		ob_start();
		wp_star_rating([
			'rating' => $rating,
		]);
		return ob_get_clean();
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	public function buildColumnType( $postId )
	{
		$reviewMeta = glsr( Database::class )->getReviewMeta( $postId );
		return isset( glsr()->reviewTypes[$reviewMeta->review_type] )
			? glsr()->reviewTypes[$reviewMeta->review_type]
			: $reviewMeta->review_type;
	}

	/**
	 * @param string $postType
	 * @return void
	 */
	public function renderFilters( $postType )
	{
		if( $postType !== Application::POST_TYPE )return;
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
	 * @param int $postId
	 * @return void
	 */
	public function renderValues( $column, $postId )
	{
		if( glsr_current_screen()->post_type != Application::POST_TYPE )return;
		$method = glsr( Helper::class )->buildMethodName( $column, 'buildColumn' );
		echo method_exists( $this, $method )
			? call_user_func( [$this, $method], $postId )
			: apply_filters( 'site-reviews/columns/'.$column, '', $postId );
	}

	/**
	 * @param array $ratings
	 * @return void
	 */
	protected function renderFilterRatings( $ratings )
	{
		if( empty( $ratings ))return;
		$ratings = array_flip( array_reverse( $ratings ));
		array_walk( $ratings, function( &$value, $key ) {
			$label = _n( '%s star', '%s stars', $key, 'site-reviews' );
			$value = sprintf( $label, $key );
		});
		echo glsr( Builder::class )->label( __( 'Filter by rating', 'site-reviews' ), [
			'class' => 'screen-reader-text',
			'for' => 'rating',
		]);
		echo glsr( Builder::class )->select([
			'name' => 'rating',
			'options' => ['' => __( 'All ratings', 'site-reviews' )] + $ratings,
			'value' => filter_input( INPUT_GET, 'rating' ),
		]);
	}

	/**
	 * @param array $types
	 * @return void
	 */
	protected function renderFilterTypes( $types )
	{
		if( count( glsr()->reviewTypes ) < 2 )return;
		echo glsr( Builder::class )->label( __( 'Filter by type', 'site-reviews' ), [
			'class' => 'screen-reader-text',
			'for' => 'review_type',
		]);
		echo glsr( Builder::class )->select([
			'name' => 'review_type',
			'options' => ['' => __( 'All types', 'site-reviews' )] + glsr()->reviewTypes,
			'value' => filter_input( INPUT_GET, 'review_type' ),
		]);
	}
}
