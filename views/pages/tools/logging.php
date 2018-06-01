<?php defined( 'WPINC' ) || die; ?>

<br>
<textarea id="log-file" class="large-text code glsr-code" rows="20" readonly>{{ logger }}</textarea>
<form method="post" class="float-left">
	<input type="hidden" name="{{ id }}[action]" value="clear-log">
	<?php wp_nonce_field( 'clear-log' ); ?>
	<?php submit_button( __( 'Clear Log', 'site-reviews' ), 'secondary', 'clear-log', false ); ?>
</form>
<form method="post">
	<input type="hidden" name="{{ id }}[action]" value="download-log">
	<?php wp_nonce_field( 'download-log' ); ?>
	<?php submit_button( __( 'Download Log', 'site-reviews' ), 'secondary', '', false ); ?>
</form>
