<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-customise-stars">
            <span class="title">How do I customise the stars?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-customise-stars" class="inside">
        <p>To customise the size of the stars, change the CSS variables with some custom CSS.</p>
        <p>Here is an example:</p>
        <pre><code class="language-css">.glsr {
    --glsr-form-star: 16px;
    --glsr-summary-star: 16px;
    --glsr-review-star: 16px;
}
</code></pre>
        <p>To customise the star images used by the plugin, use the <a data-expand="#hooks-filter-star-images" href="<?php echo glsr_admin_url('documentation', 'hooks'); ?>">site-reviews/config/inline-styles</a> filter hook in your theme's <code>functions.php</code> file.</p>
        <p>Here is an example:</p>
        <pre><code class="language-php">/**
 * Customises the stars used by Site Reviews.
 * Simply change and edit the URLs to match those of your custom images.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/inline-styles', function ($config) {
    $config[':star-empty'] = 'https://your-website.com/wp-content/uploads/star-empty.svg';
    $config[':star-error'] = 'https://your-website.com/wp-content/uploads/star-error.svg';
    $config[':star-full'] = 'https://your-website.com/wp-content/uploads/star-full.svg';
    $config[':star-half'] = 'https://your-website.com/wp-content/uploads/star-half.svg';
    return $config;
});</code></pre>
        <p>If all you need to do is change the colour of the stars:<p>
        <ol>
            <li>Copy the SVG images to your Desktop, the stars can be found here: <code>/wp-content/plugins/site-reviews/assets/images/</code></li>
            <li>Open the SVG images that you copied with a text editor</li>
            <li>Change the <a target="_blank" href="https://www.hexcolortool.com">hex colour code</a> to the one you want</li>
            <li>Install and activate the <a target="_blank" href="https://wordpress.org/plugins/safe-svg/">Safe SVG</a> plugin</li>
            <li>Upload the edited SVG images to your <a href="<?php echo admin_url('upload.php'); ?>">Media Library</a></li>
            <li>Copy the File URL of the uploaded SVG images and paste them into the snippet above</li>
        </ol>
    </div>
</div>
