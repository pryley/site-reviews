<?php defined('ABSPATH') || exit; ?>

<div id="misc-pub-verified" class="misc-pub-section misc-pub-verified">
    <label for="verified-status">
        <?php echo esc_html_x('Verified', 'admin-text', 'site-reviews'); ?>:
    </label>
    <span id="verified-status-text" class="verified-status-text">
        <?php echo $is_verified ? esc_html_x('Yes', 'admin-text', 'site-reviews') : esc_html_x('No', 'admin-text', 'site-reviews'); ?>
    </span>
    <a href="#verified-status" class="edit-verified-status hide-if-no-js">
        <span aria-hidden="true"><?php echo esc_html_x('Edit', 'admin-text', 'site-reviews'); ?></span>
        <span class="screen-reader-text"><?php echo esc_html_x('Edit verified status', 'admin-text', 'site-reviews'); ?></span>
    </a>
    <div id="verified-status-select" class="verified-status-select hide-if-js">
        <input type="hidden" name="<?php echo glsr()->id; ?>[is_verified]" id="hidden-verified-status" value="<?php echo intval($is_verified); ?>" />
        <select id="verified-status">
            <option value="1"<?php selected($is_verified, false); ?>><?php echo esc_html_x('Verify review', 'admin-text', 'site-reviews'); ?></option>
            <option value="0"<?php selected($is_verified, true); ?>><?php echo esc_html_x('Unverify review', 'admin-text', 'site-reviews'); ?></option>
        </select>
        <a href="#verified-status" class="save-verified-status hide-if-no-js button"
            data-no="<?php echo esc_attr_x('No', 'admin-text', 'site-reviews'); ?>"
            data-yes="<?php echo esc_attr_x('Yes', 'admin-text', 'site-reviews'); ?>"
        >
            <?php echo esc_html_x('OK', 'admin-text', 'site-reviews'); ?>
        </a>
        <a href="#verified-status" class="cancel-verified-status hide-if-no-js button-cancel">
            <?php echo esc_html_x('Cancel', 'admin-text', 'site-reviews'); ?>
        </a>
    </div>
</div>
