<?php defined('WPINC') || die; ?>

<div id="functions-01" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Read me first!</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>The problem with using plugin-specific helper functions is that they only exist when the plugin is active. When the plugin is disabled, any helper functions that have been used will throw a PHP error unless you have also included a <a href="https://php.net/manual/en/function.function-exists.php">function_exists</a> check.</p>
        <p>Site Reviews provides an alternative way of using these functions which is much safer:</p>
        <pre><code>/**
 * @param string $function_name (required) This is the name of the function you want to use
 * @param mixed $fallback (required) This value is returned when the function does not exist
 * @param mixed ...$args (optional) These are the arguments (one or more) required by the function
 * @return mixed
 */
apply_filters($function_name, $fallback, ...$args);</code></pre>
        <p>All functions listed here can be used in this way!</p>
        <p>The benefit of using this method is that you don't have to include a <a href="https://php.net/manual/en/function.function-exists.php">function_exists</a> check, and you can also provide a fallback value that is returned if the plugin is not available or active.</p>
        <p>For example:</p>
        <pre><code>$reviews = apply_filters('glsr_get_reviews', [], [
    'assigned_to' => 'post_id',
]);</code></pre>
        <p>Is the same as:</p>
        <pre><code>$reviews = [];
if (function_exists('glsr_get_reviews')) {
    $reviews = glsr_get_reviews([
        'assigned_to' => 'post_id',
    ]);
}</code></pre>
    </div>
</div>

<div id="functions-02" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to create a review</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <pre><code class="php">/**
 * Default values in the $reviewValues array:
 * - 'assign_to' => '',
 * - 'avatar' => '',
 * - 'content' => '',
 * - 'date' => '',
 * - 'email' => '',
 * - 'ip_address' => '',
 * - 'name' => '',
 * - 'pinned' => false,
 * - 'rating' => 0,
 * - 'response' => '',
 * - 'title' => '',
 * - 'url' => '',
 * @return \GeminiLabs\SiteReviews\Review|false
 */
glsr_create_review(array $reviewValues = []);</code></pre>
        <p><strong>Example Usage:</strong></p>
        <p>Any custom keys that are added to the $reviewValues array will be saved into the <code>$review->custom</code> array of the created review.</p>
        <pre><code class="php">$review = glsr_create_review([
    'assign_to' => $post->ID,
    'content' => 'This is my review.',
    'date' => '2018-06-13',
    'email' => 'jane@doe.com',
    'name' => 'Jane Doe',
    'rating' => 5,
    'title' => 'Fantastic plugin!',
    'xyz' => 'This is a custom field!'
]);

// OR:

$review = apply_filters('glsr_create_review', false, [
    'assign_to' => $post->ID,
    'content' => 'This is my review.',
    'date' => '2018-06-13',
    'email' => 'jane@doe.com',
    'name' => 'Jane Doe',
    'rating' => 5,
    'title' => 'Fantastic plugin!',
    'xyz' => 'This is a custom field!'
]);
</code></pre>
        <p><strong>Helpful Tip:</strong></p>
        <p>You can use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-03">glsr_debug</a></code> helper function to view the review object that is returned.</p>
        <pre><code class="php">glsr_debug($review);</code></pre>
    </div>
</div>

<div id="functions-03" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to debug variables</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <pre><code class="php">/**
 * @param mixed ...$variable
 * @return void
 */
glsr_debug(...$variable);</code></pre>
        <p>This function prints one or more variables (strings, arrays, objects, etc.) to the screen in human-readable format. You can include as many variables as you want separated by commas.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">glsr_debug($var1, $var2, $var3);

// OR:

apply_filters('glsr_debug', null, $var1, $var2, $var3);
</code></pre>
    </div>
</div>

<div id="functions-04" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to get a plugin setting</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <pre><code class="php">/**
 * @param string $path
 * @param mixed $fallback
 * @return string|array
 */
glsr_get_option($path = '', $fallback = '');</code></pre>
        <p>The <code>$path</code> variable is required and is the dot-notation path of the option you want to get. You build a dot-notation string by using the array keys leading up to the value you wish to get.</p>
        <p>The <code>$fallback</code> variable is what you want to return if the option is not found or is empty. Default is an empty string.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">$requireApproval = glsr_get_option('general.require.approval', 'no');

// OR:

$requireApproval = apply_filters('glsr_get_option', 'no', 'general.require.approval', 'no');</code></pre>
        <p><strong>Helpful Tip:</strong></p>
        <p>You can use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-03">glsr_debug</a></code> helper function to view the whole plugin settings array, this will help you figure out which dot-notation path to use.</p>
        <pre><code class="php">glsr_debug(glsr_get_options());</code></pre>
    </div>
</div>

<div id="functions-05" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to get all plugin settings</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
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
        <pre><code class="php">$pluginSettings = glsr_get_options();

// OR:

$pluginSettings = apply_filters('glsr_get_options', []);</code></pre>
        <p><strong>Helpful Tip:</strong></p>
        <p>You can use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-03">glsr_debug</a></code> helper function to print the settings array to the screen:</p>
        <pre><code class="php">glsr_debug($pluginSettings);</code></pre>
    </div>
</div>

<div id="functions-06" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to get a single review</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <pre><code class="php">/**
 * @param int $post_id
 * @return \GeminiLabs\SiteReviews\Review
 */
glsr_get_review($post_id);</code></pre>
        <p>This helper function returns an arrayable Review object with super-powers!</p>
        <p>The <code>$post_id</code> variable is required and is the $post->ID of the review you want to get. An invalid post ID will return an empty Review object.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">$review = glsr_get_review(13);

// OR:

