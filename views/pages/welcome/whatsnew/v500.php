<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v500">
            <span class="title">Version 5.0.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v500" class="inside">
        <p><em>Release Date &mdash; September 13th, 2020</em></p>
        <h4>New Features</h4>
        <ul>
            <li>Added <code>assigned_posts</code> shortcode option, this replaces the "assign_to" and "assigned_to" options and allows you to assign reviews to multiple Post IDs</li>
            <li>Added <code>assigned_terms</code> shortcode option, this replaces the "category" option and allows you to assign reviews to multiple Categories</li>
            <li>Added <code>assigned_users</code> shortcode option, this allows you to assign reviews to multiple User IDs</li>
            <li>Added "Delete data on uninstall" option to selectively delete plugin data when removing the plugin</li>
            <li>Added "Send Emails From" option to send notifications from a custom email address</li>
            <li>Added <a href="https://wordpress.org/support/article/wordpress-privacy/#privacy-policy-editing-helper">suggested privacy policy content</a></li>
            <li>Added tool to test IP address detection</li>
            <li>Added <a href="https://wordpress.org/support/article/wordpress-privacy/#erase-personal-data-tool">WordPress Personal Data Eraser</a> integration</li>
            <li>Added <a href="https://wordpress.org/support/article/wordpress-privacy/#export-personal-data-tool">WordPress Personal Data Exporter</a> integration</li>
            <li>Added <a href="https://wordpress.org/support/article/revisions/">WordPress Revisions</a> integration</li>
            <li>Site Reviews now uses custom database tables, however you may still use the WordPress Export/Import tools as before</li>
            <li>The Review Details metabox now allows you to modify any value</li>
            <li>The <code>site-reviews/after/submission</code> javascript event now contains the submitted review</li>
        </ul>
        <h4>Changes</h4>
        <ul>
            <li>Changed the <code>assigned_to</code> <strong>hide option value</strong> to <code>assigned_links</code> (i.e. [site_reviews hide="assigned_links"])</li>
            <li>Changed the minimum PHP version to 5.6.20</li>
            <li>Changed the minimum WordPress version to 5.5</li>
            <li>Changed the settings to use the WordPress "Disallowed Comment Keys" by default</li>
            <li>Renamed the <code>glsr_get_rating()</code> helper function to <code>glsr_get_ratings()</code></li>
            <li>Replaced the <code>assign_to</code> and <code>assigned_to</code> shortcode options with the <code>assigned_posts</code> option</li>
            <li>Replaced the <code>category</code> shortcode option with "assigned_terms" option</li>
        </ul>
        <h4>Tweaks</h4>
        <ul>
            <li>Added the <code>loading="lazy"</code> attribute to avatars</li>
            <li>Drastically improved plugin performance with thousands of reviews</li>
            <li>Improved console logging</li>
            <li>Improved documentation</li>
            <li>Improved translation settings</li>
            <li>Refreshed the blocks to visually match the WordPress 5.5 editor style</li>
            <li>The Terms checkbox in the submission form should now align correctly with the text</li>
            <li>Updated Trustalyze integration</li>
        </ul>
        <h4>Removed</h4>
        <ul>
            <li>Removed the <code>glsr_calculate_ratings()</code> helper function</li>
            <li>Removed the tool to calculate rating counts</li>
        </ul>
        <h4>Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with the Divi theme and Divi Builder plugin</li>
            <li>Fixed compatibility with the Elementor Pro plugin popups</li>
            <li>Fixed compatibility with the GeneratePress Premium plugin</li>
            <li>Fixed compatibility with the Members plugin</li>
            <li>Fixed review summary bars in IE11</li>
            <li>Fixed Welcome page permissions</li>
        </ul>
    </div>
</div>
