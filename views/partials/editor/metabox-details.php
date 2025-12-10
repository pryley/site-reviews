<?php defined('ABSPATH') || exit; ?>

<div class="glsr-metabox-fields">
    <div class="glsr-field-toggle glsr-metabox-field" data-toggle="edit-details">
        <div class="glsr-label">
            <label><?php echo esc_html_x('Edit Details', 'admin-text', 'site-reviews'); ?></label>
        </div>
        <div class="glsr-input">
            <span class="glsr-toggle">
                <input type="checkbox" class="glsr-toggle__input" 
                    data-edit-review
                    name="<?php echo glsr()->id; ?>[is_editing_review]"
                    <?php checked(glsr_current_screen()->action, 'add'); ?>
                />
                <span class="glsr-toggle__track"></span>
                <span class="glsr-toggle__thumb"></span>
            </span>
        </div>
    </div>
    <?php echo $fields; ?>
</div>
