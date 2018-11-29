<?php defined( 'WPINC' ) || die; ?>

<div id="titlediv">
	<input type="text" id="title" value="<?= $post->post_title ? esc_attr( $post->post_title ) : sprintf( '(%s)', __( 'no title', 'site-reviews' )); ?>" readonly>
</div>

<div id="contentdiv">
	<textarea readonly><?= esc_attr( $post->post_content ); ?></textarea>
</div>

<?php if( empty( $response ))return; ?>

<div class="postbox glsr-response-postbox">
	<button type="button" class="handlediv" aria-expanded="true">
		<span class="screen-reader-text"><?= __( 'Toggle panel: Public Response', 'site-reviews' ); ?></span>
		<span class="toggle-indicator" aria-hidden="true"></span>
	</button>
	<h2 class="hndle">
		<span><?= __( 'Public Response', 'site-reviews' ); ?></span>
	</h2>
	<div class="inside">
		<?= wpautop( esc_attr( $response )); ?>
	</div>
</div>
