<?php

namespace GeminiLabs\SiteReviews\Modules;

use DateTime;
use GeminiLabs\SchemaOrg\Review as ReviewSchema;
use GeminiLabs\SchemaOrg\Schema as SchemaOrg;
use GeminiLabs\SchemaOrg\Thing as ThingSchema;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Rating;
use WP_Post;

class Schema
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected $reviews;

	/**
	 * @return array
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$schema = $this->buildSummary( $args );
		$reviews = [];
		foreach( glsr( Database::class )->getReviews( $this->args )->results as $review ) {
			$reviews[] = $this->buildReview( $review );
		}
		if( !empty( $reviews )) {
			array_walk( $reviews, function( &$review ) {
				unset( $review['@context'] );
				unset( $review['itemReviewed'] );
			});
			$schema['review'] = $reviews;
		}
		return $schema;
	}

	/**
	 * @param null|array $args
	 * @return array
	 */
	public function buildSummary( $args = null )
	{
		if( is_array( $args )) {
			$this->args = $args;
		}
		$buildSummary = glsr( Helper::class )->buildMethodName( $this->getSchemaOptionValue( 'type' ), 'buildSummaryFor' );
		$count = $this->getReviewCount();
		$schema = method_exists( $this, $buildSummary )
			? $this->$buildSummary()
			: $this->buildSummaryForCustom();
		if( !empty( $count )) {
			$schema->aggregateRating( SchemaOrg::AggregateRating()
				->ratingValue( $this->getRatingValue() )
				->reviewCount( $count )
				->bestRating( Rating::MAX_RATING )
				->worstRating( Rating::MIN_RATING )
			);
		}
		$schema = $schema->toArray();
		$args = wp_parse_args( ['count' => -1], $this->args );
		return apply_filters( sprintf( 'site-reviews/schema/%s', $schema['@type'] ), $schema, $args );
	}

	/**
	 * @return void
	 */
	public function render()
	{
		if( is_null( glsr()->schemas ))return;
		printf( '<script type="application/ld+json">%s</script>', json_encode(
			apply_filters( 'site-reviews/schema/all', glsr()->schemas ),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		));
	}

	/**
	 * @return void
	 */
	public function store( array $schema )
	{
		$schemas = (array) glsr()->schemas;
		$schemas[] = $schema;
		glsr()->schemas = array_map( 'unserialize', array_unique( array_map( 'serialize', $schemas )));
	}

	/**
	 * @param object $review
	 * @return array
	 */
	protected function buildReview( $review )
	{
		$schema = SchemaOrg::Review()
			->doIf( !in_array( 'title', $this->args['hide'] ), function( ReviewSchema $schema ) use( $review ) {
				$schema->name( $review->title );
			})
			->doIf( !in_array( 'excerpt', $this->args['hide'] ), function( ReviewSchema $schema ) use( $review ) {
				$schema->reviewBody( $review->content );
			})
			->datePublished(( new DateTime( $review->date ))->format( DateTime::ISO8601 ))
			->author( SchemaOrg::Person()->name( $review->author ))
			->itemReviewed( $this->getSchemaType()->name( $this->getSchemaOptionValue( 'name' )));
		if( !empty( $review->rating )) {
			$schema->reviewRating( SchemaOrg::Rating()
				->ratingValue( $review->rating )
				->bestRating( Rating::MAX_RATING )
				->worstRating( Rating::MIN_RATING )
			);
		}
		return apply_filters( 'site-reviews/schema/review', $schema->toArray(), $review, $this->args );
	}

	/**
	 * @return ThingSchema
	 */
	protected function buildSchemaValues( ThingSchema $schema, array $values = [] )
	{
		foreach( $values as $value ) {
			$option = $this->getSchemaOptionValue( $value );
			if( empty( $option ))continue;
			$schema->$value( $option );
		}
		return $schema;
	}

	/**
	 * @return ThingSchema
	 */
	protected function buildSummaryForCustom()
	{
		return $this->buildSchemaValues( $this->getSchemaType(), [
			'description', 'image', 'name', 'url',
		]);
	}

	/**
	 * @return ThingSchema
	 */
	protected function buildSummaryForLocalBusiness()
	{
		return $this->buildSchemaValues( $this->buildSummaryForCustom(), [
			'address', 'priceRange', 'telephone',
		]);
	}

	/**
	 * @return ThingSchema
	 */
	protected function buildSummaryForProduct()
	{
		$offers = $this->buildSchemaValues( SchemaOrg::AggregateOffer(), [
			'highPrice', 'lowPrice', 'priceCurrency',
		]);
		return $this->buildSummaryForCustom()
			->offers( $offers )
			->setProperty( '@id', $this->getSchemaOptionValue( 'url' ));
	}

	/**
	 * @return int|float
	 */
	protected function getRatingValue()
	{
		return glsr( Rating::class )->getAverage( $this->getReviews() );
	}

	/**
	 * @return int
	 */
	protected function getReviewCount()
	{
		return count( $this->getReviews() );
	}

	/**
	 * @return array
	 */
	protected function getReviews( $force = false )
	{
		if( !isset( $this->reviews ) || $force ) {
			$args = wp_parse_args( ['count' => -1], $this->args );
			$this->reviews = glsr( Database::class )->getReviews( $args )->results;
		}
		return $this->reviews;
	}

	/**
	 * @param string $option
	 * @param string $fallback
	 * @return string
	 */
	protected function getSchemaOption( $option, $fallback )
	{
		$option = strtolower( $option );
		if( $schemaOption = trim( (string)get_post_meta( intval( get_the_ID() ), 'schema_'.$option, true ))) {
			return $schemaOption;
		}
		$setting = glsr( OptionManager::class )->get( 'settings.schema.'.$option );
		if( is_array( $setting )) {
			return $this->getSchemaOptionDefault( $setting, $fallback );
		}
		return !empty( $setting )
			? $setting
			: $fallback;
	}

	/**
	 * @param string $setting
	 * @param string $fallback
	 * @return string
	 */
	protected function getSchemaOptionDefault( $setting, $fallback )
	{
		$setting = wp_parse_args( $setting, [
			'custom' => '',
			'default' => $fallback,
		]);
		return $setting['default'] != 'custom'
			? $setting['default']
			: $setting['custom'];
	}

	/**
	 * @param string $option
	 * @param string $fallback
	 * @return void|string
	 */
	protected function getSchemaOptionValue( $option, $fallback = 'post' )
	{
		$value = $this->getSchemaOption( $option, $fallback );
		if( $value != $fallback ) {
			return $value;
		}
		if( !is_single() && !is_page() )return;
		// @todo make this dynamic
		switch( $option ) {
			case 'description':
				return $this->getThingDescription();
			case 'image':
				return $this->getThingImage();
			case 'name':
				return $this->getThingName();
			case 'url':
				return $this->getThingUrl();
		}
	}

	/**
	 * @return \GeminiLabs\SchemaOrg\Type
	 */
	protected function getSchemaType()
	{
		$type = $this->getSchemaOption( 'type', 'LocalBusiness' );
		return SchemaOrg::$type( $type );
	}

	/**
	 * @return string
	 */
	protected function getThingDescription()
	{
		$post = get_post();
		if( !( $post instanceof WP_Post )) {
			return '';
		}
		$text = strip_shortcodes( wp_strip_all_tags( $post->post_excerpt ));
		return wp_trim_words( $text, apply_filters( 'excerpt_length', 55 ));
	}

	/**
	 * @return string
	 */
	protected function getThingImage()
	{
		return (string)get_the_post_thumbnail_url( null, 'large' );
	}

	/**
	 * @return string
	 */
	protected function getThingName()
	{
		return get_the_title();
	}

	/**
	 * @return string
	 */
	protected function getThingUrl()
	{
		return (string)get_the_permalink();
	}
}
