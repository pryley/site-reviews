<?php defined( 'WPINC' ) || die; ?>

<br>
<form method="post">
	<textarea class="large-text code glsr-code" name="{{ id }}[system-info]" rows="20" onclick="this.select()" readonly>{{ system }}</textarea>
	<input type="hidden" name="{{ id }}[action]" value="download-system-info">
	<?php wp_nonce_field( 'download-system-info' ); ?>
	<?php submit_button( __( 'Download System Info', 'site-reviews' ), 'secondary', '', false ); ?>
</form>
