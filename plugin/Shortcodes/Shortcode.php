<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Rating;
use ReflectionClass;

abstract class Shortcode implements ShortcodeContract
{
	/**
	 * @var array
	 */
	protected $hiddenKeys;

	/**
	 * @param string|array $instance
	 * @return string
	 */
	public function build( $instance, array $args = [] )
	{
		$shortcodePartial = $this->getShortcodePartial();
		$args = wp_parse_args( $args, [
			'before_widget' => '<div class="glsr-shortcode shortcode-'.$shortcodePartial.'">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="glsr-shortcode-title">',
			'after_title' => '</h3>',
		]);
		$instance = $this->normalize( $instance );
		$partial = glsr( Html::class )->buildPartial( $shortcodePartial, $instance );
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
	 * @return string
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
	 * @param string $hide
	 * @return array
	 */
	protected function normalizeHide( $hide )
	{
		if( is_string( $hide )) {
			$hide = explode( ',', $hide );
		}
		return array_filter( array_map( 'trim', $hide ), function( $value ) {
			return in_array( $value, $this->hiddenKeys );
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
