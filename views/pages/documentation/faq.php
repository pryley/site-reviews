<?php defined('WPINC') || die; ?>

<div id="faq-01" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I add additional values to the schema?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To add additional values to the generated schema, use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-hooks'); ?>" data-expand="#hooks-07">site-reviews/schema/[SCHEMA_TYPE]</a></code> hook in your theme's functions.php file.</p>
        <p>Make sure to use Google's <a href="https://search.google.com/structured-data/testing-tool">Structured Data Testing Tool</a> to test the schema after any custom modifications have been made.</p>
        <pre><code class="php">/**
 * Modifies the schema created by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @param array $schema
 * @return array
 */
add_filter('site-reviews/schema/LocalBusiness', function ($schema) {
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
        <h3>How do I add pagination to my reviews?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To paginate your reviews (i.e. split them up into multiple pages), simply use the "Pagination" setting together with the "Review Count" setting in the Editor Block.</p>
        <p>If you are using the shortcodes, then use the <code>pagination</code> and <code>count</code> options.</p>
        <p>For example, this will paginate reviews to 10 reviews per-page:</p>
        <pre><code class="php">[site_reviews pagination=ajax count=10]</code></pre>
        <p>To lean more about the available shortcode options and how to use them, please see the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">Documentation > Shortcodes</a></code> page.</p>
    </div>
</div>

<div id="faq-03" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I assign reviews to a page?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To assign reviews to a page, simply use the "Assign To/Assigned To" setting in the Editor Blocks.</p>
        <p>If you are using the shortcodes, then use the <code>assign_to</code> and <code>assigned_to</code> options like this:
        <pre><code class="php">// This will assign submitted reviews to the current page
[site_reviews_form assign_to=post_id]

// This will only display the reviews that have been assigned to the current page:
[site_reviews assigned_to=post_id]

// This will only display the summary for reviews that have been assigned to the current page:
[site_reviews_summary assigned_to=post_id]</code></pre>
        <p>If you use <code>post_id</code> as the value, then Site Reviews will know to automatically use the Page ID of the current page.</p>
        <p>If you use <code>parent_id</code> as the value, then Site Reviews will know to automatically use the Page ID of the current page's Parent.</p>
        <p>You can, of course, also directly enter the numerical WordPress Page ID of the page instead if your prefer.</p>
        <p>To lean more about the available shortcode options and how to use them, please see the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">Documentation > Shortcodes</a></code> page.</p>
    </div>
</div>

<div id="faq-04" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I change the font?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Site Reviews does not impose its own font as most styling is inherited by your theme's stylesheet. However, you can easily customise this with some CSS.</p>
        <pre><code class="css">[class*=glsr-] {
    font-family: monospace;
}</code></pre>
    </div>
</div>

<div id="faq-05" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I change the order of the review fields?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
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

<div id="faq-06" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I change the order of the reviews summary fields?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
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

<div id="faq-07" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I change the order of the submission form fields?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To customise the order of the fields in the review submission form, use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-hooks'); ?>" data-expand="#hooks-01">site-reviews/submission-form/order</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
        <pre><code class="php">/**
 * Customises the order of the fields used in the Site Reviews submission form.
 * Paste this in your active theme's functions.php file.
 * @param array $order
 * @return array
 */
add_filter('site-reviews/submission-form/order', function ($order) {
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
        <p>If you have used the example above and the submission-form fields are not working correctly, check the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-console'); ?>">Tools &rarr; Console</a></code> for errors.</p>
    </div>
</div>

<div id="faq-08" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I change the pagination query string?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
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
add_filter('site-reviews/const/PAGED_QUERY_VAR', function () {
    // change this to your preferred query string
    return 'reviews-page';
});</code></pre>
    </div>
</div>

<div id="faq-09" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I change the text of...?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>You can change any text in the plugin on the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-translations'); ?>">Settings &rarr; Translations</a></code> page.</p>
    </div>
</div>

<div id="faq-10" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I create a review programmatically?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Site Reviews provides a <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#functions-02">glsr_create_review()</a></code> helper function to easily create a review.</p>
        <p>Here is an example:</p>
        <pre><code class="php">if (function_exists('glsr_create_review')) {
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

<div id="faq-11" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I customise the stars?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To customise the star images used by the plugin, use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-hooks'); ?>" data-expand="#hooks-02">site-reviews/config/inline-styles</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
        <p>Here is an example:</p>
        <pre><code class="php">/**
 * Customises the stars used by Site Reviews.
 * Simply change and edit the URLs to match those of your custom images.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/inline-styles', function ($config) {
    $config[':star-empty'] = 'https://your-website.com/images/star-empty.svg';
    $config[':star-error'] = 'https://your-website.com/images/star-error.svg';
    $config[':star-full'] = 'https://your-website.com/images/star-full.svg';
    $config[':star-half'] = 'https://your-website.com/images/star-half.svg';
    return $config;
});</code></pre>
        <p>If all you need to do is change the colour of the stars:<p>
        <ol>
            <li>Copy the SVG images to your Desktop, the stars can be found here: <code>/wp-content/plugins/site-reviews/assets/images/</code></li>
            <li>Open the SVG images that you copied with a text editor</li>
            <li>Change the <a target="_blank" href="https://www.hexcolortool.com">hex colour code</a> to the one you want</li>
            <li>Install and activate the <a target="_blank" href="https://wordpress.org/plugins/safe-svg/">Safe SVG</a> plugin</li>
            <li>Upload the edited SVG images to your <a href="<?= admin_url('upload.php'); ?>">Media Library</a></li>
            <li>Copy the File URL of the uploaded SVG images and paste them into the snippet above</li>
        </ol>
    </div>
</div>

<div id="faq-12" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I hide the form after a review is submitted?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To hide the form after a review has been submitted, use the following code snippet:</p>
        <pre><code class="php">/**
 * Hides the submission form after a review has been submitted
 * Paste this in your active theme's functions.php file
 *
 * @param string $script
 * @return string
 */
add_filter('site-reviews/enqueue/public/inline-script', function ($script) {
    return $script."
    document.addEventListener('site-reviews/after/submission', function (ev) {
        if (false !== ev.detail.errors) return;
        ev.detail.form.classList.add('glsr-hide-form');
        ev.detail.form.insertAdjacentHTML('afterend', '&lt;p&gt;' + ev.detail.message + '&lt;/p&gt;');
    });";
});</code></pre>
        <p>You can also hide the form from registered users who have already submitted a review.</p>
        <p>To do this, you will need to first make sure that the "Limit Reviews" setting on the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-submissions'); ?>">Settings &rarr; Submissions</a></code> page is set to "By Username". Once that is done, you can use the following code snippet:</p>
        <pre><code class="php">/**
 * Hides the submission form from registered users who have already submitted a review
 * Paste this in your active theme's functions.php file
 *
 * @param string $template
 * @return string
 */
add_filter('site-reviews/rendered/template/reviews-form', function ($template) {
    return glsr('Modules\ReviewLimits')->hasReachedLimit()
        ? sprintf('&lt;p&gt;%s&lt;/p&gt;', __('Thank you for your review!'))
        : $template;
});</code></pre>
    </div>
</div>


<div id="faq-13" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I limit the submitted review length?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
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
add_filter('site-reviews/config/forms/submission-form', function ($config) {
    if (array_key_exists('content', $config)) {
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
add_filter('site-reviews/validation/rules', function ($rules) {
    $rules['content'] = 'required|max:100';
    return $rules;
});</code></pre>
    </div>
</div>

<div id="faq-14" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I order pages with assigned reviews by rating or ranking?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Site Reviews provides two meta keys that can be used for sorting pages that have reviews assigned to them.</p>
        <p>The <code>_glsr_average</code> meta key contains the average rating of the page.</p>
        <p>The <code>_glsr_ranking</code> meta key contains the page rank determined by a bayesian ranking algorithm (the exact same way that films are ranked on IMDB). To understand why sorting by rank may be preferable to sorting by average rating, please see: <a target="_blank" href="https://imgs.xkcd.com/comics/tornadoguard.png">The problem with averaging star ratings</a>.</p>
        <p>Here is an example of how you can use these meta keys in a custom WP_Query. In this example, we will sort all pages by rank (highest to lowest) and regardless of whether or not they have reviews assigned to them:</p>
        <pre><code class="php">$query = new WP_Query([
    'meta_query' => [
        'relation' => 'OR',
        ['key' => '_glsr_ranking', 'compare' => 'NOT EXISTS'], // this comes first!
        ['key' => '_glsr_ranking', 'compare' => 'EXISTS'],
    ],
    'order' => 'DESC',
    'orderby' => 'meta_value_num',
    'post_status' => 'publish',
    'post_type' => 'page', // change this as needed
    'posts_per_page' => 10, // change this as needed
]);</code></pre>
        <p>If you would like to only query pages that actually have reviews assigned to them, you can do this instead:</p>
        <pre><code class="php">$query = new WP_Query([
    'meta_query' => [
        ['key' => '_glsr_ranking', 'compare' => '>', 'value' => 0],
    ],
    'order' => 'DESC',
    'orderby' => 'meta_value_num',
    'post_status' => 'publish',
    'post_type' => 'page', // change this as needed
    'posts_per_page' => 10, // change this as needed
]);</code></pre>
        <p>Once you have your custom query, you use it in your WordPress theme template like this:</p>
        <pre><code class="php">if( $query->have_posts() ) {
    while( $query->have_posts() ) {
        $query->the_post();
        $average = sprintf( '%s (average rating: %s)',
            get_the_title(),
            get_post_meta( $post->ID, '_glsr_average', true )
        );
        $ranking = sprintf( '%s (ranking: %s)',
            get_the_title(),
            get_post_meta( $post->ID, '_glsr_ranking', true )
        );
        apply_filters( 'glsr_debug', 'Site Reviews is not installed', $average, $ranking );
    }
    wp_reset_postdata();
}</code></pre>
        <p>To learn more about <code>WP_Query</code> and how to use it in your theme templates, please refer to the <a target="_blank" href="https://developer.wordpress.org/themes/basics/the-loop/">WordPress Theme Handbook</a>.</p>
    </div>
</div>

<div id="faq-15" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I prevent search engines from indexing paginated reviews?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>If you are paginating reviews then chances are high that search engines are indexing these pages. If you are not using a dedicated page for your reviews, then this may not be desirable for your SEO and it may result in the Google Search Console showing an alert.</p>
        <p>Here is how to prevent search engines from indexing your paginated reviews:</p>
        <ol>
            <li>Install and activate the <a href="https://wordpress.org/plugins/robots-txt-editor/">Robots.txt Editor</a> plugin.</li>
            <li>Go to the <code><a href="<?= admin_url('options-reading.php'); ?>">WordPress > Settings > Reading</a></code> page.</li>
            <li>Make sure that the Robots.txt starts with: <code>User-Agent: *</code></li>
            <li>Add the following lines:
                <pre><code>Disallow: /*?reviews-page=*
Disallow: /*?*reviews-page=*</code></pre>
            </li>
        </ol>
        <p>Once you have made your changes, you can tell Google to reindex your website like this:</p>
        <ol>
            <li>Login to the <a href="https://search.google.com/search-console">Google Search Console</a>.</li>
            <li>Add your website and verify it (if you haven't already done so).</li>
            <li>Click on URL Inspection and enter your website URL or custom URLs in the search bar.</li>
            <li>After Inspection it will provide an option to Request Indexing.</li>
        </ol>
    </div>
</div>

<div id="faq-16" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I redirect to a custom URL after a form is submitted?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>To redirect the page after a form has been submitted, edit the page the shortcode is on and use the <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a> metabox to add a <code>redirect_to</code> as the Custom Field name and the URL you want to redirect to as the value.</p>
    </div>
</div>

<div id="faq-17" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I remove the dash in front of the author's name?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
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

<div id="faq-18" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>How do I use the plugin templates in my theme?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
        <ol>
            <li>Create a folder in your theme called "site-reviews".</li>
            <li>Copy the template files that you would like to customise from <code>/wp-content/site-reviews/templates/</code> into this new folder.</li>
            <li>Open the template files that you copied over in a text editor and make your changes.</li>
        </ol>
    </div>
</div>

<div id="faq-19" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Why are the IP Addresses being detected as 127.0.0.1?</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p>If your server uses a reverse proxy (i.e. <a target="_blank" href="https://varnish-cache.org/intro/">Varnish</a>), then you may need to tell Site Reviews which custom header to use for IP Address detection. To add a custom header, use the following code snippet:</p>
        <pre><code class="php">/**
 * Add a custom reverse proxy header to fix IP detection
 * @param \Geminilabs\Vectorface\Whip $whip
 * @return void
 */
add_action('site-reviews/whip', function($whip) {
    $whip->addCustomHeader('X_REAL_IP'); // change the header as needed
});</code></pre>
    </div>
</div>
