<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the order of the form fields?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>To customise the order of the fields in the review submission form, use the <code>site-reviews/config/forms/submission-form</code> filter hook in your theme's <code>functions.php</code> file.</p>
		<p>You can either modify the $config array directly (as shown in the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!hooks' ); ?>">Documentation &rarr; Hooks</a></code>), or you can copy over the <code>config/forms/submission-form.php</code> file from the plugin into your theme and edit it.</p>
		<p>In the example below, I am using a custom <code>submission-form.php</code> file. If you have done this correctly, the path to the config file in your theme should look something like this:</p>
		<p><code>/wp-content/themes/your-theme/site-reviews/config/submission-form.php</code></p>
		<pre><code class="php">/**
 * Customises the fields used in the Site Reviews submission form.
 * Simply copy the "config/forms/submission-form.php" file from the plugin into your theme and customise it.
 * Make sure that the $customForm path matches the location of your custom "submission-form.php" file.
 * @return array
 */
add_filter( 'site-reviews/config/forms/submission-form', function( array $config ) {
	$customForm = get_stylesheet_directory().'/site-reviews/config/submission-form.php';
	if( !file_exists( $customForm )) {
		glsr_log()->error( 'Your custom submission-form.php was not found: '.$customForm );
		return $config;
	}
	return include $customForm;
});</code></pre>
		<p>If you have used the example above and the custom submission-form fields are not being used, check the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=tools#!console' ); ?>">Tools &rarr; Console</a></code> for errors.</p>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the order of the review fields?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
		<p>The first thing you will need to do (if you haven't already) is create a folder in your theme called <code>site-reviews</code>. Once you have done this, <strong>copy</strong> over the <code>review.php</code> file from the "templates" directory in the Site Reviews plugin to this new folder. The <code>review.php</code> template determines how a single review is displayed.</p>
		<p>If you have done this correctly, the path to the template file in your theme should look something like this:</p>
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
		<p>Now simply rearrange the review fields into the order you want (you can also remove the fields that you don't want) and save the template.</p>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I change the order of the reviews summary fields?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
		<p>The first thing you will need to do (if you haven't already) is create a folder in your theme called <code>site-reviews</code>. Once you have done this, <strong>copy</strong> over the <code>reviews-summary.php</code> file from the "templates" directory in the Site Reviews plugin to this new folder. The <code>reviews-summary.php</code> template determines how the reviews summary is displayed.</p>
		<p>If you have done this correctly, the path to the template file in your theme should look something like this:</p>
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
		<p>Now simply rearrange the summary fields into the order you want (you can also remove the fields that you don't want) and save the template.</p>
	</div>
</div>

<div class="glsr-card postbox">
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

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I customise the stars?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>To customise the star images used by the plugin, use the <code>site-reviews/config/inline-styles</code> filter hook in your theme's <code>functions.php</code> file.</p>
		<p>Here is an example:</p>
		<pre><code class="php">/**
 * Customises the stars used by Site Reviews.
 * Simply change and edit the URLs to match those of your custom images.
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

<div class="glsr-card postbox">
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

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>How do I remove the dash in front of the author's name?</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>The dash shows in front of an author's name if you have disabled avatars. If you want to remove the dash, simply use the following custom CSS. If your theme does not allow you to add custom CSS, you can use a plugin such as <a href="https://wordpress.org/plugins/simple-custom-css/">Simple Custom CSS</a>.</p>
		<pre><code class="css">.glsr-review-author::before {
	display: none !important;
}</code></pre>
	</div>
</div>
