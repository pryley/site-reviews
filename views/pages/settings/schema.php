<?php defined('ABSPATH') || die; ?>

<p><?= _x('The JSON-LD schema is disabled by default. To use it, please enable the option in your blocks or shortcodes. The schema appears in Google\'s search results and shows the star rating and other information about your reviews. If the schema has been enabled, you can use Google\'s <a href="https://search.google.com/test/rich-results">Rich Results</a> tool to test your pages for valid schema data.', 'admin-text', 'site-reviews'); ?></p>
<p><?= _x('You may override any of these options on a per-post/page basis by using its Custom Field name and adding a custom value using the <a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">Custom Fields</a> metabox.', 'admin-text', 'site-reviews'); ?></p>
<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
