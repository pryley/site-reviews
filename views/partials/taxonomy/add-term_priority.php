<?php defined('ABSPATH') || exit; ?>

<div class="form-field term-<?php echo esc_attr($id); ?>-wrap">
    <label for="<?php echo esc_attr($id); ?>">
        <?php echo _x('Priority', 'admin-text', 'site-reviews'); ?>
    </label>
    <input name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" type="number" value="0" placeholder="0" size="40" aria-describedby="<?php echo esc_attr($id); ?>-description" />
    <p class="description" id="<?php echo esc_attr($id); ?>-description">
        <?php printf(_x('Categories with a higher priority will be displayed first when using the %s template tag in the review.', '{{ assigned_terms }} (admin-text)', 'site-reviews'), '<code style="white-space:nowrap;">{{ assigned_terms }}</code>'); ?>
    </p>
</div>
