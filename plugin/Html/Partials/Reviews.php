<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Html\Partials\Base;

class Reviews extends Base
{
	protected static $HIDDEN_KEYS = ['author', 'avatar', 'date', 'excerpt', 'rating', 'title'];

	/**
	 * @return null|string
	 */
	public function render()
	{
		$this->normalize();
		$this->buildSchema();
		if( $this->isHidden() )return;
		$html = '';
		$reviews = $this->db->getReviews( $this->args );
		foreach( $reviews->reviews as $review ) {
			$html .= $this->renderReview( $review );
		}
		if( empty( $reviews->reviews )) {
			$html = sprintf( '<p class="glsr-review glsr-no-reviews">%s</p>',
				__( 'No reviews were found.', 'site-reviews' )
			);
		}
		else if( $this->args['pagination'] ) {
			$html .= $this->buildPagination( $reviews->max_num_pages );
		}
		return sprintf(
			'<div class="glsr-reviews-wrap"><div class="glsr-reviews %s" id="%s">%s</div></div>',
			$this->args['class'],
			$this->args['id'],
			$html
		);
	}

	/**
	 * @return string
	 */
	protected function renderReview( $review )
	{
		$rendered = apply_filters( 'site-reviews/rendered/review/order', [
			'title' => $this->buildTitle( $review ),
			'meta' => $this->buildMeta( $review ),
			'review' => $this->buildText( $review ),
			'avatar' => $this->buildAvatar( $review->avatar ),
			'author' => $this->buildAuthor( $review->author ),
			'response' => $this->buildResponse( $review->response ),
		]);
		$renderedReview = sprintf( '<div class="glsr-review">%s</div>',
			array_reduce( $rendered, function( $carry, $part ) {
				return $carry . $part;
			})
		);
		return apply_filters( 'site-reviews/rendered/review', $renderedReview, $rendered, $review );
	}

	/**
	 * @param int|string $assignedTo
	 * @return null|string
	 */
	protected function buildAssignedLink( $assignedTo )
	{
		if( $this->db->getOption( 'settings.reviews.assigned_links.enabled' ) != 'yes' )return;
		$permalink = $this->app->make( 'Html' )->renderPartial( 'link', [
			'post_id' => $assignedTo,
		]);
		if( empty( $permalink ))return;
		return sprintf( '<span class="glsr-review-assigned">%s</span>',
			sprintf( __( 'Review of %s', 'site-reviews' ), $permalink )
		);
	}

	/**
	 * @param string $author
	 * @return null|string
	 */
	protected function buildAuthor( $author )
	{
		if( in_array( 'author', $this->args['hide'] ))return;
		$dash = $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes'
			? '&mdash;'
			: '';
		return sprintf( '<p class="glsr-review-author">%s<span>%s</span></p>', $dash, $author );
	}

	/**
	 * @param string $avatar
	 * @return null|string
	 */
	protected function buildAvatar( $avatar )
	{
		if( in_array( 'avatar', $this->args['hide'] )
			|| $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes'
		)return;
		return sprintf( '<div class="glsr-review-avatar"><img src="%s" width="36"></div>', $avatar );
	}

	/**
	 * @param string $date
	 * @return null|string
	 */
	protected function buildDate( $date )
	{
		if( in_array( 'date', $this->args['hide'] ))return;
		$dateFormat = $this->db->getOption( 'settings.reviews.date.format', 'default' );
		if( $dateFormat == 'relative' ) {
			$date = $this->app->make( 'Date' )->relative( $date );
		}
		else {
			$format = $dateFormat == 'custom'
				? glsr_get_option( 'reviews.date.custom', 'M j, Y' )
				: (string) get_option( 'date_format' );
			$date = date_i18n( $format, strtotime( $date ));
		}
		return sprintf( '<span class="glsr-review-date">%s</span>', $date );
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildMeta( $review )
	{
		if( in_array( 'date', $this->args['hide'] )
			&& empty( $review->rating )
			&& empty( $review->assigned_to )
		)return;
		$rendered = apply_filters( 'site-reviews/rendered/review/meta/order', [
			'rating' => $this->buildRating( $review->rating ),
			'date' => $this->buildDate( $review->date ),
			'assigned_link' => $this->buildAssignedLink( $review->assigned_to ),
		]);
		return sprintf( '<p class="glsr-review-meta">%s</p>',
			array_reduce( $rendered, function( $carry, $part ) {
				return $carry . $part;
			})
		);
	}

	/**
	 * @param int $maxPageNum
	 * @return string|null
	 */
	protected function buildPagination( $maxPageNum )
	{
		if( $maxPageNum < 2 )return;
		$paged = $this->app->make( 'Query' )->getPaged();
		$links = $this->buildPaginationForDeprecatedThemes( $maxPageNum, $paged );
		if( empty( $links )) {
			$paginateArgs = [
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'site-reviews' ) . ' </span>',
				'current' => $paged,
				'format' => '?'.App::PAGED_QUERY_VAR.'=%#%',
				'mid_size' => 1,
				'next_text' => __( 'Next &rarr;', 'site-reviews' ),
				'prev_text' => __( '&larr; Previous', 'site-reviews' ),
				'total' => $maxPageNum,
			];
			if( is_front_page() ) {
				unset( $paginateArgs['format'] );
			}
			$links = paginate_links( $paginateArgs );
		}
		$links = apply_filters( 'site-reviews/reviews/navigation_links', $links, $paged, $maxPageNum );
		if( empty( $links ))return;
		return $this->getPaginationTemplate( $links );
	}

