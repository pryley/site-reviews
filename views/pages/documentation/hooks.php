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
	// modify the submission-form $config array here
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
	// modify the star URLs in the $config array here
	return $config;
});</code></pre>
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
		<h3>Disable the polyfill.io script</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you want to disable the polyfill.io script from loading on your website.</p>
		<p><span class="required">Important:</span> The polyfill.io script provides support for Internet Explorer versions 9-10. If you disable it, Site Reviews will no longer work in those browsers.</p>
		<pre><code class="php">/**
 * Disables the polyfill.io script in Site Reviews.
 * @return bool
 */
add_filter( 'site-reviews/assets/polyfill', '__return_false' );</code></pre>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Do something immediately after a review has been submitted</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you want to do something immediately after a review has been successfully submitted.</p>
		<p>The <code>$review</code> object is the review that was created. The <code>$command</code> object is the request that was submitted to create the review.</p>
		<pre><code>/**
 * Runs after a review has been submitted in Site Reviews.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 */
add_action( 'site-reviews/review/created', function( $review, $command ) {
	// do something here.
}, 10, 2 );</code></pre>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Modify the schema</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you would like to modify the primary schema type properties. For example, suppose you have set "LocalBusiness" as the default schema type. You may want to add additional properties to it and this is the hook to use in order to do that.</p>
		<p>This hook is specific to the schema type. For example, to modify the schema for the LocalBusiness schema type you would use the <em>"site-reviews/schema/LocalBusiness"</em> hook, but to modify the schema for the Product schema type you would use the <em>"site-reviews/schema/Product"</em> hook.</p>
		<p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
		<pre><code class="php">/**
 * Modifies the properties of the schema created by Site Reviews.
 * Change "LocalBusiness" to the schema type you wish to change (i.e. Product)
 * @return array
 */
add_filter( 'site-reviews/schema/LocalBusiness', function( array $schema ) {
	// modify the $schema array here.
	return $schema;
});</code></pre>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Modify the submitted review before it is saved</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Use this hook if you want to modify the submitted review values before the review is created.</p>
		<pre><code>/**
 * Modifies the review values before they are saved
 * @return array
 */
add_filter( 'site-reviews/create/review-values', function( array $reviewValues ) {
	// modify the $reviewValues array here
	return $reviewValues;
});</code></pre>
	</div>
</div>
