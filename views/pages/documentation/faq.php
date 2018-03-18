<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>How do I change the text of...?</h3>
	<p>You can change any text in the plugin on the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=settings&tab=translations' ); ?>">Settings &rarr; Translations</a></code> page.</p>
</div>

<div class="glsr-card card">
	<h3>How do I disable the plugin CSS and/or Javascript?</h3>
	<p>To disable the plugin's stylesheet or javascript from loading on your website, copy and paste the provided <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation&tab=hooks' ); ?>">Documentation &rarr; Hooks</a></code> into your theme's <code>functions.php</code> file.</p>
</div>

<div class="glsr-card card">
	<h3>The widgets and shortcodes look funny in my sidebar. What's happening?</h3>
	<p>Some themes may have very small sidebars and/or CSS styles that conflict or alter the styles within Site Reviews. To correct any styling errors you can either disable the plugin's CSS altogether, or override the CSS selectors in use to make the widget or shortcode appear how you'd like. CSS-related issues are not actively supported as there are too many variations between the thousands of WordPress themes available.</p>
</div>
