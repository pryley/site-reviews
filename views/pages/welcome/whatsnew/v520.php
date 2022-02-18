<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_20_0">
            <span class="title">Version 5.20</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_20_0" class="inside">
        <p><em>Initial Release Date &mdash; January 26th, 2022</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a loading indicator when changing review status on the All Reviews page</li>
            <li>Added an "Author" filter to the All Reviews table</li>
            <li>Added hooks to override custom field sanitizers (for the <a href="https://niftyplugins.com/plugins/site-reviews-forms/" target="_blank">Review Forms</a> add-on)</li>
            <li>Added schema support for archive pages, it will now use the archive title and description if they exist</li>
            <li>Added Twenty Twenty-Two plugin style</li>
        </ul>
        <h4>🛠 Tweaks</h4>
        <ul>
            <li>Updated the polyfill.io script version</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed a security issue with AJAX requests @patchstackapp</li>
            <li>Fixed compatibility with Object Caching plugins (i.e. Docket Cache)</li>
            <li>Fixed compatibility with review pagination and the <a href="https://niftyplugins.com/plugins/site-reviews-images/" target="_blank">Review Images</a> add-on lightbox</li>
            <li>Fixed schema in review snippets when reviews have an anonymous author</li>
            <li>Fixed the category filter visibility in the All Reviews table</li>
            <li>Fixed the status option in the <a data-expand="#fn-glsr_get_reviews" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_get_reviews</a> function, it will no longer return trashed reviews</li>
            <li>Fixed the updater for inactive add-ons</li>
        </ul>
    </div>
</div>
