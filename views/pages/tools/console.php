<?php defined('ABSPATH') || exit; ?>

<textarea id="glsr-log-file" class="large-text code glsr-code glsr-code-large" rows="20" readonly>{{ console }}</textarea>

<div style="display: flex; flex-wrap: wrap;justify-content: space-between;">
    <div style="display: flex; flex-wrap: wrap; margin-top: 4px;">
        <form method="post" style="margin-right:6px;">
            <?php wp_nonce_field('download-console'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="download-console">
            <button type="submit" id="glsr-download-console" class="button button-primary">
                <?php echo _x('Download Log', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
        <form method="post" style="margin-right:6px;">
            <?php wp_nonce_field('fetch-console'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="fetch-console">
            <button type="submit" id="glsr-fetch-console" class="glsr-button button"
                data-ajax-click
                data-console
                data-loading="<?php echo esc_attr_x('Reloading...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Reload', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
        <form method="post" style="margin-right:6px;">
            <?php wp_nonce_field('clear-console'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="clear-console">
            <button type="submit" id="glsr-clear-console" class="glsr-button button"
                data-ajax-click
                data-console
                data-loading="<?php echo esc_attr_x('Clearing...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Clear', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
    <div style="display: flex; flex-wrap: wrap; margin-top: 4px;">
        <form method="post">
            <?php wp_nonce_field('console-level'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="console-level">
            <select name="{{ id }}[level]" class="glsr-tooltip" style="margin-bottom: 4px; margin-left: 0;" data-tippy-allowhtml="1" data-tippy-content="
                <?php echo _x('Here you can change the <u>minimum</u> log level used by the Console. Site Reviews uses the Console to log various details and events throughout the plugin. The log level determines the importance of the logged entry: the higher the level, the more important the entry.', 'admin-text', 'site-reviews'); ?>
                <ul>
                    <li><?php echo _x('Level 0: Entries used for debugging.', 'admin-text', 'site-reviews'); ?></li>
                    <li><?php echo _x('Level 1: Informational entries.', 'admin-text', 'site-reviews'); ?></li>
                    <li><?php echo _x('Level 2: Deprecation notices.', 'admin-text', 'site-reviews'); ?></li>
                    <li><?php echo _x('Level 4: Warnings and errors.', 'admin-text', 'site-reviews'); ?></li>
                </ul>">
                <option value="0" <?php selected(0 === $console_level); ?>><?php echo _x('Level 0: Debugging', 'admin-text', 'site-reviews'); ?></option>
                <option value="1" <?php selected(1 === $console_level); ?>><?php echo _x('Level 1: Informational', 'admin-text', 'site-reviews'); ?></option>
                <option value="2" <?php selected(2 === $console_level); ?>><?php echo _x('Level 2: Notices', 'admin-text', 'site-reviews'); ?></option>
                <option value="4" <?php selected(4 === $console_level); ?>><?php echo _x('Level 4: Warnings', 'admin-text', 'site-reviews'); ?></option>
                <?php if (!in_array($console_level, [0, 1, 2, 4])) { ?>
                    <option value="-1" selected="selected"><?php echo _x('Unknown Level', 'admin-text', 'site-reviews'); ?></option>
                <?php } ?>
            </select>
            <button type="submit" id="glsr-console-level" class="glsr-button button"
                data-ajax-click
                data-loading="<?php echo esc_attr_x('Please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Apply', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
