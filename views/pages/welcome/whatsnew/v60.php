<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v6_0_0">
            <span class="title">Version 6.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v6_0_0" class="inside">
        <p><em>Initial Release Date &mdash; August 15th, 2022</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a "Limit Reviews For" setting which allows you to apply review limits for a specified number of days</li>
            <li>Added automatic conversion of UTF-16/UTF-32 encoded CSV files when importing reviews</li>
            <li>Added experimental filter hooks to combine css and javascript files when using add-ons (see <a data-expand="#hooks-filter-combine-assets" href="<?= glsr_admin_url('documentation', 'hooks'); ?>">Hooks documentation</a>)</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Requires at least PHP v7.2</li>
            <li>Requires at least WordPress v5.8</li>
            <li>Rewrote expanding excerpts, they now support multiple paragraphs</li>
            <li>Rewrote the review modals to support the new review image galleries (<a href="https://niftyplugins.com/plugins/site-reviews-images/" target="_blank">Review Images</a> add-on required)</li>
            <li>Submit and load more buttons now make use of the WordPress Block button classes</li>
        </ul>
        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed support for Internet Explorer</li>
            <li>Removed support for PHP 5.6, 7.0, and 7.1</li>
            <li>Removed the Polyfill.io script</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with <a href="https://wordpress.org/plugins/duplicate-post/" target="_blank">Yoast Duplicate Post</a></li>
            <li>Fixed invalid "deprecated" entries which were being added to the Console on some websites</li>
            <li>Fixed review importing to skip empty CSV rows without throwing an error</li>
            <li>Fixed the blocks in the Customizer widget panel</li>
            <li>Fixed the star rating field for some themes</li>
            <li>Fixed the WPML integration</li>
        </ul>
    </div>
</div>

