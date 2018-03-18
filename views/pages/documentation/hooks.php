<?php defined( 'WPINC' ) || die; ?>

<p>Hooks (also known as Filters &amp; Actions) are used to make changes to the plugin without modifying the core files of the plugin directly. In order to use the following hooks, you must add them to your theme's <code>functions.php</code> file.</p>

<div class="glsr-card card">
	<h3>Disable the plugin CSS</h3>
	<pre><code>add_filter( 'site-reviews/assets/css', '__return_false' );</code></pre>
	<p>Use this hook if you want to disable the plugin stylesheet from loading on your website.</p>
</div>

<div class="glsr-card card">
	<h3>Disable the plugin javascript</h3>
	<pre><code>add_filter( 'site-reviews/assets/js', '__return_false' );</code></pre>
	<p>Use this hook if you want to disable the plugin javascript from loading on your website.</p>
</div>

<div class="glsr-card card">
	<h3>Do something immediately after a review has been submitted.</h3>
	<pre><code>add_action( 'site-reviews/local/review/submitted', function( $message, $request ) {
	// do something here.
}, 10, 2 );</code></pre>
	<p>Use this hook if you want to do something immediately after a review has been successfully submitted.</p>
	<p>The <code>$message</code> is the "successfully submitted" message returned to the user.</p>
	<p>The <code>$request</code> is the PHP object used to create the review. With this you can also determine the current referrer URI (<code>$request->referrer</code>) or whether the request is an AJAX request or not (<code>$request->ajaxRequest</code>).</p>
</div>

<div class="glsr-card card">
	<h3>Change the default <a href="https://developers.google.com/recaptcha/docs/language" target="_blank">reCAPTCHA language</a>.</h3>
	<pre><code>add_filter( 'site-reviews/recaptcha/language', function( $locale ) {
	// return a language code here (e.g. "en")
	return $locale;
});</code></pre>
	<p>This hook will only work when using "Custom Integration" reCAPTCHA setting.
</div>

<div class="glsr-card card">
	<h3>Modify the JSON-LD schema type properties</h3>
	<pre><code>$schemaType = 'LocalBusiness';
add_filter( "site-reviews/schema/{$schemaType}", function( array $schema, array $args ) {
	// do something here.
	return $schema;
}, 10, 2 );</code></pre>
	<p>Use this hook if you would like to modify the primary schema type properties. For example, suppose you have set "LocalBusiness" as the default schema type. You may want to add other properties to it such as "address", "priceRange", and "telephone"; this is the hook to use in order to do that.</p>
	<p>The <code>$schema</code> variable is the existing schema type array. The <code>$args</code> variable is the array of arguments used to query the reviews in order to calculate the average rating and review count. You can pass this variable to the <code>glsr_get_reviews()</code> function to return the exact same array of reviews.</p>
	<p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
</div>

<div class="glsr-card card">
	<h3>Modify the final generated JSON-LD schemas</h3>
	<pre><code>add_filter( 'site-reviews/schema/all', function( array $schemas ) {
	// do something here.
	return $schemas;
});</code></pre>
	<p>Use this hook if you would like to modify the generated JSON-LD schema. This hook is fired immediately before the schema is printed on the page.</p>
	<p>The <code>$schemas</code> variable is an array of all schema arrays that have been generated for the page. Ideally there should only be one schema in the array, however, if more than one shortcode has been included on the same page with the schema attribute enabled, then there may be more than one.</p>
	<p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
</div>
