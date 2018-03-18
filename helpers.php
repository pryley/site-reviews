<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     2.7.4
 * -------------------------------------------------------------------------------------------------
 */

defined( 'WPINC' ) || die;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Log\LogLevel;

/**
 * Global helper to return $app
 *
 * @return App
 */
function glsr_app() {
	return App::load();
}

/**
 * Global helper to debug variables
 *
 * @return void
 */
function glsr_debug() {
	call_user_func_array([ App::load()->make( 'Log\Logger' ), 'display'], func_get_args());
}

/**
 * Global helper to log variables
 *
 * @return void
 */
function glsr_log( $message, $level = 'debug' ) {
	$levels = array_values((new ReflectionClass( 'GeminiLabs\SiteReviews\Log\LogLevel' ))->getConstants());
	if( !in_array( $level, $levels )) {
		$level = LogLevel::DEBUG;
	}
	App::load()->make( 'Log\Logger' )->log( $level, $message );
}

/**
 * Global helper to get a plugin option
 *
 * @param string $option_path
 * @param mixed $fallback
 * @return mixed
 */
function glsr_get_option( $option_path = '', $fallback = '' ) {
	return App::load()->make( 'Helper' )->get( 'option', $option_path, $fallback );
}

/**
 * Global helper to get all plugin options
 *
 * @return array
 */
function glsr_get_options() {
	return App::load()->make( 'Helper' )->get( 'options' );
}

/**
 * Global helper to get a single review
 *
 * @param int $post_id
 * @return null|object
 */
function glsr_get_review( $post_id ) {
	return App::load()->make( 'Helper' )->get( 'review', $post_id );
}

/**
 * Global helper to get an array of reviews
 *
 * @return array
 */
function glsr_get_reviews( array $args = array() ) {
	return App::load()->make( 'Helper' )->get( 'reviews', $args );
}

/**
 * Global helper to resolve a class instance where $app is not accessible
 *
 * @param string $alias
 * @return class
 */
function glsr_resolve( $alias ) {
	return App::load()->make( $alias );
}

/**
 * register_taxonomy() 'meta_box_cb' callback
 *
 * This function prevents the taxonomy object from containing class recursion
 *
 * @return void
 */
function glsr_categories_meta_box( $post, $box ) {
	App::load()->make( 'Controllers\MainController' )->renderTaxonomyMetabox( $post, $box );
}

/**
 * get_current_screen() is unreliable because it is defined on most admin pages, but not all.
 *
 * @return WP_Screen|null
 */
function glsr_current_screen() {
	if( function_exists( 'get_current_screen' )) {
		$screen = get_current_screen();
	}
	return empty( $screen )
		? (object)array_fill_keys( ['base', 'id', 'post_type', 'parent_base'], null )
		: $screen;
}
