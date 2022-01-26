<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_19_0">
            <span class="title">Version 5.19</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_19_0" class="inside">
        <p><em>Initial Release Date &mdash; December 30th, 2021</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added <code>author_id</code> as an accepted value in the <code>assigned_users</code> shortcode option</li>
            <li>Added <code>author_id</code> column to the <a data-expand="#tools-import-reviews" href="<?= glsr_admin_url('tools', 'general'); ?>">Import Third Party Reviews</a> tool</li>
            <li>Added settings to customise the login and register URLs</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed API usage when creating reviews using string values for the assigned_posts (i.e. post_type:slug), assigned_terms (i.e. slug), and assigned_users (i.e. username) parameters</li>
            <li>Fixed approve/unapprove actions on the All Reviews page which was removing the post_author ID of reviews</li>
            <li>Fixed importing reviews with unknown author</li>
            <li>Fixed the <a data-expand="#faq-redirect-after-submission" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ documentation</a> for redirects</li>
            <li>Fixed the "hide" option when using AJAX pagination</li>
            <li>Fixed the loading indicator (spinner) on the submit button of the review form</li>
        </ul>
    </div>
</div>
