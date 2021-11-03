<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="welcome-v5_17_0">
            <span class="title">Version 5.17</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_17_0" class="inside">
        <p><em>Initial Release Date &mdash; October 3rd, 2021</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added the <a href="<?= admin_url('edit.php?post_type=site-review&page=addons'); ?>">Review Notifications</a> add-on</li>
            <li>Added a setting to restrict the "Require Approval" setting to a minimum rating</li>
            <li>Added Accepted Terms field to privacy export (using the review creation date as the value if the terms were accepted)</li>
            <li>Added <a href="https://actionscheduler.org/">Action Scheduler</a> which allows plugin migrations and review notifications to be queued and dispatched automatically.</li>
            <li>Added support for additional date formats and columns in the "Import Third Party Reviews" tool</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Renamed the <code>email-notification.php</code> template file to <code>notification.php</code>. If you are using this template file in your theme, please rename it.</li>
        </ul>
        <h4>🛠 Tweaks</h4>
        <ul>
            <li>Updated the <a data-expand="#support-common-problems-and-solutions" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation&tab=support'); ?>">Common Problems and Solutions</a> documentation</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed HTML sanitization in review values</li>
            <li>Fixed importing of IP Addresses in the <a data-expand="#tools-import-reviews" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools&tab=general'); ?>">Import Third Party Reviews</a> tool</li>
            <li>Fixed importing to skip empty CSV rows</li>
            <li>Fixed non-ajax pagination when paginated URLs are disabled in the settings</li>
            <li>Fixed the removal of foreign key constraints in database tables when plugin is deactivated</li>
            <li>Fixed the <code>terms</code> field value to be false by default</li>
        </ul>
    </div>
</div>