$review = apply_filters('glsr_get_review', null, 13);</code></pre>
        <p><strong>Helpful Tips:</strong></p>
        <ol>
            <li>
                <p>Print a specific (non-rendered) Review value to the page:</p>
                <pre><code class="php">echo $review->author; // OR: echo $review['author'];</code></pre>
            </li>
            <li>
                <p>Print the rendered (HTML) review to the page:</p>
                <pre><code class="php">$review->render(); // OR: echo $review;</code></pre>
            </li>
            <li>
                <p>Render (as HTML) all of the review fields and return them as an arrayable object that can be looped through:</p>
                <pre><code class="php">$reviewHtml = $review->build();</code></pre>
            </li>
            <li>
                <p>Print a specific rendered (HTML) field to the page:</p>
                <pre><code class="php">echo $reviewHtml->author; // OR: echo $reviewHtml['author'];</code></pre>
            </li>
            <li>
                <p>Print the rendered (HTML) review to the page:</p>
                <pre><code class="php">echo $reviewHtml; // This is identical to: $review->render();</code></pre>
            </li>
            <li>
                <p>You can also use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-03">glsr_debug</a></code> helper function to print both arrayable objects to the screen:</p>
                <pre><code class="php">glsr_debug($review, $reviewHtml);</code></pre>
            </li>
        </ol>
    </div>
</div>

<div id="functions-07" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to get multiple reviews</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <pre><code class="php">/**
 * @return \GeminiLabs\SiteReviews\Reviews
 */
glsr_get_reviews(array $args = []);</code></pre>
        <p>This helper function returns an arrayable Reviews object with super-powers!</p>
        <p>The <code>$args</code> variable is optional, but if included it must be an array.</p>
        <p><strong>Default $args array:</strong></p>
        <pre><code class="php">$args = [
    'assigned_to' => '',
    'category' => '',
    'offset' => '',
    'order' => 'DESC',
    'orderby' => 'date',
    'pagination' => false,
    'per_page' => 10,
    'post__in' => [],
    'post__not_in' => [],
    'rating' => '',
    'type' => '',
];</code></pre>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">$reviews = glsr_get_reviews([
    'assigned_to' => 'post_id',
    'rating' => 3,
]);

// OR:

$reviews = apply_filters('glsr_get_reviews', [], [
    'assigned_to' => 'post_id',
    'rating' => 3,
]);</code></pre>
        <p><strong>Helpful Tips:</strong></p>
        <ol>
            <li>
                <p>Print (as HTML) ALL reviews, including pagination (if included in the $args) to the page:</p>
                <pre><code class="php">echo $reviews; // This is identical to: $reviews->render();</code></pre>
            </li>
            <li>
                <p>Loop through all reviews and handle each review as needed. Each <code>$review</code> is identical to what the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-06">glsr_get_review</a></code> helper function returns, so make sure to read the "Helpful Tips" from that section above for more information.</p>
                <pre><code class="php">foreach ($reviews as $review) {
    echo $review;
};</code></pre>
            </li>
            <li>
                <p>Use the "max_num_pages" value when creating your own custom navigation:</p>
                <pre><code class="php">$totalPages = $reviews->max_num_pages;</code></pre>
            </li>
            <li>
                <p>Render (as HTML) all reviews, including pagination (if included in the $args), and return them as an arrayable object. You can then loop through this object like an array, the object also contains the navigation HTML (if "pagination"  is included in the $args):</p>
                <pre><code class="php">$reviewsHtml = $reviews->build();
foreach ($reviewsHtml as $reviewHtml) {
    echo $reviewHtml;
}
echo $reviewsHtml->navigation;</code></pre>
            </li>
            <li>
                <p>You can also use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-03">glsr_debug</a></code> helper function to print both arrayable objects to the screen:</p>
                <pre><code class="php">glsr_debug($reviews, $reviewsHtml);</code></pre>
            </li>

        </ol>
    </div>
</div>

<div id="functions-08" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to generate HTML stars for a rating</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>This helper function allows you to build a 1-5 star rating HTML string.</p>
        <pre><code class="php">/**
 * @param int $rating (a number from 1-5)
 * @return void
 */
glsr_star_rating($rating);</code></pre>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">echo glsr_star_rating(4);

// OR:

echo apply_filters('glsr_star_rating', null, 4);</code></pre>
    </div>
</div>

<div id="functions-09" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to log variables to the plugin console</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <pre><code class="php">/**
 * @param null|mixed $var
 * @return \GeminiLabs\SiteReviews\Modules\Console
 */
glsr_log($var = null);</code></pre>
        <p>This chainable function logs a variable (strings, arrays, objects, etc.) to the plugin console with a default logging level of "debug".</p>
        <p>There are eight available logging levels: <code>debug</code>, <code>info</code>, <code>notice</code>, <code>warning</code>, <code>error</code>, <code>critical</code>, <code>alert</code>, and <code>emergency</code>. Since glsr_log() returns an instance of the Console class, you can chain as many logging levels together as needed.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">glsr_log($var1);
glsr_log()->warning($var2);
glsr_log()->error($var4)->debug($var5);

// OR:

apply_filters('glsr_log', null, $var1);</code></pre>
    <p>Logged entries will be found in the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-console'); ?>">Tools &rarr; Console</a></code>.</p>
    </div>
</div>

<div id="functions-10" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Helper function to recalculate the rating counts</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>This helper function allows you to recalculate the rating counts used in the summary shortcode. In most cases this should not be necessary as Site Reviews automatically manages the rating counts.</p>
        <pre><code class="php">/**
 * @return void
 */
glsr_calculate_ratings();</code></pre>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="php">glsr_calculate_ratings();

// OR:

apply_filters('glsr_calculate_ratings', null);</code></pre>
    <p>You can verify that it runs by checking the log entries in the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-console'); ?>">Tools &rarr; Console</a></code>.</p>
    </div>
</div>
