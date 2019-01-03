<?php
defined( 'WPINC' ) || die;

/**
 * Alternate method of using the functions without having to use `function_exists()`
 * Example: apply_filters( 'glsr_get_reviews', [], ['assigned_to' => 'post_id'] );
 * @param mixed ...
 * @return mixed
 */
add_filter( 'all', function() {
	$args = func_get_args();
	$hook = array_shift( $args );
	$hooks = array(
		'glsr',
		'glsr_calculate_ratings',
		'glsr_create_review',
		'glsr_debug',
		'glsr_get_option', 'glsr_get_options',
		'glsr_get_review', 'glsr_get_reviews',
		'glsr_log',
	);
	if( !in_array( $hook, $hooks ) || !function_exists( $hook ))return;
	add_filter( $hook, function() use( $hook, $args ) {
		array_shift( $args ); // remove the fallback value
		return call_user_func_array( $hook, $args );
	});
});

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
 * array_column() alternative specifically for PHP v7.0.x
 * @param $column string
 * @return array
 */
function glsr_array_column( array $array, $column ) {
	$result = array();
	foreach( $array as $subarray ) {
		$subarray = (array)$subarray;
		if( !isset( $subarray[$column] ))continue;
		$result[] = $subarray[$column];
	}
	return $result;
}

/**
 * @return void
 */
function glsr_calculate_ratings() {
	glsr( 'Controllers\AdminController' )->routerCountReviews( false );
	glsr_log()->info( __( 'Recalculated rating counts.', 'site-reviews' ));
}

/**
 * @return null|\GeminiLabs\SiteReviews\Review
 */
function glsr_create_review( $reviewValues = array() ) {
	if( !is_array( $reviewValues )) {
		$reviewValues = array();
	}
	$review = new \GeminiLabs\SiteReviews\Commands\CreateReview( $reviewValues );
	$result = glsr( 'Database\ReviewManager' )->create( $review );
	return !empty( $result )
		? $result
		: null;
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
	return is_string( $path )
		? glsr( 'Database\OptionManager' )->get( 'settings.'.$path, $fallback )
		: $fallback;
}

/**
 * @return array
 */
function glsr_get_options() {
	return glsr( 'Database\OptionManager' )->get( 'settings' );
}

/**
 * @param int $post_id
 * @return \GeminiLabs\SiteReviews\Review|void
 */
function glsr_get_review( $post_id ) {
	if( !is_numeric( $post_id ))return;
	$post = get_post( $post_id );
	if( $post instanceof WP_Post ) {
		return glsr( 'Database\ReviewManager' )->single( $post );
	}
}

/**
 * @return array
 * @todo document change of $reviews->reviews to $reviews->results
 */
function glsr_get_reviews( $args = array() ) {
	if( !is_array( $args )) {
		$args = [];
	}
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
