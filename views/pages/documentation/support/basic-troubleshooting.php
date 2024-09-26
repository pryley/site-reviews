<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-basic-troubleshooting">
            <span class="title">Basic Troubleshooting Steps</span>
            <span class="badge code important">Do this before asking for support!</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-basic-troubleshooting" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">If you find an incompatible theme or plugin, please <a data-expand="#support-contact-support" href="<?php echo glsr_admin_url('documentation', 'support'); ?>">contact support</a> so we can fix it.</p>
        </div>
        <h3>1. Make sure you are using the latest version of Site Reviews.</h3>
        <p>Site Reviews is updated frequently with bug patches, security updates, improvements, and new features. If you are experiencing problems, make sure you are using the latest version, as there is a good chance that the problem has already been fixed.</p>
        <h3>2. Deactivate Site Reviews and then reactivate it.</h3>
        <p>If you recently cloned your database or restored it from a backup, this should repair any broken database indexes.</p>
        <h3>3. Run the repair tools.</h3>
        <p>If you recently upgraded to a new version of Site Reviews and your reviews stopped working, try running the <a data-expand="#tools-migrate-plugin" href="<?php echo glsr_admin_url('tools', 'general'); ?>">Migrate Plugin (Hard Reset)</a> and <a data-expand="#tools-reset-assigned-meta" href="<?php echo glsr_admin_url('tools', 'general'); ?>">Reset Assigned Meta Values</a> tools. Plugin migrations should run automatically in the background when needed; however, sometimes, you may need to run these tools manually.</p>
        <h3>4. Temporarily switch to an official WordPress Theme.</h3>
        <p>Try switching to an official WordPress Theme (i.e. Twenty Twenty-Four) and then see if you are still experiencing problems with the plugin. If this fixes the problem, your theme has a compatibility issue.</p>
        <h3>5. Temporarily deactivate all of your plugins.</h3>
        <p>If switching to an official WordPress theme did not fix anything, the final thing to try is deactivating all of your plugins except for Site Reviews. If this fixes the problem, there is a compatibility issue with one of your plugins.</p>
        <p>To find out which plugin is incompatible with Site Reviews you will need to reactivate your plugins one by one until you find the plugin causing the problem. Then, if you think you’ve found the culprit, deactivate it and continue testing the rest of your plugins. Hopefully, you won’t find any more, but it’s good to make sure.</p>
    </div>
</div>

