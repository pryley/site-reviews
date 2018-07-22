<?php defined( 'WPINC' ) || die; ?>

<textarea id="log-file" class="large-text code glsr-code" rows="20" readonly>{{ console }}</textarea>
<form method="post" class="float-left">
	<input type="hidden" name="{{ id }}[action]" value="download-console">
	<?php wp_nonce_field( 'download-console' ); ?>
	<?php submit_button( __( 'Download Console', 'site-reviews' ), 'secondary', '', false ); ?>
</form>
<form method="post">
	<input type="hidden" name="{{ id }}[action]" value="clear-console">
	<?php wp_nonce_field( 'clear-console' ); ?>
	<button type="submit" class="glsr-button button" name="clear-console" id="clear-console">
		<span data-loading="<?= __( 'Clearing Console...', 'site-reviews' ); ?>"><?= __( 'Clear Console', 'site-reviews' ); ?></span>
	</button>
</form>
