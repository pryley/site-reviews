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
 * @return false|\GeminiLabs\SiteReviews\Review
 */
function glsr_create_review( array $reviewValues = [] ) {
	if( empty( $reviewValues )) {
		return false;
	}
	$review = new \GeminiLabs\SiteReviews\Commands\CreateReview( $reviewValues );
	return glsr( 'Database\ReviewManager' )->create( $review );
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
 * @return void|\GeminiLabs\SiteReviews\Review
 */
function glsr_get_review( $post_id ) {
	$post = get_post( $post_id );
	if( $post instanceof WP_Post ) {
		return glsr( 'Database\ReviewManager' )->single( $post );
	}
}

/**
 * @return array
 * @todo document change of $reviews->reviews to $reviews->results
 */
function glsr_get_reviews( array $args = array() ) {
	return glsr( 'Database\ReviewManager' )->get( $args );
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
