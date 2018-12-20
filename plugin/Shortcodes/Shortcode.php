<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Rating;
use ReflectionClass;

abstract class Shortcode implements ShortcodeContract
{
	/**
	 * @param string|array $instance
	 * @param string $type
	 * @return string
	 */
	public function build( $instance, array $args = [], $type = 'shortcode' )
	{
		$shortcodePartial = $this->getShortcodePartial();
		$args = wp_parse_args( $args, [
			'before_widget' => '<div class="glsr-'.$type.' '.$type.'-'.$shortcodePartial.'">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="glsr-'.$type.'-title">',
			'after_title' => '</h3>',
		]);
		$args = apply_filters( 'site-reviews/shortcode/args', $args, $type, $shortcodePartial );
		$instance = $this->normalize( $instance );
		$partial = glsr( Partial::class )->build( $shortcodePartial, $instance );
		if( !empty( $instance['title'] )) {
			$instance['title'] = $args['before_title'].$instance['title'].$args['after_title'];
		}
		return $args['before_widget'].$instance['title'].$partial.$args['after_widget'];
	}

	/**
	 * @param string|array $atts
	 * @return string
	 */
	public function buildShortcode( $atts = [] )
	{
		return $this->build( $atts );
	}

	/**
	 * @return array
	 */
	public function getDefaults()
	{
		$className = glsr( Helper::class )->buildClassName(
			str_replace( 'Shortcode', 'Defaults', (new ReflectionClass( $this ))->getShortName() ),
			'Defaults'
		);
		return glsr( $className )->defaults();
	}

	/**
	 * @return array
	 */
	public function getHiddenKeys()
	{
		$hiddenKeys = defined( 'static::HIDDEN_KEYS' )
			? static::HIDDEN_KEYS
			: [];
		$shortcode = glsr( Helper::class )->snakeCase( $this->getShortcodePartial() );
		return apply_filters( 'site-reviews/shortcode/hidden-keys', $hiddenKeys, $shortcode );
	}

	/**
	 * @return string
	 */
	public function getShortcodePartial()
	{
		return glsr( Helper::class )->dashCase(
			str_replace( 'Shortcode', '', (new ReflectionClass( $this ))->getShortName() )
		);
	}

	/**
	 * @param array|string $args
	 * @return array
	 */
	public function normalize( $args )
	{
		$args = shortcode_atts( $this->getDefaults(), wp_parse_args( $args ));
		array_walk( $args, function( &$value, $key ) {
			$methodName = glsr( Helper::class )->buildMethodName( $key, 'normalize' );
			if( !method_exists( $this, $methodName ))return;
			$value = $this->$methodName( $value );
		});
		return $this->sanitize( $args );
	}

	/**
	 * @param string $postId
	 * @return int|string
	 */
	protected function normalizeAssignedTo( $postId )
	{
		return $postId == 'post_id'
			? intval( get_the_ID() )
			: $postId;
	}

	/**
	 * @param string $postId
	 * @return int|string
	 */
	protected function normalizeAssignTo( $postId )
	{
		return $this->normalizeAssignedTo( $postId );
	}

	/**
	 * @param string|array $hide
	 * @return array
	 */
	protected function normalizeHide( $hide )
	{
		if( is_string( $hide )) {
			$hide = explode( ',', $hide );
		}
		$hiddenKeys = $this->getHiddenKeys();
		return array_filter( array_map( 'trim', $hide ), function( $value ) use( $hiddenKeys ) {
			return in_array( $value, $hiddenKeys );
		});
	}

	/**
	 * @param string $id
	 * @return string
	 */
	protected function normalizeId( $id )
	{
		return sanitize_title( $id );
	}

	/**
	 * @param string $labels
	 * @return array
	 */
	protected function normalizeLabels( $labels )
	{
		$defaults = [
			__( 'Excellent', 'site-reviews' ),
			__( 'Very good', 'site-reviews' ),
			__( 'Average', 'site-reviews' ),
			__( 'Poor', 'site-reviews' ),
			__( 'Terrible', 'site-reviews' ),
		];
		$defaults = array_pad( $defaults, Rating::MAX_RATING, '' );
		$labels = array_map( 'trim', explode( ',', $labels ));
		foreach( $defaults as $i => $label ) {
			if( empty( $labels[$i] ))continue;
			$defaults[$i] = $labels[$i];
		}
		return array_combine( range( Rating::MAX_RATING, 1 ), $defaults );
	}

	/**
	 * @param string $schema
	 * @return bool
	 */
	protected function normalizeSchema( $schema )
	{
		return wp_validate_boolean( $schema );
	}

	/**
	 * @param string $text
	 * @return string
	 */
	protected function normalizeText( $text )
	{
		return trim( $text );
	}

	/**
	 * @return array
	 */
	protected function sanitize( array $args )
	{
		return $args;
	}
}
