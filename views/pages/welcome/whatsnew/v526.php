<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v5_26_0">
            <span class="title">Version 5.26</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_26_0" class="inside">
        <p><em>Initial Release Date &mdash; July 15th, 2022</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a new Modal implementation (if you are using the Review Images add-on, please update to v3)</li>
            <li>Added automatic conversion of UTF-16/UTF-32 encoded CSV files when importing reviews</li>
            <li>Added filter hooks to combine css and javascript files (see <a data-expand="#hooks-filter-combine-assets" href="<?= glsr_admin_url('documentation', 'hooks'); ?>">Hooks documentation</a>)</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with [Yoast Duplicate Post](https://wordpress.org/plugins/duplicate-post/)</li>
            <li>Fixed review importing: it now skips empty CSV rows without returning an error</li>
            <li>Fixed WPML integration</li>
        </ul>
    </div>
</div>
