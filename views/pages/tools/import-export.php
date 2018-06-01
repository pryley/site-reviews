<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>Import Settings</h3>
	<p>Import the Site Reviews settings from a <code>*.json</code> file. This file can be obtained by exporting the settings on another site using the export tool below.</p>
	<p>To import your Site Reviews reviews and categories from another website, please use the WordPress <a href="<?= admin_url( 'import.php' ); ?>">Import</a> tool.</p>
	<form method="post" enctype="multipart/form-data">
		<input type="file" name="import-file">
		<input type="hidden" name="{{ id }}[action]" value="import-settings">
		<?php wp_nonce_field( 'import-settings' ); ?>
		<?php submit_button( __( 'Import Settings', 'site-reviews' ), 'secondary' ); ?>
	</form>
</div>

<div class="glsr-card card">
	<h3>Export Settings</h3>
	<p>Export the Site Reviews settings for this site to a <code>*.json</code> file. This allows you to easily import the plugin settings into another site.</p>
	<p>To export your Site Reviews reviews and categories, please use the WordPress <a href="<?= admin_url( 'export.php' ); ?>">Export</a> tool.</p>
	<form method="post">
		<input type="hidden" name="{{ id }}[action]" value="export-settings">
		<?php wp_nonce_field( 'export-settings' ); ?>
		<?php submit_button( __( 'Export Settings', 'site-reviews' ), 'secondary' ); ?>
	</form>
</div>
