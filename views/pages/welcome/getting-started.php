<?php defined('WPINC') || die; ?>

<div class="is-fullwidth">
    <div class="glsr-flex-row glsr-has-2-columns">
        <div class="glsr-column">
            <h3>Editor Blocks</h3>
            <p>The fastest way to getting started with Site Reviews is to use the three provided blocks in the WordPress Block Editor. Each block comes with multiple settings which let you configure the block exactly as needed. To add a block to your page, click the "Add Block" button and search for "Site Reviews".</p>
            <img class="screenshot" src="<?= glsr()->url('assets/images/blocks.png'); ?>" alt="Editor Blocks" />
        </div>
        <div class="glsr-column">
            <h3>Shortcodes and Widgets</h3>
            <p>You can also use the shortcodes or widgets on your page. Keep in mind, however, that widgets are limited in options compared to the shortcodes (for example, the "Latest Reviews" widget does not allow pagination). If you are using the Classic Editor in WordPress, you can click on the Site Reviews shortcode button above the editor (next to the media button) to add a shortcode via a friendly popup.</p>
            <p>To learn more about the shortcodes and the available shortcode options, please see the Shortcode Documentation page of the plugin.</p>
            <a class="button" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">View Shortcode Documentation</a>
        </div>
    </div>
</div>
<hr>
<div class="is-fullwidth">
    <h2>Features</h2>
    <ul class="glsr-flex-row glsr-has-3-columns">
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=addons'); ?>">Add-ons</a></h3>
            <p>Extend Site Reviews with add-ons that provide additional features.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-reviews'); ?>">Avatars</a></h3>
            <p>Enable avatars to generate images using the WordPress Gravatar service.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-general'); ?>">Backup/Restore</a></h3>
            <p>Backup and restore your plugin settings as needed.</p>
        </li>
        <li class="glsr-column">
            <h3><a data-expand="#faq-14" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">Bayesian Ranking</a></h3>
            <p>Easily rank pages with assigned reviews using the bayesian algorithm.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-submissions'); ?>">Blacklist</a></h3>
            <p>Blacklist words, phrases, IP addresses, names, and emails.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-general'); ?>">Blockchain Validation</a></h3>
            <p>Verify your reviews on the Blockchain with <a href="https://trustalyze.com?ref=105">Trustalyze</a>.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit-tags.php?taxonomy=site-review-category&post_type=site-review'); ?>">Categories</a></h3>
            <p>Add your own categories and assign reviews to them.</p>
        </li>
        <li class="glsr-column">
            <h3><a target="_blank" href="https://github.com/pryley/site-reviews/blob/master/HOOKS.md">Developer Friendly</a></h3>
            <p>Designed for WordPress developers with over 100 filter hooks and convenient functions.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">Documentation</a></h3>
            <p>Provides FAQ and documenation for hooks and all shortcodes and functions.</p>
        </li>
        <li class="glsr-column">
            <h3><a target="_blank" href="https://wordpress.org/support/article/adding-a-new-block/">Editor Blocks</a></h3>
            <p>Use configurable editor blocks in the new WordPress 5.0 editor.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-schema'); ?>">JSON-LD Schema</a></h3>
            <p>Enable JSON-LD schema to display your reviews and ratings in search results.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-general'); ?>">Multilingual</a></h3>
            <p>Integrates with Polylang and WPML and provides easy search/replace translation.</p>
        </li>
        <li class="glsr-column">
            <h3><a target="_blank" href="https://wordpress.org/support/article/create-a-network/">Multisite Support</a></h3>
            <p>Provides full support for the WordPress multisite feature.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-general'); ?>">Notifications</a></h3>
            <p>Send notifications to one or more emails when a review is submitted.</p>
        </li>
        <li class="glsr-column">
            <h3><a data-expand="#faq-03" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">Page Assignment</a></h3>
            <p>Assign reviews to Posts, Pages, and Custom Post Types.</p>
        </li>
        <li class="glsr-column">
            <h3><a data-expand="#faq-02" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">Pagination</a></h3>
            <p>Enable AJAX pagination to display a custom number of reviews per-page.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.''); ?>">Responses</a></h3>
            <p>Write a response to reviews that require a response.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-general'); ?>">Restrictions</a></h3>
            <p>Require approval before publishing reviews and limit to registered users.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-submissions'); ?>">Review Limits</a></h3>
            <p>Limit review submissions by email address, IP address, or username.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">Review Summaries</a></h3>
            <p>Display a summary of your review ratings from high to low.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">Shortcodes</a></h3>
            <p>Use the configurable shortcodes complete with documentation.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-general'); ?>">Slack</a></h3>
            <p>Receive notifications in Slack when a review is submitted.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-submissions'); ?>">SPAM Protection</a></h3>
            <p>Uses a Honeypot and integrates with Invisible reCAPTCHA and Akismet.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-general'); ?>">Styles</a></h3>
            <p>Change the submission form style to match popular themes and form plugins.</p>
        </li>
        <li class="glsr-column">
            <h3><a target="_blank" href="https://wordpress.org/support/plugin/site-reviews/">Support</a></h3>
            <p>Free premium-level support included on the WordPress support forum.</p>
        </li>
        <li class="glsr-column">
            <h3><a data-expand="#faq-18" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">Templates</a></h3>
            <p>Use the Site Reviews templates in your theme for full control over the HTML.</p>
        </li>
        <li class="glsr-column">
            <h3><a href="<?= admin_url('widgets.php'); ?>">Widgets</a></h3>
            <p>Use the configurable widgets in your sidebars.</p>
        </li>
    </ul>
</div>
