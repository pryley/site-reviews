<?php defined('ABSPATH') || exit; ?>

<div class="form-field term-<?= esc_attr($id); ?>-wrap">
    <label for="<?= esc_attr($id); ?>">
        <?= _x('Priority', 'admin-text', 'site-reviews'); ?>
    </label>
    <input name="<?= esc_attr($id); ?>" id="<?= esc_attr($id); ?>" type="number" value="0" placeholder="0" size="40" aria-describedby="<?= esc_attr($id); ?>-description" />
    <p class="description" id="<?= esc_attr($id); ?>-description">
        <?php printf(_x('Categories with a higher priority will be displayed first when using the %s template tag in the review.', '{{ assigned_terms }} (admin-text)', 'site-reviews'), '<code>{{ assigned_terms }}</code>'); ?>
    </p>
</div>
