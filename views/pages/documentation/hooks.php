<?php defined( 'WPINC' ) || die; ?>

<p>Hooks (also known as Filters &amp; Actions) are used to make changes to the plugin without modifying the core files of the plugin directly. In order to use the following hooks, you must add them to your theme's <code>functions.php</code> file.</p>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Customise the fields in the review submission form</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook to customise the fields in the review submission form used by Site Reviews.</p>
		<p>See the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!faq' ); ?>">Documentation &rarr; FAQ</a></code> for a detailed example of how to use this hook.</p>
		<pre><code class="php">/**
 * Customises the fields used in the Site Reviews submission form.
 * @return array
 */
add_filter( 'site-reviews/config/forms/submission-form', function( array $config ) {
	// change the submission-form $config array here
	return $config;
});</code></pre>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Customise the star images</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook to customise the star images used by Site Reviews.</p>
		<p>See the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!faq' ); ?>">Documentation &rarr; FAQ</a></code> for a detailed example of how to use this hook.</p>
		<pre><code class="php">/**
 * Customises the stars used by Site Reviews.
 * @return array
 */
add_filter( 'site-reviews/config/inline-styles', function( array $config ) {
	// change the star URLs in the $config array here
	return $config;
});</code></pre>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Disable the plugin stylesheet</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you want to disable the plugin stylesheet from loading on your website.</p>
		<pre><code class="php">/**
 * Disables the Site Reviews stylesheet.
 * @return bool
 */
add_filter( 'site-reviews/assets/css', '__return_false' );</code></pre>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Disable the plugin javascript</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you want to disable the plugin javascript from loading on your website.</p>
		<pre><code class="php">/**
 * Disables the Site Reviews javascript.
 * @return bool
 */
add_filter( 'site-reviews/assets/js', '__return_false' );</code></pre>
	</div>
</div>


<!--
<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Do something immediately after a review has been submitted.</h3>
	</div>
	<div class="glsr-card-body">
		<pre><code>add_action( 'site-reviews/local/review/submitted', function( $message, $request ) {
	// do something here.
}, 10, 2 );</code></pre>
		<p>Use this hook if you want to do something immediately after a review has been successfully submitted.</p>
		<p>The <code>$request</code> is the PHP object used to create the review. With this you can also determine the current referrer URI (<code>$request->referrer</code>) or whether the request is an AJAX request or not (<code>$request->ajaxRequest</code>).</p>
	</div>
</div>
-->
<!--
<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Change the default <a href="https://developers.google.com/recaptcha/docs/language" target="_blank">reCAPTCHA language</a>.</h3>
	</div>
	<div class="glsr-card-body">
		<pre><code>add_filter( 'site-reviews/recaptcha/language', function( $locale ) {
	// return a language code here (e.g. "en")
	return $locale;
});</code></pre>
		<p>This hook will only work when using "Custom Integration" reCAPTCHA setting.
	</div>
</div>
-->



<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Modify the JSON-LD schema</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you would like to modify the primary schema type properties. For example, suppose you have set "LocalBusiness" as the default schema type. You may want to add additional properties to it and this is the hook to use in order to do that.</p>
		<p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
		<pre><code class="php">/**
 * Modifies the properties of the schema created by Site Reviews.
 * Change "LocalBusiness" to the schema type you wish to change (i.e. Product)
 * @return array
 */
add_filter( 'site-reviews/schema/LocalBusiness', function( array $schema ) {
	// change the $schema array here.
	return $schema;
});</code></pre>
	</div>
</div>