	/**
	 * @param int $maxPageNum
	 * @param int $paged
	 * @return string|void
	 */
	protected function buildPaginationForDeprecatedThemes( $maxPageNum, $paged )
	{
		$theme = wp_get_theme()->get( 'TextDomain' );
		if( !in_array( $theme, ['twentyten','twentyeleven','twentytwelve','twentythirteen'] ))return;
		$links = '';
		if( $paged > 1 ) {
			$links .= sprintf( '<div class="nav-previous"><a href="%s"><span class="meta-nav">&larr;</span> %s</a></div>',
				$this->buildPaginationUrlForDeprecatedThemes( $paged, -1 ),
				__( 'Previous', 'site-reviews' )
			);
		}
		if( $paged < $maxPageNum ) {
			$links .= sprintf( '<div class="nav-next"><a href="%s">%s <span class="meta-nav">&rarr;</span></a></div>',
				$this->buildPaginationUrlForDeprecatedThemes( $paged, 1 ),
				__( 'Next', 'site-reviews' )
			);
		}
		return $links;
	}

	/**
	 * @param int $paged
	 * @param int $pageIncrement
	 * @return string
	 */
	protected function buildPaginationUrlForDeprecatedThemes( $paged, $pageIncrement )
	{
		if( is_front_page() ) {
			return get_pagenum_link( $paged + $pageIncrement );
		}
		return add_query_arg( App::PAGED_QUERY_VAR, $paged + $pageIncrement, get_pagenum_link() );
	}

	/**
	 * @param int $rating
	 * @return string
	 */
	protected function buildRating( $rating )
	{
		if( empty( $rating ))return;
		return $this->app->make( 'Html' )->renderPartial( 'star-rating', [
			'hidden' => in_array( 'rating', $this->args['hide'] ),
			'rating' => $rating,
		]);
	}

	/**
	 * @param string $response
	 * @return null|string
	 */
	protected function buildResponse( $response )
	{
		if( in_array( 'response', $this->args['hide'] ) || empty( $response ))return;
		$title = sprintf( __( 'Response from %s', 'site-reviews' ), get_bloginfo( 'name' ));
		$text = $this->normalizeText( $response );
		$text = $this->getExcerpt( $text );
		return sprintf( '<div class="glsr-review-response"><p><strong>%s</strong></p><p>%s</p></div>',
			$title,
			$text
		);
	}

