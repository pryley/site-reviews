<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

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

// Wordpress 4.0-4.2 support
if( !function_exists( 'wp_roles' )) {
	function wp_roles() {
		global $wp_roles;
		isset( $wp_roles ) ?: $wp_roles = new WP_Roles;
		return $wp_roles;
	}
}

// Wordpress 4.0-4.2 support
if( !function_exists( 'get_avatar_url' )) {
	function get_avatar_url( $id_or_email, $args = null ) {
		isset( $args['size'] ) ?: $args['size'] = 96;
		isset( $args['default'] ) ?: $args['default'] = 'mystery';
		if( $avatar = get_avatar( $id_or_email, $args['size'], $args['default'] )) {
			$dom = new \DOMDocument;
			$dom->loadHTML( $avatar );
			return $dom->getElementsByTagName( 'img' )->item(0)->getAttribute( 'src' );
		}
	}
}

// Wordpress 4.0 support
add_filter( 'script_loader_src', function( $src, $handle ) {
	global $wp_version;
	if( version_compare( $wp_version, '4.1', '<' )
		&& strpos( $handle, '/google-recaptcha' ) !== false
		&& strpos( $src, ' async defer ' ) === false
		&& glsr_get_option( 'reviews-form.recaptcha.integration' ) == 'custom' ) {
		return sprintf( "%s' async defer='defer", $src );
	}
	return $src;
}, 10, 2 );

// PHP 5.4 support
if( !function_exists( 'array_column' )) {
	function array_column( $array, $column_name ) {
		return array_map( function( $element ) use( $column_name ){
			return $element[$column_name];
		}, $array );
	}
}

// Wordpress 4.0-4.6 support
if( !function_exists( 'wp_doing_ajax' )) {
	function wp_doing_ajax() {
		return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
}

// Wordpress 4.0-4.4 support
if( !function_exists( 'wp_add_inline_script' )) {
	function wp_add_inline_script( $handle, $data, $position = 'after' ) {
		if( $handle != glsr_app()->id.'/google-recaptcha' )return;
		echo '<script>'.$data.'</script>';
	}
}

// Wordpress 4.0-4.6 support
if( !function_exists( 'sanitize_textarea_field' )) {
	function sanitize_textarea_field( $str ) {
		$filtered = wp_check_invalid_utf8( $str );
		if( strpos($filtered, '<') !== false ) {
			$filtered = wp_pre_kses_less_than( $filtered );
			$filtered = wp_strip_all_tags( $filtered, false );
			$filtered = str_replace("<\n", "&lt;\n", $filtered);
		}
		$filtered = trim( $filtered );
		$found = false;
		while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
			$filtered = str_replace($match[0], '', $filtered);
			$found = true;
		}
		if( $found ) {
			$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
		}
		return $filtered;
	}
}

// Wordpress 4.0-4.3 support
if( !function_exists( 'get_the_post_thumbnail_url' )) {
	function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
		$post_thumbnail_id = get_post_thumbnail_id( $post );
		if( !$post_thumbnail_id ) {
			return false;
		}
		return wp_get_attachment_image_url( $post_thumbnail_id, $size );
	}
}

// Wordpress 4.0-4.3 support
if( !function_exists( 'wp_get_attachment_image_url' )) {
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
		$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
		return isset( $image['0'] )
			? $image['0']
			: false;
	}
}

// Wordpress 4.0-4.3 support
if( !function_exists( 'wp_json_encode' )) {
	function wp_json_encode( $data ) {
		return json_encode( $data );
	}
}
