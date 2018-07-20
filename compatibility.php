<?php

defined( 'WPINC' ) || die;

/**
 * @param array $scriptHandles
 * @return array
 * @see https://wordpress.org/plugins/speed-booster-pack/
 */
add_filter( 'sbp_exclude_defer_scripts', function( $scriptHandles ) {
	$scriptHandles[] = 'site-reviews/google-recaptcha';
	return array_keys( array_flip( $scriptHandles ));
});

/**
 * @param \GeminiLabs\SiteReviews\Modules\Html\Builder $instance
 * @return void
 */
add_action( 'site-reviews/customize/divi', function( $instance ) {
	if( $instance->tag != 'label' || $instance->args['type'] != 'checkbox' )return;
	$instance->args['text'] = '<i></i>'.$instance->args['text'];
});
