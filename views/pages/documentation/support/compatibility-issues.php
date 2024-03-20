<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-compatibility-issues">
            <span class="title">Compatibility Issues</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-compatibility-issues" class="inside">
        <h3>Custom Content Shortcodes</h3>
        <p>The <a href="https://wordpress.org/plugins/custom-content-shortcode/" target="_blank">Custom Content Shortcodes</a> plugin provides a <code>[content]</code> shortcode which conflicts with the Site Reviews blocks. It also conflicts with the Site Reviews shortcodes when they are used with page builder plugins. For a more detailed explaination of this issue, please see this <a href="https://wordpress.org/support/topic/conflict-with-name-attributes-in-form-field/" target="_blank">support forum topic</a>.</p>
        <p>Here is a workaround to fix the problem:</p>
        <ol>
            <li>
                <p>Go to the <a href="<?php echo admin_url('options-general.php?page=ccs_reference&tab=settings'); ?>">Custom Content Shortcodes settings</a> page.</p>
            </li>
            <li>
                <p>Disable the <code>[raw] shortcode</code> setting.</p>
            </li>
            <li>
                <p>In the Advanced section, add <code>content</code> to the "Deactivate shortcodes" setting to disable the <code>[content]</code> shortcode.</p>
            </li>
        </ol>
    </div>
</div>
