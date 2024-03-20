<?php defined('ABSPATH') || exit; ?>

<fieldset>
    <div class="inline-edit-col">
        <label class="inline-edit-group" style="margin-top: 0;">
            <span class="title"><?php echo _x('Priority', 'admin-text', 'site-reviews'); ?></span>
            <span class="input-text-wrap">
                <input type="number" name="<?php echo esc_attr($id); ?>" class="ptitle" value="" />
            </span>
        </label>
    </div>
</fieldset>
