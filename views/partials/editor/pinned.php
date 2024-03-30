<?php defined('ABSPATH') || exit; ?>

<div id="misc-pub-pinned" class="misc-pub-section" data-action="toggle-pinned">
    <?php echo esc_html_x('Pinned', 'admin-text', 'site-reviews'); ?>:
    <span id="pinned-status-text" class="misc-pub-text">
        <?php echo $is_pinned ? esc_html_x('Yes', 'admin-text', 'site-reviews') : esc_html_x('No', 'admin-text', 'site-reviews'); ?>
    </span>
    <a href="#pinned-status" data-click="edit" class="hide-if-no-js edit-pinned-status" role="button">
        <span aria-hidden="true"><?php echo esc_html_x('Edit', 'admin-text', 'site-reviews'); ?></span>
        <span class="screen-reader-text"><?php echo esc_html_x('Edit pinned status', 'admin-text', 'site-reviews'); ?></span>
    </a>
    <div id="pinned-status-select" class="misc-pub-select hide-if-js">
        <input type="hidden" name="<?php echo glsr()->id; ?>[is_pinned]" value="<?php echo intval($is_pinned); ?>" />
        <label for="pinned-status" class="screen-reader-text">
            <?php echo esc_attr_x('Set pinned status', 'admin-text', 'site-reviews'); ?>
        </label>
        <select id="pinned-status">
            <option value="1" data-alt="<?php echo esc_attr_x('Yes', 'admin-text', 'site-reviews'); ?>"<?php selected($is_pinned, true); ?>>
                <?php echo esc_html_x('Pin review', 'admin-text', 'site-reviews'); ?>
            </option>
            <option value="0" data-alt="<?php echo esc_attr_x('No', 'admin-text', 'site-reviews'); ?>"<?php selected($is_pinned, false); ?>>
                <?php echo esc_html_x('Unpin review', 'admin-text', 'site-reviews'); ?>
            </option>
        </select>
        <a href="#pinned-status" data-click="save" class="button hide-if-no-js" role="button">
            <?php echo esc_html_x('OK', 'admin-text', 'site-reviews'); ?>
        </a>
        <a href="#pinned-status" data-click="cancel" class="button-cancel hide-if-no-js" role="button">
            <?php echo esc_html_x('Cancel', 'admin-text', 'site-reviews'); ?>
        </a>
    </div>
</div>
