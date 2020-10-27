<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v520">
            <span class="title">Version 5.2</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v520" class="inside">
        <p><em>Initial Release Date &mdash; October 24th, 2020</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a review assignment setting</li>
        </ul>
        <h4>🛠 Tweaks</h4>
        <ul>
            <li>Added additional System Information</li>
            <li>Added "ORDER BY" to migration SQL queries; this will make migrations run a little slower but fix a potential problem where database migrations may not complete successfully</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed autoloading of the PHP multibyte polyfill</li>
            <li>Fixed multisite compatibility</li>
            <li>Fixed the submission date of reviews to use the timezone offset in the WordPress settings</li>
            <li>Fixed compatibility issue with the Elementor Pro Popups</li>
            <li>Fixed validation in the glsr_create_review helper function</li>
            <li>Fixed addons notice styling and placement</li>
            <li>Fixed plugin file paths on IIS Windows servers</li>
            <li>Fixed plugin migrations to work better with the W3 Total Cache plugin</li>
            <li>Fixed strict standard notices in PHP 5.6</li>
        </ul>
    </div>
</div>
