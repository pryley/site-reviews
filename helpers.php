<?php
defined( 'WPINC' ) || die;

/**
 * @return mixed
 */
function glsr( $alias = null ) {
	$app = \GeminiLabs\SiteReviews\Application::load();
	return empty( $alias )
		? $app
		: $app->make( $alias );
}

/**
 * get_current_screen() is unreliable because it is not defined on all admin pages
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
 * @return \GeminiLabs\SiteReviews\Modules\Logger
 */
function glsr_log() {
	$args = func_get_args();
	$context = isset( $args[1] )
		? $args[1]
		: [];
	$logger = glsr( 'Modules\Logger' );
	return empty( $args )
		? $logger
		: $logger->log( 'debug', $args[0], $context );
}

/**
 * This function prevents the taxonomy object from containing class recursion
 * @return void
 * @callback register_taxonomy() "meta_box_cb"
 */
function glsr_categories_meta_box( $post, $box ) {
	glsr( 'Controllers\EditorController' )->renderTaxonomyMetabox( $post, $box );
}



/**
 * @param string $option_path
 * @param mixed $fallback
 * @return string|array
 */
// function glsr_get_option( $option_path = '', $fallback = '' ) {
// 	return glsr( 'Database\OptionManager' )->get( $option_path, $fallback );
// }

/**
 * @return array
 */
// function glsr_get_options() {
// 	return glsr( 'Database\OptionManager' )->all();
// }

/**
 * @param int $post_id
 * @return void|object
 */
// function glsr_get_review( $post_id ) {
// 	return glsr( 'Database' )->getReview( get_post( $post_id ));
// }

/**
 * @return array
 */
// function glsr_get_reviews( array $args = array() ) {
// 	return glsr( 'Database' )->getReviews( $args );
// }
