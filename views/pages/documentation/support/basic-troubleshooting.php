<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-basic-troubleshooting">
            <span class="title">Basic Troubleshooting Steps</span>
            <span class="badge code important">Do this first</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-basic-troubleshooting" class="inside">
        <h3>Make sure you are using the latest version of Site Reviews.</h3>
        <p>Site Reviews is updated frequently with bug patches, security updates, improvements, and new features. If you are not using the latest version and are experiencing problems, chances are good that your problem has already been addressed in the latest version.</p>
        <h3>Temporarily switch to an official WordPress Theme.</h3>
        <p>Try switching to an official WordPress Theme (i.e. Twenty Seventeen) and then see if you are still experiencing problems with the plugin. If this fixes the problem then there is a compatibility issue with your theme.</p>
        <h3>Temporarily deactivate all of your plugins.</h3>
        <p>If switching to an official WordPress theme did not fix anything, the final thing to try is to deactivate all of your plugins except for Site Reviews. If this fixes the problem then there is a compatibility issue with one of your plugins.</p>
        <p>To find out which plugin is incompatible with Site Reviews you will need to reactivate your plugins one-by-one until you find the plugin that is causing the problem. If you think that you’ve found the culprit, deactivate it and continue to test the rest of your plugins. Hopefully you won’t find any more but it’s always better to make sure.</p>
        <p>If you find an incompatible theme or plugin, please <code><a data-expand="#support-contact-support" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-support'); ?>">contact support</a></code> so we can fix it.</p>
    </div>
</div>
