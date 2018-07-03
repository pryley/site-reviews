<?php defined( 'WPINC' ) || die; ?>

<textarea id="log-file" class="large-text code glsr-code" rows="20" readonly>{{ console }}</textarea>
<form method="post" class="float-left">
	<input type="hidden" name="{{ id }}[action]" value="clear-console">
	<?php wp_nonce_field( 'clear-console' ); ?>
	<?php submit_button( __( 'Clear Console', 'site-reviews' ), 'secondary', 'clear-console', false ); ?>
</form>
<form method="post">
	<input type="hidden" name="{{ id }}[action]" value="download-log">
	<?php wp_nonce_field( 'download-log' ); ?>
	<?php submit_button( __( 'Download Log', 'site-reviews' ), 'secondary', '', false ); ?>
</form>
