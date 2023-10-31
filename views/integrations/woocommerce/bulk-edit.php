<?php defined('WPINC') || die; ?>

<fieldset class="inline-edit-col-right">
    <div class="inline-edit-group wp-clearfix">
        <label class="alignleft">
            <span class="title"><?php _ex('Reviews', 'admin-text', 'site-reviews'); ?></span>
            <select name="comment_status">
                <option value=""><?php _e('&mdash; No Change &mdash;'); ?></option>
                <option value="open"><?php _e('Allow'); ?></option>
                <option value="closed"><?php _e('Do not allow'); ?></option>
            </select>
        </label>
    </div>
</fieldset>
