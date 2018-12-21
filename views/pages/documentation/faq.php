<?php defined( 'WPINC' ) || die; ?>

<div id="faq-01" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I add additional values to the schema?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
		<pre><code class="php">/**
 * Modifies the schema created by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @return array
 */
add_filter( 'site-reviews/schema/LocalBusiness', function( array $schema ) {
	$schema['address'] = [
		'@type' => 'PostalAddress',
		'streetAddress' => '123 Main St',
		'addressLocality' => 'City',
		'addressRegion' => 'State',
		'postalCode' => 'Zip',
	];
	return $schema;
});</code></pre>
	</div>
</div>

<div id="faq-02" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the order of the review fields?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
		<p>The <code>review.php</code> template determines how a single review is displayed.</p>
		<p>The first thing you will need to do (if you haven't already) is create a folder in your theme called <code>site-reviews</code>. Once you have done this, <strong>copy</strong> over the <code>review.php</code> file from the "templates" directory in the Site Reviews plugin to this new folder. If you have done this correctly, the path to the template file in your theme should look something like this:</p>
		<p><code>/wp-content/themes/your-theme/site-reviews/review.php</code></p>
		<p>Finally, open the template file you copied over into a text editer, it will look something like this:</p>
		<pre><code class="html">&lt;div class="glsr-review"&gt;
	{{ title }}
	{{ rating }}
	{{ date }}
	{{ assigned_to }}
	{{ content }}
	{{ avatar }}
	{{ author }}
	{{ response }}
&lt;/div&gt;</code></pre>
		<p>Now simply rearrange the review fields into the order you want (you can also remove the fields that you don't want) and then save the template.</p>
	</div>
</div>

<div id="faq-03" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the order of the reviews summary fields?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
		<p>The <code>reviews-summary.php</code> template determines how the reviews summary is displayed.</p>
		<p>The first thing you will need to do (if you haven't already) is create a folder in your theme called <code>site-reviews</code>. Once you have done this, <strong>copy</strong> over the <code>reviews-summary.php</code> file from the "templates" directory in the Site Reviews plugin to this new folder. If you have done this correctly, the path to the template file in your theme should look something like this:</p>
		<p><code>/wp-content/themes/your-theme/site-reviews/reviews-summary.php</code></p>
		<p>Finally, open the template file you copied over into a text editer, it will look something like this:</p>
		<pre><code class="html">&lt;div class="glsr-summary-wrap"&gt;
	&lt;div class="{{ class }}" id="{{ id }}"&gt;
		{{ rating }}
		{{ stars }}
		{{ text }}
		{{ percentages }}
	&lt;/div&gt;
&lt;/div&gt;</code></pre>
		<p>Now simply rearrange the summary fields into the order you want (you can also remove the fields that you don't want) and then save the template.</p>
	</div>
</div>

<div id="faq-04" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the order of the submission form fields?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>To customise the order of the fields in the review submission form, use the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!hooks' ); ?>" data-expand="#hooks-01">site-reviews/submission-form/order</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
		<pre><code class="php">/**
 * Customises the order of the fields used in the Site Reviews submission form.
 * Paste this in your active theme's functions.php file.
 * @return array
 */
add_filter( 'site-reviews/submission-form/order', function( array $order ) {
	// The $order array contains the field keys returned below.
	// Simply change the order of the field keys to the desired field order.
	return [
		'rating',
		'title',
		'content',
		'name',
		'email',
		'terms',
	];
});</code></pre>
		<p>If you have used the example above and the submission-form fields are not working correctly, check the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=tools#!console' ); ?>">Tools &rarr; Console</a></code> for errors.</p>
	</div>
</div>

<div id="faq-05" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the pagination query string?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>The pagination query string can be seen in the address bar of the browser when you go to the next or previous page of reviews (i.e. <code>https://website.com/reviews/?reviews-page=2</code>).</p>
		<pre><code class="php">/**
 * Modifies the pagination query string used by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @return string
 */
add_filter( 'site-reviews/const/PAGED_QUERY_VAR', function() {
	// change this to your preferred query string
	return 'reviews-page';
});</code></pre>
	</div>
</div>

<div id="faq-06" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the text of...?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>You can change any text in the plugin on the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=settings#!translations' ); ?>">Settings &rarr; Translations</a></code> page.</p>
	</div>
</div>

<div id="faq-07" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I create a review programmatically?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Site Reviews provides a <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!functions' ); ?>" data-expand="#functions-01">glsr_create_review()</a></code> helper function to easily create a review.</p>
		<p>Here is an example:</p>
		<pre><code class="php">if( function_exists( 'glsr_create_review' )) {
	$review = glsr_create_review([
		'author' => 'Jane Doe',
		'content' => 'This is my review.',
		'date' => '2018-06-13',
		'email' => 'jane@doe.com',
		'rating' => 5,
		'title' => 'Fantastic plugin!',
	]);
}
</code></pre>
	</div>
</div>

<div id="faq-08" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I customise the stars?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>To customise the star images used by the plugin, use the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!hooks' ); ?>" data-expand="#hooks-02">site-reviews/config/inline-styles</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
		<p>Here is an example:</p>
		<pre><code class="php">/**
 * Customises the stars used by Site Reviews.
 * Simply change and edit the URLs to match those of your custom images.
 * Paste this in your active theme's functions.php file.
 * @return array
 */
add_filter( 'site-reviews/config/inline-styles', function( array $config ) {
	$config[':star-empty'] = get_stylesheet_directory_uri().'/assets/images/star-empty.svg';
	$config[':star-error'] = get_stylesheet_directory_uri().'/assets/images/star-error.svg';
	$config[':star-full'] = get_stylesheet_directory_uri().'/assets/images/star-full.svg';
	$config[':star-half'] = get_stylesheet_directory_uri().'/assets/images/star-half.svg';
	return $config;
});</code></pre>
	</div>
</div>

<div id="faq-09" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I limit the submitted review length?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>To limit the allowed length of submitted reviews, use the following filter hooks in your theme's <code>functions.php</code> file:</p>
		<pre><code class="php">/**
 * Set the "maxlength" HTML attribute to limit review length to 100 characters
 * Simply change the number to your desired length.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter( 'site-reviews/config/forms/submission-form', function( $config ) {
	if( array_key_exists( 'content', $config )) {
		$config['content']['maxlength'] = 100;
	}
	return $config;
});

/**
 * Limit review length to 100 characters in the form validation
 * Simply change the number to your desired length.
 * Paste this in your active theme's functions.php file.
 * @param array $rules
 * @return array
 */
add_filter( 'site-reviews/validation/rules', function( $rules ) {
	$rules['content'] = 'required|max:100';
	return $rules;
});</code></pre>
	</div>
</div>

<div id="faq-10" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I redirect to a custom URL after a form is submitted?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>To redirect the page after a form has been submitted, edit the page the shortcode is on and use the <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a> metabox to add a <code>redirect_to</code> as the Custom Field name and the URL you want to redirect to as the value.</p>
	</div>
</div>

<div id="faq-11" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I remove the dash in front of the author's name?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>A "dash" character appears in front of an author's name if you have chosen to disable avatars in the settings (or possibly also if you changed the order of the review fields). If you want to remove the dash, simply use the following custom CSS. If your theme does not allow you to add custom CSS, you can use a plugin such as <a href="https://wordpress.org/plugins/simple-custom-css/">Simple Custom CSS</a>.</p>
		<pre><code class="css">.glsr-review-author::before {
	display: none !important;
}</code></pre>
	</div>
</div>
