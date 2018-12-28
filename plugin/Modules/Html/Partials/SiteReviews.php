<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Polylang;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Review;
use IntlRuleBasedBreakIterator;
use WP_Post;

class SiteReviews
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var int
	 */
	protected $current;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var object
	 */
	protected $reviews;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$this->options = glsr( Helper::class )->flattenArray( glsr( OptionManager::class )->all() );
		$this->reviews = glsr( ReviewManager::class )->get( $args );
		$this->generateSchema();
		$navigation = wp_validate_boolean( $this->args['pagination'] )
			? glsr( Partial::class )->build( 'pagination', ['total' => $this->reviews->max_num_pages] )
			: '';
		return glsr( Template::class )->build( 'templates/reviews', [
			'context' => [
				'assigned_to' => $this->args['assigned_to'],
				'class' => $this->getClass(),
				'id' => $this->args['id'],
				'navigation' => $navigation,
				'reviews' => $this->buildReviews(),
			],
		]);
	}

	/**
	 * @return string
	 */
	public function buildReviews()
	{
		$reviews = '';
		foreach( $this->reviews->results as $index => $result ) {
			$this->current = $index;
			$reviews.= glsr( Template::class )->build( 'templates/review', [
				'context' => $this->buildReview( $result )->values,
			]);
		}
		return $reviews;
	}

	/**
	 * @param Review $review
	 * @return ReviewHtml
	 */
	protected function buildReview( Review $review )
	{
		$review = apply_filters( 'site-reviews/review/build/before', $review ); // @todo make this an arrayable object!
		$renderedFields = [];
		foreach( (array)$review as $key => $value ) {
			$method = glsr( Helper::class )->buildMethodName( $key, 'buildOption' );
			if( !method_exists( $this, $method ))continue;
			$renderedFields[$key] = $this->$method( $key, $value );
		}
		$this->wrap( $renderedFields, $review );
		$renderedFields = apply_filters( 'site-reviews/review/build/after', $renderedFields, $review );
		return new ReviewHtml( (array)$renderedFields );
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionAssignedTo( $key, $value )
	{
		if( $this->isHidden( $key, 'settings.reviews.assigned_links' ))return;
		$post = glsr( Polylang::class )->getPost( $value );
		if( !( $post instanceof WP_Post ))return;
		$permalink = glsr( Builder::class )->a( get_the_title( $post->ID ), [
			'href' => get_the_permalink( $post->ID ),
		]);
		$assignedTo = sprintf( __( 'Review of %s', 'site-reviews' ), $permalink );
		return '<span>'.$assignedTo.'</span>';
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionAuthor( $key, $value )
	{
		if( $this->isHidden( $key ))return;
		return '<span>'.$value.'</span>';
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionAvatar( $key, $value )
	{
		if( $this->isHidden( $key, 'settings.reviews.avatars' ))return;
		$size = $this->getOption( 'settings.reviews.avatars_size', 40 );
		return glsr( Builder::class )->img([
			'height' => $size,
			'src' => $this->generateAvatar( $value ),
			'style' => sprintf( 'width:%1$spx; height:%1$spx;', $size ),
			'width' => $size,
		]);
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionContent( $key, $value )
	{
		$text = $this->normalizeText( $value );
		if( $this->isHiddenOrEmpty( $key, $text ))return;
		return '<p>'.$text.'</p>';
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionDate( $key, $value )
	{
		if( $this->isHidden( $key ))return;
		$dateFormat = $this->getOption( 'settings.reviews.date.format', 'default' );
		if( $dateFormat == 'relative' ) {
			$date = glsr( Date::class )->relative( $value );
		}
		else {
			$format = $dateFormat == 'custom'
				? $this->getOption( 'settings.reviews.date.custom', 'M j, Y' )
				: (string)get_option( 'date_format' );
			$date = date_i18n( $format, strtotime( $value ));
		}
		return '<span>'.$date.'</span>';
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionRating( $key, $value )
	{
		if( $this->isHiddenOrEmpty( $key, $value ))return;
		return glsr( Partial::class )->build( 'star-rating', [
			'rating' => $value,
		]);
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionResponse( $key, $value )
	{
		if( $this->isHiddenOrEmpty( $key, $value ))return;
		$title = sprintf( __( 'Response from %s', 'site-reviews' ), get_bloginfo( 'name' ));
		$text = $this->normalizeText( $value );
		$text = '<p><strong>'.$title.'</strong></p><p>'.$text.'</p>';
		$response = glsr( Builder::class )->div( $text, ['class' => 'glsr-review-response-inner'] );
		$background = glsr( Builder::class )->div( ['class' => 'glsr-review-response-background'] );
		return $response.$background;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void|string
	 */
	protected function buildOptionTitle( $key, $value )
	{
		if( $this->isHidden( $key ))return;
		if( empty( $value )) {
			$value = __( 'No Title', 'site-reviews' );
		}
		return '<h3>'.$value.'</h3>';
	}

	/**
	 * @param string $avatarUrl
	 * @return string
	 */
	protected function generateAvatar( $avatarUrl )
	{
		$review = $this->reviews->results[$this->current];
		if( !$this->isOptionEnabled( 'settings.reviews.avatars_regenerate' ) || $review->review_type != 'local' ) {
			return $avatarUrl;
		}
		$authorIdOrEmail = get_the_author_meta( 'ID', $review->user_id );
		if( empty( $authorIdOrEmail )) {
			$authorIdOrEmail = $review->email;
		}
		if( $newAvatar = get_avatar_url( $authorIdOrEmail )) {
			return $newAvatar;
		}
		return $avatarUrl;
	}

	/**
	 * @return void
	 */
	protected function generateSchema()
	{
		if( !wp_validate_boolean( $this->args['schema'] ))return;
		glsr( Schema::class )->store(
			glsr( Schema::class )->build( $this->args )
		);
	}

	/**
	 * @return string
	 */
	protected function getClass()
	{
		$pagination = $this->args['pagination'] == 'ajax'
			? 'glsr-ajax-pagination'
			: '';
		return trim( 'glsr-reviews glsr-default '.$pagination.' '.$this->args['class'] );
	}

	/**
	 * @param string $text
	 * @return string
	 */
	protected function getExcerpt( $text )
	{
		$limit = intval( $this->getOption( 'settings.reviews.excerpts_length', 55 ));
		$split = extension_loaded( 'intl' )
			? $this->getExcerptIntlSplit( $text, $limit )
			: $this->getExcerptSplit( $text, $limit );
		$hiddenText = substr( $text, $split );
		if( !empty( $hiddenText )) {
			$showMore = glsr( Builder::class )->span( $hiddenText, [
				'class' => 'glsr-hidden glsr-hidden-text',
				'data-show-less' => __( 'Show less', 'site-reviews' ),
				'data-show-more' => __( 'Show more', 'site-reviews' ),
			]);
			$text = ltrim( substr( $text, 0, $split )).$showMore;
		}
		return nl2br( $text );
	}

	/**
	 * @param string $text
	 * @param int $limit
	 * @return int
	 */
	protected function getExcerptIntlSplit( $text, $limit )
	{
		$words = IntlRuleBasedBreakIterator::createWordInstance( '' );
		$words->setText( $text );
		$count = 0;
		foreach( $words as $offset ){
			if( $words->getRuleStatus() === IntlRuleBasedBreakIterator::WORD_NONE )continue;
			$count++;
			if( $count != $limit )continue;
			return $offset;
		}
		return strlen( $text );
	}

	/**
	 * @param string $text
	 * @param int $limit
	 * @return int
	 */
	protected function getExcerptSplit( $text, $limit )
	{
		if( str_word_count( $text, 0 ) > $limit ) {
			$words = array_keys( str_word_count( $text, 2 ));
			return $words[$limit];
		}
		return strlen( $text );
	}

	/**
	 * @param string $path
	 * @param mixed $fallback
	 * @return mixed
	 */
	protected function getOption( $path, $fallback = '' )
	{
		if( array_key_exists( $path, $this->options )) {
			return $this->options[$path];
		}
		return $fallback;
	}

	/**
	 * @param string $key
	 * @param string $path
	 * @return bool
	 */
	protected function isHidden( $key, $path = '' )
	{
		$isOptionEnabled = !empty( $path )
			? $this->isOptionEnabled( $path )
			: true;
		return in_array( $key, $this->args['hide'] ) || !$isOptionEnabled;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return bool
	 */
	protected function isHiddenOrEmpty( $key, $value )
	{
		return $this->isHidden( $key ) || empty( $value );
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	protected function isOptionEnabled( $path )
	{
		return $this->getOption( $path ) == 'yes';
	}

	/**
	 * @param string $text
	 * @return string
	 */
	protected function normalizeText( $text )
	{
		$text = wp_kses( $text, wp_kses_allowed_html() );
		$text = convert_smilies( strip_shortcodes( $text ));
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = preg_replace( '/(\R){2,}/', '$1', $text );
		if( $this->isOptionEnabled( 'settings.reviews.excerpts' )) {
			$text = $this->getExcerpt( $text );
		}
		return wptexturize( $text );
	}

	/**
	 * @return void
	 */
	protected function wrap( array &$renderedFields, Review $review )
	{
		$renderedFields = apply_filters( 'site-reviews/review/wrap', $renderedFields, $review );
		array_walk( $renderedFields, function( &$value, $key ) use( $review ) {
			$value = apply_filters( 'site-reviews/review/wrap/'.$key, $value, $review );
			if( empty( $value ))return;
			$value = glsr( Builder::class )->div( $value, [
				'class' => 'glsr-review-'.$key,
			]);
		});
	}
}
