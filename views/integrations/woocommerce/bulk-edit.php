<?php defined('WPINC') || exit; ?>

<fieldset class="inline-edit-col-right">
    <div class="inline-edit-group wp-clearfix">
        <label class="alignleft">
            <span class="title"><?php echo esc_html_x('Reviews', 'admin-text', 'site-reviews'); ?></span>
            <select name="comment_status">
                <option value="">&mdash; <?php echo esc_html_x('No Change', 'admin-text', 'site-reviews'); ?> &mdash;</option>
                <option value="open"><?php echo esc_html_x('Allow', 'admin-text', 'site-reviews'); ?></option>
                <option value="closed"><?php echo esc_html_x('Do not allow', 'admin-text', 'site-reviews'); ?></option>
            </select>
        </label>
    </div>
</fieldset>
