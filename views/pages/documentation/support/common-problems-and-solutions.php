<div id="support-common-problems-and-solutions" class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title">Common Problems and Solutions</span>
            <span class="icon"></span>
        </button>
    </h3>
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