	/**
	 * @return void
	 */
	protected function buildSchema()
	{
		if( !$this->args['schema'] )return;
		$schema = $this->app->make( 'Schema' );
		$schema->store( $schema->build( $this->args ));
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildText( $review )
	{
		if( in_array( 'excerpt', $this->args['hide'] ))return;
		$text = $this->normalizeText( $review->content );
		$text = $this->getExcerpt( $text );
		$text = apply_filters( 'site-reviews/reviews/review/text', $text, $review, $this->args );
		if( empty( $text ))return;
		return sprintf( '<div class="glsr-review-excerpt"><p>%s</p></div>', $text );
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildTitle( $review )
	{
		if( in_array( 'title', $this->args['hide'] ))return;
		if( empty( $review->title )) {
			$review->title = __( 'No Title', 'site-reviews' );
		}
		$title = sprintf( '<h3 class="glsr-review-title">%s</h3>', $review->title );
		return apply_filters( 'site-reviews/reviews/review/title', $title, $review, $this->args );
	}

	/**
	 * @param string $text
	 * @return string
	 */
	protected function getExcerpt( $text )
	{
		if( $this->db->getOption( 'settings.reviews.excerpt.enabled' ) != 'yes' ) {
			return $text;
		}
		$limit = $this->db->getOption( 'settings.reviews.excerpt.length', $this->args['word_limit'] );
		$excerpt = $text;
		if( str_word_count( $text, 0 ) > $limit ) {
			$words = str_word_count( $text, 2 );
			$pos = array_keys( $words );
			$excerpt = ltrim( substr( $text, 0, $pos[$limit] ));
			$hiddenText = substr( $text, strlen( $excerpt ));
		}
		if( empty( $hiddenText )) {
			return $text;
		}
		return nl2br( sprintf( '%s<span class="glsr-hidden glsr-hidden-text" data-show-more="%s" data-show-less="%s">%s</span>',
			$excerpt,
			__( 'Show more', 'site-reviews' ),
			__( 'Show less', 'site-reviews' ),
			$hiddenText
		));
	}

	/**
	 * @param string $links
	 * @return string
	 */
	protected function getPaginationTemplate( $links )
	{
		$theme = wp_get_theme()->get( 'TextDomain' );
		$class = $this->args['pagination'] === 'ajax'
			? 'glsr-ajax-navigation navigation '
			: 'glsr-navigation navigation ';
		switch( $theme ) {
			case 'twentyten':
			case 'twentyeleven':
			case 'twentytwelve':
				$template = '<nav class="%1$s" role="navigation">%3$s</nav>';
				break;
			case 'twentyfourteen':
				$class .= 'paging-navigation';
				$template = '<nav class="%1$s" role="navigation"><h2 class="screen-reader-text">%2$s</h2><div class="pagination loop-pagination">%3$s</div></nav>';
				break;
			default:
				$class .= 'pagination';
				$template = '<nav class="%1$s" role="navigation"><h2 class="screen-reader-text">%2$s</h2><div class="nav-links">%3$s</div></nav>';
		}
		$template = apply_filters( 'navigation_markup_template', $template, $class );
		$screenReaderText = __( 'Site Reviews navigation', 'site-reviews' );
		return sprintf( $template, $class, $screenReaderText, $links ) . '<div class="glsr-loader"></div>';
	}

	/**
	 * @return void
	 */
	protected function normalize()
	{
		$defaults = [
			'assigned_to' => '',
			'category'    => '',
			'class'       => '',
			'count'       => '',
			'hide'        => '',
			'id'          => '',
			'offset'      => '',
			'orderby'     => 'date',
			'pagination'  => false,
			'rating'      => '1',
			'schema'      => false,
			'type'        => '',
			'word_limit'  => 55,
		];
		$this->args = shortcode_atts( $defaults, $this->args );
		array_walk( $this->args, function( &$value, $key ) {
			$methodName = $this->app->make( 'Helper' )->buildMethodName( $key, 'normalize' );
			if( !method_exists( $this, $methodName ))return;
			$value = $this->$methodName( $value );
		});
	}

	/**
	 * @return array
	 */
	protected function normalizeHide( $hide )
	{
		return array_filter(( array ) $hide );
	}

	/**
	 * @return bool
	 */
	protected function normalizePagination( $pagination )
	{
		if( $pagination !== 'ajax' ) {
			$pagination = wp_validate_boolean( $pagination );
		}
		return $pagination;
	}

	/**
	 * @return bool
	 */
	protected function normalizeSchema( $schema )
	{
		return wp_validate_boolean( $schema );
	}

	/**
	 * @return string
	 */
	protected function normalizeText( $text )
	{
		$text = wp_kses( $text, [
			'a' => ['href' => [], 'title' => []],
			'em' => [],
			'strong' => [],
		]);
		$text = strip_shortcodes( $text );
		$text = wptexturize( $text );
		$text = convert_smilies( $text );
		$text = str_replace( ']]>', ']]&gt;', $text );
		return $text;
	}

	/**
	 * @return bool
	 */
	protected function isHidden( array $values = [] )
	{
		if( empty( $values )) {
			$values = static::$HIDDEN_KEYS;
		}
		return !array_diff( $values, $this->args['hide'] );
	}
}
