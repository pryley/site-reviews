<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="upgrade-v7_0_0">
            <span class="title">Version 7.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v7_0_0" class="inside">

        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                Site Reviews should automatically migrate itself after updating to v7.0. However, if you are experiencing problems after updating, you may need to manually run the <a href="<?php echo glsr_admin_url('tools', 'general'); ?>" data-expand="#tools-migrate-plugin">Migrate Plugin</a> tool.
            </p>
        </div>

        <h2>Changes to CSS Variables</h2>
        <p><em>Likelihood Of Impact: <span class="impact-medium">Medium</span></em></p>
        <ol>
            <li>
                <p><strong>All CSS variables have been moved from <code>:root {}</code> to <code>body {}</code>. </strong></p>
                <p>This <a href="https://specificity.keegan.st/" target="_blank">specificity</a> change allows Site Reviews to inherit CSS variables of some WordPress themes. If you are overriding any of the Site Reviews CSS variables in your custom CSS inside <code>:root {}</code>, please override them inside <code>body {}</code> instead.</p>
            </li>
        </ol>

        <h2>Changes to Plugin Templates</h2>
        <p><em>Likelihood Of Impact: <span class="impact-medium">Medium</span></em></p>
        <ol>
            <li>
                <p><strong>The HTML markup of the <code>form/submit-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>load-more-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
        </ol>

    </div>
</div>
