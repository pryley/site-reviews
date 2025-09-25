<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-contact-support">
            <span class="title">Contact Support</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-contact-support" class="inside">
        <h3>Request help on the WordPress Support Forum</h3>
        <p>The official support channel for Site Reviews is the WordPress support forum. Most topics posted in the support forum are resolved within a day or two.</p>
        <p><strong>Before asking for help:</strong></p>
        <ol>
            <li><?php echo glsr_admin_link('welcome', 'Read the Getting Started Guide', '#support-get-started'); ?> if you are unsure how to use Site Reviews.</li>
            <li><?php echo glsr_admin_link('documentation.support', 'Read the Common Problems and Solutions', '#support-common-problems-and-solutions'); ?> if you are having problems sending emails or submitting reviews.</li>
            <li><?php echo glsr_admin_link('documentation.support', 'Try the Basic Troubleshooting Steps', '#support-basic-troubleshooting'); ?> if you are unsure how to use Site Reviews.</li>
        </ol>
        <p><a class="components-button is-primary" target="_blank" href="https://wordpress.org/support/plugin/site-reviews">Visit the Support Forum &rarr;</a></p>
    </div>
</div>
