<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>F.A.Q.</h3>
	<dl>
		<dt>What is the polyfill.io script and how can I disable it?</dt>
		<dd>
			The polyfill.io script provides polyfills (or "fallbacks") that allow applications written in modern javascript, HTML, and CSS standards to work properly in older web browsers. If you do not need the polyfill script on your website (i.e. you do not wish to support Internet Explorer v9-10), it can easily be disabled. Please see the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=help&tab=documentation&section=hooks' ); ?>">Get Help -> Documentation -> Hooks</a></code> page for instructions.
		</dd>

		<dt>How do I change the placeholder/label text?</dt>
		<dd>All text strings in the plugin can be customized in the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=settings&tab=settings&section=strings' ); ?>">Settings -> Translations</a></code> page.</dd>

		<dt>How do I disable the plugin CSS and/or Javascript?</dt>
		<dd>To disable the plugin stylesheet or javascript from loading on your website, copy and paste the provided <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=help&tab=documentation&section=hooks' ); ?>">WordPress Filter Hooks</a></code> into your theme's <code>functions.php</code> file.</dd>

		<dt>The widgets look funny in my sidebar. What's happening?</dt>
		<dd>Some themes may have very small sidebars and/or CSS styles that conflict or alter the styles within Site Reviews. To correct any styling errors you can either disable the plugin's CSS altogether, or override the CSS selectors in use to make the widget or shortcode appear how you'd like. CSS-related issues are not actively supported as there are too many variations between the thousands of WordPress themes available.</dd>
	</dl>
</div>

<div class="glsr-card card">
	<h3>Basic Troubleshooting</h3>
	<ol>
		<li>
			<p><strong>Make sure you are using the latest version of Site Reviews.</strong></p>
			<p>Site Reviews is updated frequently with bug patches, security updates, code improvements, and new features. It’s always best to be on the latest version of Site Reviews, and if you are not, chances are that if you are experiencing a real bug it has already been fixed.</p>
		</li>
		<li>
			<p><strong>Switch to the default “Twenty Seventeen” WordPress Theme.</strong></p>
			<p>If you have jQuery or Javascript errors on your page, try switching to the default “Twenty Seventeen” WordPress Theme and then see if you are still experiencing problems with the plugin. This is especially true if you have purchased a third party theme or had a theme developed by someone else. Theme authors are notorious for improperly adding their own Javascript and/or jQuery.</p>
		</li>
		<li>
			<p><strong>Deactivate all of your plugins.</strong></p>
			<p>If switching to the default WordPress theme did not fix anything, the next thing to try is to deactivate all your plugins and then activate them one at a time, starting with Site Reviews, and reloading the page between each activation. Once the Javascript on your page stops working properly, you have probably found the problem plugin. If you think that you’ve found the problem plugin, deactivate it and go through the rest of your plugins anyway. Hopefully you don’t find any more, but it’s better to be safe than sorry.</p>
		</li>
	</ol>
</div>

<div class="glsr-card card">
	<h3>Contacting Support</h3>
	<p>If none of the above troubleshooting steps help, then congratulations you <em>may</em> just have found a pesky little bug that must be squashed!</p>
	<p>Send an email to <a href="mailto:site-reviews@geminilabs.io?subject=Support%20request">site-reviews@geminilabs.io</a> and make sure to write a detailed description of the problem you are having and the steps needed to reproduce it.</p>
	<p><span class="required">Important:</span> Please also make sure to attach the <em>System Info</em> report to your email. You can download it from the <a href="<?= admin_url( 'edit.php?post_type=site-review&page=help&tab=system' ); ?>">System Info</a> tab.</p>
</div>
