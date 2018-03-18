<?php defined( 'WPINC' ) || die; ?>

<div id="glsr-outdated-notice" class="notice notice-warning is-dismissible">
	<div style="float:left; padding:12px 12px 12px 0;">
		<img src="<?= glsr_app()->url; ?>assets/img/icon.png" width="80" height="80">
	</div>
	<div style=" margin-left: 92px;">
		<p><strong>Hello, this is a friendly notice to let you know that Site Reviews v3.0 will soon be released.</strong></p>
		<p>In order to continue development of the plugin, the minimum required version of PHP has been increased to 5.6. Since your server is using PHP <?= PHP_VERSION; ?> the plugin will deactivate after updating and you will need to manually roll-back the plugin to the old v2. We recommend that you contact your hosting provider as soon as possible and ask them to upgrade PHP to 5.6 or higher.</p>
		<p>Site Reviews v3.0 has been re-written from the ground up and includes all of the features from v2.0, as as well as some new ones! It will be well worth the effort to upgrade your version of PHP.</p>
		<p><strong>Thank you for using Site Reviews!</strong></p>
	</div>
</div>
