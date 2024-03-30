<?php defined('ABSPATH') || exit; ?>

<div id="misc-pub-verified" class="misc-pub-section" data-action="toggle-verified">
    <?php echo esc_html_x('Verified', 'admin-text', 'site-reviews'); ?>:
    <span id="verified-status-text" class="misc-pub-text">
        <?php echo $is_verified ? esc_html_x('Yes', 'admin-text', 'site-reviews') : esc_html_x('No', 'admin-text', 'site-reviews'); ?>
    </span>
    <a href="#verified-status" data-click="edit" class="hide-if-no-js edit-verified-status" role="button">
        <span aria-hidden="true"><?php echo esc_html_x('Edit', 'admin-text', 'site-reviews'); ?></span>
        <span class="screen-reader-text"><?php echo esc_html_x('Edit verified status', 'admin-text', 'site-reviews'); ?></span>
    </a>
    <div id="verified-status-select" class="misc-pub-select hide-if-js">
        <input type="hidden" name="<?php echo glsr()->id; ?>[is_verified]" value="<?php echo intval($is_verified); ?>" />
        <label for="verified-status" class="screen-reader-text">
            <?php echo esc_attr_x('Set verified status', 'admin-text', 'site-reviews'); ?>
        </label>
        <select id="verified-status">
            <option value="1" data-alt="<?php echo esc_attr_x('Yes', 'admin-text', 'site-reviews'); ?>"<?php selected($is_verified, true); ?>>
                <?php echo esc_html_x('Verify review', 'admin-text', 'site-reviews'); ?>
            </option>
            <option value="0" data-alt="<?php echo esc_attr_x('No', 'admin-text', 'site-reviews'); ?>"<?php selected($is_verified, false); ?>>
                <?php echo esc_html_x('Unverify review', 'admin-text', 'site-reviews'); ?>
            </option>
        </select>
        <a href="#verified-status" data-click="save" class="button hide-if-no-js" role="button">
            <?php echo esc_html_x('OK', 'admin-text', 'site-reviews'); ?>
        </a>
        <a href="#verified-status" data-click="cancel" class="button-cancel hide-if-no-js" role="button">
            <?php echo esc_html_x('Cancel', 'admin-text', 'site-reviews'); ?>
        </a>
    </div>
</div>
