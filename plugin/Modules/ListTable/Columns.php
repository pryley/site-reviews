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
		if( glsr_current_screen()->id != Application::POST_TYPE )return;
		$method = glsr( Helper::class )->buildMethodName( $column, 'buildColumn' );
		echo method_exists( $this, $method )
			? call_user_func( [$this, $method], $postId )
			: apply_filters( 'site-reviews/columns/'.$column, '', $postId );
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
		return glsr( Database::class )->getReviewMeta( $postId )->author;
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
		if( count( glsr()->reviewTypes ) < 2 )return;
		$reviewTypes = ['' => __( 'All types', 'site-reviews' )] + glsr()->reviewTypes;
		printf( '<label class="screen-reader-text" for="type">%s</label>', __( 'Filter by type', 'site-reviews' ));
		glsr( Html::class )->renderPartial( 'filterby', [
			'name' => 'review_type',
			'options' => $reviewTypes,
		]);
	}
}
