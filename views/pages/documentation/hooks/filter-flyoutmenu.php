<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-filter-scripts">
            <span class="title">Disable the flyout menu</span>
            <span class="badge code">site-reviews/flyoutmenu/enabled</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-filter-scripts" class="inside">
        <p>Use this hook if you want to disable the flyout menu. The flyout menu is the little mascot button displayed on the bottom-right of WP Admin Site Reviews pages.</p>
        <pre><code class="language-php">/**
 * Disables the Site Reviews flyout menu.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/flyoutmenu/enabled', '__return_false');</code></pre>
    </div>
</div>
