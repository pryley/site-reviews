<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-contact-support">
            <span class="title">Contact Support</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-contact-support" class="inside">
        <h3>Use the Official Support Forum</h3>
        <p><strong>The fastest way to get help</strong> is to post a support request on the WordPress Support Forum. The chances are good that your question has already been answered there.</p>
        <p><a class="components-button is-primary" target="_blank" href="https://wordpress.org/support/plugin/site-reviews">Visit the Support Forum &rarr;</a></p>

        <h3>The Last Resort</h3>
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content"><strong>Expect a slow response time</strong> unless we have specifically asked you on the support forum to contact us directly.</p>
        </div>
        <p>Please confirm the following questions before proceeding:</p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-1" class="glsr-support-step">
            <label for="step-1">I have a good reason for not using the <a target="_blank" href="https://wordpress.org/support/plugin/site-reviews">WordPress Support Forum</a> to request assistance.</label>
        </p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-2" class="glsr-support-step">
            <label for="step-2">I have read the <code><a href="<?= glsr_admin_url('documentation', 'shortcodes'); ?>">Shortcodes</a></code> help page and it does not answer my question.</label>
        </p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-3" class="glsr-support-step">
            <label for="step-3">I have read the <code><a data-expand="#support-common-problems-and-solutions" href="<?= glsr_admin_url('documentation', 'support'); ?>">Common Problems and Solutions</a></code> section provided above and it does not answer my question.</label>
        </p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-4" class="glsr-support-step">
            <label for="step-4">I have read the <code><a href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a></code> help page and it does not answer my question.</label>
        </p>
        <?php if (glsr()->hasPermission('documentation', 'hooks')) : ?>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-5" class="glsr-support-step">
            <label for="step-5">I have read the <code><a href="<?= glsr_admin_url('documentation', 'hooks'); ?>">Hooks</a></code> help page and it does not answer my question.</label>
        </p>
        <?php endif; ?>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-6" class="glsr-support-step">
            <label for="step-6">I have completed each step in the <code><a data-expand="#support-basic-troubleshooting" href="<?= glsr_admin_url('documentation', 'support'); ?>">Basic Troubleshooting Steps</a></code> section provided above.</label>
        </p>
        <div class="glsr-card-result hidden">
            <p><strong>Please send an email to <a href="mailto:site-reviews@geminilabs.io?subject=Support%20request">site-reviews@geminilabs.io</a> and include the following details:</strong></p>
            <ul>
                <li>A detailed description of the problem you are having and the steps to reproduce it.</li>
                <li>Download and attach the Site Reviews <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> log file to the email.</li>
                <li>Download and attach the Site Reviews <code><a href="<?= glsr_admin_url('tools', 'system-info'); ?>">Tools &rarr; System Info</a></code> report to the email.</li>
                <li>Please also include screenshots if they will help explain the problem.</li>
            </ul>
            <div class="glsr-notice-inline components-notice is-error">
                <p class="components-notice__content">If your email does not include the <a href="<?= glsr_admin_url('tools', 'console'); ?>">Console</a> and <a href="<?= glsr_admin_url('tools', 'system-info'); ?>">System Info</a> files, the response may be delayed or even ignored.</p>
            </div>
        </div>
    </div>
</div>
