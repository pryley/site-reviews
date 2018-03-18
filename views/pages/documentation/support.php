<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>Contacting Support</h3>
	<ol>
		<li>
			<p><strong>Before you ask for help</strong>, please confirm that you have read both the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation&tab=faq' ); ?>">FAQ</a></code> and <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation&tab=shortcodes' ); ?>">Shortcodes</a></code> pages as many questions have already been answered there.</p>
		</li>
		<li>
			<p><strong>If you are experiencing problems</strong> or something is not working as expected, please confirm that you have tried the <em>Basic Troubleshooting Steps</em> provided below.</p>
		</li>
		<li>
			<p><strong>If you can confirm that you have completed the steps above</strong> and are still in a pickle, send an email to <a href="mailto:site-reviews@geminilabs.io?subject=Support%20request">site-reviews@geminilabs.io</a> and make sure to write a detailed description of the problem you are having as well as any steps needed to reproduce it.</p>
			<p><span class="required">Important:</span> If you are writing about a problem that you are having, please make sure to <em>download</em> and attach the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=tools&tab=system-info' ); ?>">Tools &rarr; System Info</a></code> report to your email.</p>
		</li>
	</ol>
</div>

<div class="glsr-card card">
	<h3>Basic Troubleshooting Steps</h3>
	<ol>
		<li>
			<p><strong>Make sure you are using the latest version of Site Reviews.</strong></p>
			<p>Site Reviews is updated frequently with bug patches, security updates, improvements, and new features. If you are not using the latest version and are experiencing problems, the chances are good that your problem has already been fixed in the latest version.</p>
		</li>
		<li>
			<p><strong>Temporarily switch to an official WordPress Theme.</strong></p>
			<p>Try switching to an official WordPress Theme (i.e. Twenty Seventeen) and then see if you are still experiencing problems with the plugin. If this fixes the problem you were having then there is a compatibility issue with your theme.</p>
		</li>
		<li>
			<p><strong>Temporarily deactivate all of your plugins.</strong></p>
			<p>If switching to an official WordPress theme did not fix anything, the last thing to do is deactivate all of your plugins except for Site Reviews. If this fixes the problem you were having then there is a compatibility issue with one of your plugins.</p>
			<p>The next thing to do is reactivate your plugins one-by-one until you find the plugin that is causing the problem. If you think that you’ve found the culprit, deactivate it and continue to test the rest of your plugins. Hopefully you won’t find any more, but it’s always better to make sure.</p>
		</li>
	</ol>
</div>
