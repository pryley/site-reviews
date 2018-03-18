<?php defined( 'WPINC' ) || die; ?>

<br>

<textarea id="log-file" class="large-text code glsr-code" rows="20" readonly><?= $logger; ?></textarea>

<form method="post" class="float-left">
	<?php wp_nonce_field( 'clear-log' ); ?>
	<input type="hidden" name="<?= $id; ?>[action]" value="clear-log">
	<?php submit_button( __( 'Clear Log', 'site-reviews' ), 'secondary', 'clear-log', false ); ?>
</form>

<form method="post">
	<?php wp_nonce_field( 'download-log' ); ?>
	<input type="hidden" name="<?= $id; ?>[action]" value="download-log">
	<?php submit_button( __( 'Download Log', 'site-reviews' ), 'secondary', '', false ); ?>
</form>
