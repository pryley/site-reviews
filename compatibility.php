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
