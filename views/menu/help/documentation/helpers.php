<?php defined( 'WPINC' ) || die; ?>

<p>The following helper functions will only work if the <?= glsr_app()->name; ?> plugin is active. If you use any of them in a theme, make sure you add <a href="https://php.net/manual/en/function.function-exists.php">function_exists</a> checks to your theme's <code>functions.php</code> file for each helper function you use.</p>

<div class="glsr-card card">
	<h3>Global helper to get a single review</h3>
	<pre><code>glsr_get_review( $post_id );</code></pre>
	<p>The <code>$post_id</code> variable is required and is the post_id of the review you want to get.</p>
	<p><strong>Helpful Tip:</strong></p>
	<p>You can use the following code to view all available values in the review object that is returned.</p>
	<pre><code>glsr_debug( glsr_get_review( $post_id ));</code></pre>
</div>

<div class="glsr-card card">
	<h3>Global helper to get an array of reviews</h3>
	<pre><code>glsr_get_reviews( $args );</code></pre>
	<p>The <code>$args</code> variable is optional, but if included it must be an array.</p>
	<p><strong>Default Usage:</strong></p>
	<pre><code>glsr_get_reviews([
	'assigned_to' => '',
	'category' => '',
	'count' => 10,
	'order' => 'DESC',
	'orderby' => 'date',
	'pagination' => false,
	'post__in' => [],
	'post__not_in' => [],
	'rating' => 1,
	'type' => '',
]);</code></pre>
	<p><strong>Example:</strong></p>
	<pre><code>$reviews = glsr_get_reviews([
	"count"  => -1,
	"rating" => 1,
]);

foreach( $reviews as $review ) {
	glsr_debug( $review );
}</code></pre>
</div>

<div class="glsr-card card">
	<h3>Global helper to get a plugin option</h3>
	<pre><code>glsr_get_option( $option_path, $fallback );</code></pre>
	<p>The <code>$option_path</code> variable is required and is the dot-notation path of the option you want to get. You build a dot-notation string by using the array keys leading up to the value you wish to get.</p>
	<p>The <code>$fallback</code> variable is what you want to return if the option is not found or is empty. Default is an empty string.</p>
	<p><strong>Example:</strong></p>
	<p><code>"general.require.login"</code> will get the value of the "Require Login" setting which is stored in the settings array as shown below:</p>
	<pre><code>[
	"general" => [
		"require" => [
			"approval" => "yes",
			"login" => "no",
		],
	],
]</code></pre>
	<p><strong>Helpful Tip:</strong></p>
	<p>You can use the following code to view the whole plugin settings array, this will help you figure out which dot-notation path to use.</p>
	<pre><code>glsr_debug( glsr_get_options() );</code></pre>
</div>

<div class="glsr-card card">
	<h3>Global helper to get an array of all plugin options</h3>
	<pre><code>glsr_get_options();</code></pre>
</div>

<div class="glsr-card card">
	<h3>Global helper to debug variables</h3>
	<pre><code>glsr_debug( $variable, ... );</code></pre>
	<p>This function prints one or more variables (strings, arrays, objects, etc.) to the screen in human-readable format. You can include as many variables as you want separated by commas.</p>
	<p><strong>Example:</strong></p>
	<pre><code>glsr_debug( $var1, $var2, $var3 );</code></pre>
</div>

<div class="glsr-card card">
	<h3>Global helper to log variables</h3>
	<pre><code>glsr_log( $message, $level );</code></pre>
	<p>This function logs a variable if logging is enabled.</p>
	<p><code>$level</code> is optional and defaults to "debug". Available logging levels are: "emergency", "alert", "critical", "error", "warning", "notice", "info", and "debug".</p>
	<p><strong>Example:</strong></p>
	<pre><code>glsr_log( $log_this_variable );</code></pre>
</div>
