<?php defined('ABSPATH') || exit; ?>

<div id="misc-pub-pinned" class="misc-pub-section misc-pub-pinned">
    <label for="pinned-status">
        <?php echo esc_html_x('Pinned', 'admin-text', 'site-reviews'); ?>:
    </label>
    <span id="pinned-status-text" class="pinned-status-text">
        <?php echo $is_pinned ? esc_html_x('Yes', 'admin-text', 'site-reviews') : esc_html_x('No', 'admin-text', 'site-reviews'); ?>
    </span>
    <a href="#pinned-status" class="edit-pinned-status hide-if-no-js">
        <span aria-hidden="true"><?php echo esc_html_x('Edit', 'admin-text', 'site-reviews'); ?></span>
        <span class="screen-reader-text"><?php echo esc_html_x('Edit pinned status', 'admin-text', 'site-reviews'); ?></span>
    </a>
    <div id="pinned-status-select" class="pinned-status-select hide-if-js">
        <input type="hidden" name="<?php echo glsr()->id; ?>[is_pinned]" id="hidden-pinned-status" value="<?php echo intval($is_pinned); ?>" />
        <select id="pinned-status">
            <option value="1"<?php selected($is_pinned, false); ?>><?php echo esc_html_x('Pin review', 'admin-text', 'site-reviews'); ?></option>
            <option value="0"<?php selected($is_pinned, true); ?>><?php echo esc_html_x('Unpin review', 'admin-text', 'site-reviews'); ?></option>
        </select>
        <a href="#pinned-status" class="save-pinned-status hide-if-no-js button"
            data-no="<?php echo esc_attr_x('No', 'admin-text', 'site-reviews'); ?>"
            data-yes="<?php echo esc_attr_x('Yes', 'admin-text', 'site-reviews'); ?>"
        >
            <?php echo esc_html_x('OK', 'admin-text', 'site-reviews'); ?>
        </a>
        <a href="#pinned-status" class="cancel-pinned-status hide-if-no-js button-cancel">
            <?php echo esc_html_x('Cancel', 'admin-text', 'site-reviews'); ?>
        </a>
    </div>
</div>
