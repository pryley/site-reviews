<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Polylang;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;
use IntlRuleBasedBreakIterator;
use WP_Post;

class SiteReviews
{
	/**
	 * @var array
	 */
	public $args;

	/**
	 * @var Review
	 */
	public $current;

	/**
	 * @var array
	 */
	public $options;

	/**
	 * @var Reviews
	 */
	protected $reviews;

	/**
	 * @param Reviews|null $reviews
	 * @return void|string
	 */
	public function build( array $args = [], $reviews = null )
	{
		$this->args = $args;
		$this->options = glsr( Helper::class )->flattenArray( glsr( OptionManager::class )->all() );
		$this->reviews = $reviews instanceof Reviews
			? $reviews
			: glsr( ReviewManager::class )->get( $args );
		$this->generateSchema();
		return $this->buildReviews();
	}

	/**
	 * @return ReviewHtml
	 */
	public function buildReview( Review $review )
	{
		$review = apply_filters( 'site-reviews/review/build/before', $review );
		$this->current = $review;
		$renderedFields = [];
		foreach( $review as $key => $value ) {
			$method = glsr( Helper::class )->buildMethodName( $key, 'buildOption' );
			$field = method_exists( $this, $method )
				? $this->$method( $key, $value )
				: apply_filters( 'site-reviews/review/build/'.$key, false, $value, $this, $review );
			if( $field === false )continue;
			$renderedFields[$key] = $field;
		}
		$this->wrap( $renderedFields, $review );
		$renderedFields = apply_filters( 'site-reviews/review/build/after', $renderedFields, $review );
		$this->current = null;
		return new ReviewHtml( (array)$renderedFields );
	}

	/**
	 * @return ReviewsHtml
	 */
	public function buildReviews()
	{
		$renderedReviews = [];
		foreach( $this->reviews as $index => $review ) {
			$renderedReviews[] = glsr( Template::class )->build( 'templates/review', [
				'context' => $this->buildReview( $review )->values,
			]);
		}
		return new ReviewsHtml( $renderedReviews, $this->reviews->max_num_pages, $this->args );
	}

	/**
	 * @return void
	 */
	public function generateSchema()
	{
		if( !wp_validate_boolean( $this->args['schema'] ))return;
		glsr( Schema::class )->store(
			glsr( Schema::class )->build( $this->args )
		);
	}

	/**
	 * @param string $key
	 * @param string $path
	 * @return bool
	 */
	public function isHidden( $key, $path = '' )
	{
		$isOptionEnabled = !empty( $path )
			? $this->isOptionEnabled( $path )
			: true;
		return in_array( $key, $this->args['hide'] ) || !$isOptionEnabled;
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
		return glsr_star_rating( $value );
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
		if( !$this->isOptionEnabled( 'settings.reviews.avatars_regenerate' ) || $this->current->review_type != 'local' ) {
			return $avatarUrl;
		}
		$authorIdOrEmail = get_the_author_meta( 'ID', $this->current->user_id );
		if( empty( $authorIdOrEmail )) {
			$authorIdOrEmail = $this->current->email;
		}
		if( $newAvatar = get_avatar_url( $authorIdOrEmail )) {
			return $newAvatar;
		}
		return $avatarUrl;
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
		return $text;
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
		return wptexturize( nl2br( $text ));
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
