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

        <h2>Changes to IP Address detection</h2>
        <p><em>Likelihood Of Impact: <span class="impact-high">High</span></em></p>
        <ol>
            <li>
                <p><strong>Site Reviews no longer looks for proxy HTTP headers when detecting the IP address.</strong></p>
                <p>Proxy HTTP headers are sometimes used by visitors to spoof their IP address so Site Reviews now disables this by default.</p>
                <p>If you get incorrect IP address values in your reviews, there might be a reverse proxy, load balancer, cache, Cloudflare, CDN, or any other type of proxy in front of your web server that "proxies" traffic to your website.</p>
                <p>Please use the <a data-expand="#tools-ip-detection" href="<?php echo glsr_admin_url('tools', 'general'); ?>">Configure IP Address Detection</a> tool to test IP Address detection on your website and if needed, select the correct proxy HTTP header used by your server that contains the real visitor IP address.</p>
                <p>
            </li>
        </ol>

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

        <h2>Action and Filter Hook changes</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The <code>site-reviews/builder/&lt;field_type&gt;</code> filter hook has been removed.</strong></p>
                <p>If you were previously using this hook to change the PHP class used for the field element, you should change it to: <code>site-reviews/field/element/&lt;field_element_type&gt;</code>.</p>
            </li>
        </ol>

    </div>
</div>
