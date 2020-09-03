<div id="support-contact-support" class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title">Contact Support</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div class="inside">
        <p><strong>The fastest way to get help</strong> is to post a support request in the <a href="https://wordpress.org/support/plugin/site-reviews/">WordPress Support Forum</a>. Using the support forum will also allow existing and future users of the plugin to benefit from the solution.</p>
        <p>However, you may also contact us directly (expect a slower response time) after confirming the following:</p>
        <p class="glsr-card-field">
            <input type="checkbox" id="step-1" class="glsr-support-step">
            <label for="step-1">I have read the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-support'); ?>" data-expand="#support-common-problems-and-solutions">Common Problems and Solutions</a></code> and it does not answer my question.</label>
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
            <label for="step-5">I have completed the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-support'); ?>" data-expand="#support-basic-troubleshooting">Basic Troubleshooting Steps</a></code> provided above.</label>
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
