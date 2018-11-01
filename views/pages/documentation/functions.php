<?php defined( 'WPINC' ) || die; ?>

<p>Make sure to add a <a href="https://php.net/manual/en/function.function-exists.php">function_exists</a> check for each helper function used.</p>

<div id="functions-01" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to create a review</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * Default values in the $reviewValues array:
 * - 'assigned_to' => '',
 * - 'author' => '',
 * - 'avatar' => '',
 * - 'content' => '',
 * - 'date' => '',
 * - 'email' => '',
 * - 'ip_address' => '',
 * - 'pinned' => false,
 * - 'rating' => 0,
 * - 'response' => '',
 * - 'title' => '',
 * - 'url' => '',
 * @return false|\GeminiLabs\SiteReviews\Review
 */
glsr_create_review( array $reviewValues = [] );</code></pre>
		<p><strong>Example Usage:</strong></p>
		<p>Any custom keys that are added to the $reviewValues array will be saved into the <code>$review->custom</code> array of the created review.</p>
		<pre><code class="php">if( function_exists( 'glsr_create_review' )) {
	$review = glsr_create_review([
		'author' => 'Jane Doe',
		'content' => 'This is my review.',
		'date' => '2018-06-13',
		'email' => 'jane@doe.com',
		'rating' => 5,
		'title' => 'Fantastic plugin!',
		'xyz' => 'This is a custom field!'
	]);
}</code></pre>
		<p><strong>Helpful Tip:</strong></p>
		<p>You can use the debug helper to view the review object that is returned.</p>
		<pre><code class="php">glsr_debug( $review );</code></pre>
	</div>
</div>

<div id="functions-02" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to debug variables</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * @param mixed ...$variable
 * @return void
 */
glsr_debug( ...$variable );</code></pre>
		<p>This function prints one or more variables (strings, arrays, objects, etc.) to the screen in human-readable format. You can include as many variables as you want separated by commas.</p>
		<p><strong>Example Usage:</strong></p>
		<pre><code class="php">if( function_exists( 'glsr_debug' )) {
	glsr_debug( $var1, $var2, $var3 );
}</code></pre>
	</div>
</div>

<div id="functions-03" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to get a plugin setting</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * @param string $path
 * @param mixed $fallback
 * @return string|array
 */
glsr_get_option( $path = '', $fallback = '' );</code></pre>
		<p>The <code>$path</code> variable is required and is the dot-notation path of the option you want to get. You build a dot-notation string by using the array keys leading up to the value you wish to get.</p>
		<p>The <code>$fallback</code> variable is what you want to return if the option is not found or is empty. Default is an empty string.</p>
		<p><strong>Example Usage:</strong></p>
		<pre><code class="php">if( function_exists( 'glsr_get_option' )) {
	$requireApproval = glsr_get_option( 'general.require.approval' ); // = <?= glsr_get_option( 'general.require.approval' ); ?>

}</code></pre>
		<p><strong>Helpful Tip:</strong></p>
		<p>You can use the following code to view the whole plugin settings array, this will help you figure out which dot-notation path to use.</p>
		<pre><code class="php">glsr_debug( glsr_get_options() );</code></pre>
	</div>
</div>

<div id="functions-04" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to get a single review</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * @param int $post_id
 * @return void|\GeminiLabs\SiteReviews\Review
 */
glsr_get_review( $post_id );</code></pre>
		<p>The <code>$post_id</code> variable is required and is the post_id of the review you want to get.</p>
		<p><strong>Example Usage:</strong></p>
		<pre><code class="php">if( function_exists( 'glsr_get_review' )) {
	$review = glsr_get_review( 13 );
}</code></pre>
		<p><strong>Helpful Tip:</strong></p>
		<p>You can use the debug helper to view all available values in the review object that is returned.</p>
		<pre><code class="php">glsr_debug( $review );</code></pre>
	</div>
</div>

<div id="functions-05" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to get all plugin settings</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * @return array
 */
glsr_get_options();</code></pre>
		<p>This function returns an array of all of the plugin settings.</p>
		<p><strong>Example Usage:</strong></p>
		<pre><code class="php">if( function_exists( 'glsr_get_options' )) {
	glsr_debug( glsr_get_options() );
}</code></pre>
	</div>
</div>

<div id="functions-06" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to get an array of reviews</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * @return array
 */
glsr_get_reviews( array $args = [] );</code></pre>
		<p>The <code>$args</code> variable is optional, but if included it must be an array.</p>
		<p><strong>Default $args array:</strong></p>
		<pre><code class="php">$args = [
	'assigned_to' => '',
	'category' => '',
	'count' => 10,
	'offset' => '',
	'order' => 'DESC',
	'orderby' => 'date',
	'pagination' => false,
	'post__in' => [],
	'post__not_in' => [],
	'rating' => '',
	'type' => '',
];</code></pre>
		<p><strong>Example Usage:</strong></p>
		<pre><code class="php">if( function_exists( 'glsr_get_reviews' )) {
	$reviews = glsr_get_reviews([
		'rating' => 3,
	]);
	foreach( $reviews as $review ) {
		glsr_debug( $review );
	}
}</code></pre>
	</div>
</div>

<div id="functions-07" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Helper function to log variables to the plugin console</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<pre><code class="php">/**
 * @param null|mixed $var
 * @return \GeminiLabs\SiteReviews\Modules\Console
 */
glsr_log( $var = null );</code></pre>
		<p>This chainable function logs a variable (strings, arrays, objects, etc.) to the plugin console with a default logging level of "debug".</p>
		<p>There are eight available logging levels: <code>alert</code>, <code>critical</code>, <code>debug</code>, <code>emergency</code>, <code>error</code>, <code>info</code>, <code>notice</code>, and <code>warning</code>. Since glsr_log() returns an instance of the Console class, you can chain as many logging levels together as needed.</p>
		<p><strong>Example Usage:</strong></p>
		<pre><code class="php">if( function_exists( 'glsr_log' )) {
	glsr_log( $var1 );
	glsr_log()->warning( $var2 );
	glsr_log( $var3 )->error( $var4 )->info( $var5 );
}</code></pre>
	<p>Logged entries will be found in the  <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=tools#!console' ); ?>">Tools &rarr; Console</a></code>.</p>
	</div>
</div>
