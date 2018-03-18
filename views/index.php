<?php defined( 'WPINC' ) || die;

$file = sprintf( '%s/site-reviews/%s.php', get_stylesheet_directory(), $view );
if( !file_exists( $file )) {
	$file = trailingslashit( __DIR__ )."{$view}.php";
}
$file = apply_filters( 'site-reviews/addon/views/file', $file, $view, $data );

if( file_exists( $file )) {
	include $file;
}
else {
	glsr_log()->error( 'File not found: '.$file );
}
