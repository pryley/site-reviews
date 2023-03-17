<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-common-problems-and-solutions">
            <span class="title">Common Problems and Solutions</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-common-problems-and-solutions" class="inside">

        <h3>After restoring my database from a backup, reviews are no longer being assigned.</h3>
        <p>To fix this problem, deactivate the Site Reviews plugin and activate it again.</p>

        <h3>Email notifications are not working.</h3>
        <p>Site Reviews uses the standard WordPress mail functions to send emails. However, this does not guarantee that emails will send successfully if your WordPress settings and server configuration are incorrect.</p>
        <p>To ensure notifications are sent, please verify that you are sending from an email address that uses the same domain as your website. For example, if your website is <code>https://reviews.com</code>, the email address you are sending from should end with <code>@reviews.com</code>. You can change the email address that notifications are sent from in the <code><a href="<?= glsr_admin_url('settings', 'general'); ?>">settings</a></code>. If the <em>Send Emails From</em> email address saved in the settings does not share the same domain as your website, you will likely experience issues sending emails.</p>
        <p>If notifications are still not sending, I recommend you install the <a href="https://wordpress.org/plugins/check-email/" target="_blank">Check Email</a> plugin to verify that your website can correctly send emails. See also, <a href="https://www.butlerblog.com/2013/12/12/easy-smtp-email-wordpress-wp_mail/" target="_blank">Easy SMTP email settings for WordPress</a>.</p>

        <h3>The "Site Reviews will automatically migrate your reviews and settings to the latest version" notice keeps appearing.</h3>
        <p>This message may appear after updating Site Reviews. Please click the "Run Migration" button in the notice if it does. If it continues to show after reloading your pages:</p>
        <ol>
            <li>
                <p>Check the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> page. If any entries say "Unknown character set", you will need to check your wp-config.php file to see if it defines <a href="https://wordpress.org/support/article/editing-wp-config-php/#database-character-set" target="_blank">DB_CHARSET</a> or <a href="https://wordpress.org/support/article/editing-wp-config-php/#database-collation" target="_blank">DB_COLLATE</a>. If it does, either remove those entries or ensure the values they define are correct, then try again.</p>
            </li>
            <li>
                <p>You may be using a caching plugin which is caching the database and preventing Site Reviews from storing the migration status. To fix this, you will need to flush your database cache and/or object cache and then try again.</p>
            </li>
            <li>
                <p>You may have 3rd-party reviews that were not imported correctly. You can verify this by looking on the <code><a href="<?= glsr_admin_url(); ?>">All Reviews</a></code> page for reviews that do not have any stars. To fix this, delete the invalid reviews and empty the trash. Afterwards, use the provided <code><a href="<?= glsr_admin_url('tools', 'general'); ?>" data-expand="#tools-import-reviews">Import Reviews</a></code> tool to re-import the reviews and then try again.</p>
            </li>
            <li>
                <p>You may have reviews that were duplicated or cloned with an unsupported plugin. Most duplication plugins will not work with Site Reviews because the review details are stored in a custom database table. Please delete the duplicated reviews, empty the trash and try again.</p>
            </li>
        </ol>

        <h3>The review form is not working; the submit button spins forever.</h3>
        <ol>
            <li>
                <p>Does your website have an SSL certificate? If it does, ensure your website is configured to always use it by using an SSL plugin like <a href="https://wordpress.org/plugins/really-simple-ssl/" target="_blank">Really Simple SSL</a>. If your website has a valid SSL certificate, but you view it using <code>http://</code> instead of <code>https://</code>, the browser will detect this as a <em>Cross-Domain Request</em> and prevent you from submitting the review.</p>
            </li>
            <li>
                <p>Are you using a security plugin to disable access to <code>/wp-admin/admin-ajax.php</code> on the front end of your website? Or, have you disabled access to <code>/wp-admin/</code> for non-administrators? If you have, this could be preventing Site Reviews from submitting reviews.</p>
            </li>
            <li>
                <p>Are you using Cloudflare? Have you created a Cloudflare Firewall rule to block access to the WordPress admin? If so, you might have incorrectly configured the rule. See also: <a href="https://turbofuture.com/internet/Cloudflare-Firewall-Rules-for-Securing-WordPress#3-protect-the-wp-admin-area" target="_blank">Cloudflare Firewall Rules for Securing WordPress</a></p>
            </li>
            <li>
                <p>Was the review created? Do you have notifications enabled in the Site Reviews settings? If so, then your server might not be configured to send emails. You must either disable the notification setting for new reviews or fix your server configuration.</p>
            </li>
            <li>
                <p>Finally, check the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> page for helpful messages that may help you track down the cause of the problem.</p>
            </li>
        </ol>

        <h3>The review form is not working; it returns an error message.</h3>
        <p>Here is a list of possible errors and what they mean:</p>
        <ol>
            <li>
                <h4 class="components-notice is-error" style="font-size:15px;">
                    <?= __('The form could not be submitted. Please notify the site administrator.', 'site-reviews'); ?>
                </h4>
                <p>This is an Ajax error that is triggered by any of the following reasons:</p>
                <ul>
                    <li>The Form Request is missing a Nonce</li>
                    <li>The Form Request failed the Nonce check</li>
                    <li>The Form Request is missing a required action</li>
                    <li>The Form Request is invalid</li>
                </ul>
                <p>To fix the nonce errors, ensure you are not caching the review page for logged-in users because Site Reviews adds a <a href="https://www.bynicolas.com/code/wordpress-nonce/" target="_blank">WordPress Nonce</a> token to the form if the user is logged in. Nonces are a standard WordPress security feature that helps to prevent malicious form submissions. Still, they will not work if your web pages are cached because the nonce tokens are time-sensitive, and their validity expires after 12 hours.</p>
                <p>Alternatively, you may remove the Nonce check as shown on the FAQ page: <code><a data-expand="#faq-remove-nonce-check" href="<?= glsr_admin_url('documentation', 'faq'); ?>">How do I remove the WordPress Nonce check for logged-in users?</a></code>.</p>
            </li>
            <li>
                <h4 class="components-notice is-error" style="font-size:15px;">
                    <?= __('Your review cannot be submitted at this time.', 'site-reviews'); ?>
                </h4>
                <p>This error is shown when the Blacklist validator prevents the review from being submitted.</p>
            </li>
            <li>
                <h4 class="components-notice is-error" style="font-size:15px;">
                    <?= __('This review has been flagged as possible spam and cannot be submitted.', 'site-reviews'); ?>
                </h4>
                <p>This error is shown when the Akismet or Honeypot validator prevents the review from being submitted.</p>
            </li>
            <li>
                <h4 class="components-notice is-error" style="font-size:15px;">
                    <?= __('Your review could not be submitted and the error has been logged. Please notify the site administrator.', 'site-reviews'); ?>
                </h4>
                <p>This error is triggered when WordPress encounters an error when saving the review to the database.</p>
                <p>If you encounter this error:</p>
                <ul>
                    <li><p>Check for any invalid custom functions you may have added to your theme's <code>functions.php</code> file that might be triggered after a review is created.</p></li>
                    <li><p>Deactivate Site Reviews and then reactivate it. If you recently cloned your database or restored it from a backup, this <em>should</em> fix any broken database table indexes.</p></li>
                    <li><p>If you imported or cloned your database and can redo it, try deactivating Site Reviews before you export the database. This should prevent any issues related to broken indexes.</p></li>
                    <li>
                        <p>Finally, there is the "Nuclear" option:</p>
                        <p class="components-notice is-warning" style="margin-bottom:1em;">
                            <i class="dashicons dashicons-warning" style="color:#f0b849; margin-right:5px;"></i>
                            Only use this "Nuclear" option as a last resort, as it will delete all your reviews and settings!
                        </p>
                        <ul>
                            <li>Go to the Site Reviews settings.</li>
                            <li>Change the "Delete data on uninstall" option to "Delete everything".</li>
                            <li>Uninstall Site Reviews.</li>
                            <li>Install a new copy of Site Reviews.</li>
                        </ul>
                    </li>
                </ul>
                <p>If these solutions do not work, please contact support for assistance.</p>
            </li>
            <li>
                <h4 class="components-notice is-error" style="font-size:15px;">
                    <?= __('The review submission failed. Please notify the site administrator.', 'site-reviews'); ?>
                </h4>
                <p>This error is shown if you have added any custom validation functions which are returning false You can override this error with your own by returning an error message instead of <code>false</code> in your custom validation logic.</p>
            </li>
            <li>
                <h4 class="components-notice is-error" style="font-size:15px;">
                    <?= __('Service Unavailable.', 'site-reviews'); ?>
                </h4>
                <p>If your website is using Cloudflare and you configured a Firewall rule to block access to <code>wp-admin</code>, then this is likely causing the error. Site Reviews uses the <code>/wp-admin/admin-ajax.php</code> file to submit AJAX requests; this is standard practice for WordPress plugins. To learn how to correctly configure Cloudflare to protect your <code>wp-admin</code> without blocking access to "admin-ajax.php", please see: <a href="https://turbofuture.com/internet/Cloudflare-Firewall-Rules-for-Securing-WordPress" target="_blank">Cloudflare Firewall Rules for Securing WordPress</a></p>
            </li>
        </ol>
        <p>Finally, in each case, you should also check the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> page for any error messages that may have been logged. These provide additional information on the error and why it happened.</p>
    </div>
</div>
