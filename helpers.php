<?php
defined( 'WPINC' ) || die;

/**
 * @return mixed
 */
function glsr( $alias = null ) {
	$app = \GeminiLabs\SiteReviews\Application::load();
	return !empty( $alias )
		? $app->make( $alias )
		: $app;
}

/**
 * @return \WP_Screen|object
 */
function glsr_current_screen() {
	if( function_exists( 'get_current_screen' )) {
		$screen = get_current_screen();
	}
	return empty( $screen )
		? (object)array_fill_keys( ['base', 'id', 'post_type'], null )
		: $screen;
}

/**
 * @return \GeminiLabs\SiteReviews\Database
 */
function glsr_db() {
	return glsr( 'Database' );
}

/**
 * @param mixed ...$vars
 * @return void
 */
function glsr_debug( ...$vars ) {
	if( count( $vars ) == 1 ) {
		$value = htmlspecialchars( print_r( $vars[0], true ), ENT_QUOTES, 'UTF-8' );
		printf( '<div class="glsr-debug"><pre>%s</pre></div>', $value );
	}
	else {
		echo '<div class="glsr-debug-group">';
		foreach( $vars as $var ) {
			glsr_debug( $var );
		}
		echo '</div>';
	}
}

/**
 * @return \GeminiLabs\SiteReviews\Modules\Console
 */
function glsr_log() {
	$args = func_get_args();
	$context = isset( $args[1] )
		? $args[1]
		: [];
	$console = glsr( 'Modules\Console' );
	return !empty( $args )
		? $console->log( 'debug', $args[0], $context )
		: $console;
}

/**
 * @param string $path
 * @param mixed $fallback
 * @return string|array
 */
function glsr_get_option( $path = '', $fallback = '' ) {
	return glsr( 'Database\OptionManager' )->get( 'settings.'.$path, $fallback );
}

/**
 * @return array
 */
function glsr_get_options() {
	return glsr( 'Database\OptionManager' )->get( 'settings' );
}

/**
 * @param int $post_id
 * @return void|object
 */
function glsr_get_review( $post_id ) {
	return glsr( 'Database' )->getReview( get_post( $post_id ));
}

/**
 * @return array
 * @todo document change of $reviews->reviews to $reviews->results
 */
function glsr_get_reviews( array $args = array() ) {
	return glsr( 'Database' )->getReviews( $args );
}
