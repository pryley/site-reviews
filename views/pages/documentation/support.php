<?php defined('WPINC') || die; ?>

<div id="support-01" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Basic Troubleshooting Steps</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <ol>
            <li>
                <p class="glsr-heading">Make sure you are using the latest version of Site Reviews.</p>
                <p>Site Reviews is updated frequently with bug patches, security updates, improvements, and new features. If you are not using the latest version and are experiencing problems, chances are good that your problem has already been addressed in the latest version.</p>
            </li>
            <li>
                <p class="glsr-heading">Temporarily switch to an official WordPress Theme.</p>
                <p>Try switching to an official WordPress Theme (i.e. Twenty Seventeen) and then see if you are still experiencing problems with the plugin. If this fixes the problem then there is a compatibility issue with your theme.</p>
            </li>
            <li>
                <p class="glsr-heading">Temporarily deactivate all of your plugins.</p>
                <p>If switching to an official WordPress theme did not fix anything, the final thing to try is to deactivate all of your plugins except for Site Reviews. If this fixes the problem then there is a compatibility issue with one of your plugins.</p>
                <p>To find out which plugin is incompatible with Site Reviews you will need to reactivate your plugins one-by-one until you find the plugin that is causing the problem. If you think that you’ve found the culprit, deactivate it and continue to test the rest of your plugins. Hopefully you won’t find any more but it’s always better to make sure.</p>
                <p>If you find an incompatible theme or plugin, please <em>contact support</em> so we can fix it.</p>
            </li>
        </ol>
    </div>
</div>

<div id="support-02" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Common Problems and Solutions</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <ul>
            <li>
                <p class="glsr-heading">Email notifications are not working</p>
                <p>Site Reviews uses the standard WordPress mail functions to send email. However, this does not guarantee that emails will send successfully as it still depends on your WordPress settings and server configuration being correct.</p>
                <p>To make sure that emails are correctly sent, please verify that the email you have saved in the "Email Address" setting of the <code><a href="<?= admin_url('options-general.php'); ?>">WordPress General Settings</a></code> page uses the same domain as that of your website. For example, if your website is <code>https://reviews.com</code> then the "Email Address" setting should end with, <code>@reviews.com</code>. If the email address you have saved in the WordPress General Settings does not share the same domain as your website, you will likely experience issues sending email from your WordPress site.</p>
                <p>If your email notifications are not sending, I recommend that you install the <a href="https://wordpress.org/plugins/check-email/">Check Email</a> plugin to verify that your website is able to correctly send email. See also, <a href="https://www.butlerblog.com/2013/12/12/easy-smtp-email-wordpress-wp_mail/">Easy SMTP email settings for WordPress</a>.</p>
            </li>
            <li>
                <p class="glsr-heading">I only want my reviews to show on the page they were published or assigned to but they are showing on every page.</p>
                <p>All reviews are unassigned by default. If you want to assign reviews to specific pages, you will need to use the “assign_to” and “assigned_to” shortcode options. Please see the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">Shortcodes</a></code> documentation page for more information.</p>
            </li>
            <li>
                <p class="glsr-heading">My submission form is not assigning reviews to the page even though I have set the option to do so.</p>
                <p>Make sure you are not mixing up the “assign_to” and “assigned_to” shortcode options.</p>
                <p>The <code>assign_to</code> shortcode option is used in the [site_reviews_form] shortcode to <em>assign</em> submitted reviews to a page.</p>
                <p>The <code>assigned_to</code> shortcode option is used with the [site_reviews] and [site_reviews_summary] shortcodes to only show reviews that have been <em>assigned</em> to a page.</p>
            </li>
            <li>
                <p class="glsr-heading">The review form is not working, the submit button just spins.</p>
                <p>Does your website have a SSL certificate? If it does, make sure that your website is configured to always use it by using a SSL plugin such as <a href="https://wordpress.org/plugins/really-simple-ssl/">Really Simple SSL</a>. Site Reviews will use HTTPS to submit a review if possible, but if your site has a valid SSL certificate and you are viewing the website using HTTP (instead of HTTPS) then the browser will detect this as a cross-domain request and prevent the review submission from completing.</p>
                <p>Have you enabled the reCAPTCHA setting? If you have, make sure that the "Site Key" and "Site Secret" have been entered and that they were generated for the Invisible reCAPTCHA (Google provides three different types of reCAPTCHA). Also, make sure that you correctly entered your website domain when creating the "Site Key" and "Site Secret".</p>
                <p>Have you used a security plugin to disable access to <code>/wp-admin/admin-ajax.php</code> on the frontend of your website, or have you disabled <code>/wp-admin/</code> access for non-administrators? If you have, then it's possible this is preventing Site Reviews from submitting reviews.</p>
            </li>
        </ul>
    </div>
</div>

<div id="support-03" class="glsr-card postbox">
    <div class="glsr-card-header">
        <h3>Contact Support</h3>
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?= __('Toggle documentation panel', 'site-reviews'); ?></span>
            <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
    </div>
    <div class="inside">
        <p><strong>The fastest way to get help</strong> is to post a support request in the <a href="https://wordpress.org/support/plugin/site-reviews/">WordPress Support Forum</a>. Using the support forum will also allow existing and future users of the plugin to benefit from the solution.</p>
        <p>However, you may also contact us directly (expect a slower response time) after confirming the following:</p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-1" class="glsr-support-step">
            <label for="step-1">I have read the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-support'); ?>" data-expand="#support-02">Common Problems and Solutions</a></code> and it does not answer my question.</label>
        </p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-2" class="glsr-support-step">
            <label for="step-2">I have read the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-faq'); ?>">FAQ</a></code> page and it does not answer my question.</label>
        </p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-3" class="glsr-support-step">
            <label for="step-3">I have read the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-shortcodes'); ?>">Shortcodes</a></code> page and it does not answer my question.</label>
        </p>
        <?php if (glsr()->hasPermission('documentation', 'hooks')) : ?>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-4" class="glsr-support-step">
            <label for="step-4">I have read the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-hooks'); ?>">Hooks</a></code> page and it does not answer my question.</label>
        </p>
        <?php endif; ?>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-5" class="glsr-support-step">
            <label for="step-5">I have completed the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-support'); ?>" data-expand="#support-01">Basic Troubleshooting Steps</a></code> provided above.</label>
        </p>
        <div class="glsr-card-result hidden">
            <p><strong>Please send an email to <a href="mailto:site-reviews@geminilabs.io?subject=Support%20request">site-reviews@geminilabs.io</a> and include the following details:</strong></p>
            <ul>
                <li>A detailed description of the problem you are having and steps to reproduce it.</li>
                <li>Download and attach the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-console'); ?>">Tools &rarr; Console</a></code> log file to the email.</li>
                <li>Download and attach the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-system-info'); ?>">Tools &rarr; System Info</a></code> report to the email.</li>
                <li>Include screenshots if they will help explain the problem.</li>
            </ul>
            <p><span class="required">Please be aware that if your email does not include the System Info report and the Console log (as requested above), it will most likely be ignored. Thank you for understanding.</span></p>
        </div>
    </div>
</div>
