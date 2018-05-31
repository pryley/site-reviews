<?php defined( 'WPINC' ) || die; ?>

<div id="titlediv">
	<input type="text" id="title" value="<?= $post->post_title ? esc_attr( $post->post_title ) : sprintf( '(%s)', __( 'no title', 'site-reviews' ) ); ?>" readonly>
</div>

<div id="contentdiv">
	<textarea readonly><?= esc_attr( $post->post_content ); ?></textarea>
</div>
