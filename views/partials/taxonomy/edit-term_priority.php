<?php defined('ABSPATH') || exit; ?>

<tr class="form-field term-<?= esc_attr($id); ?>-wrap">
    <th scope="row">
        <label for="<?= esc_attr($id); ?>">
            <?= _x('Priority', 'admin-text', 'site-reviews'); ?>
        </label>
    </th>
    <td>
        <input name="<?= esc_attr($id); ?>" id="<?= esc_attr($id); ?>" type="number" value="<?= esc_attr($value); ?>" size="40" aria-describedby="<?= esc_attr($id); ?>-description" />
        <p class="description" id="<?= esc_attr($id); ?>-description">
            <?php printf(_x('Categories with a higher priority will be displayed first when using the %s template tag in the review.', '{{ assigned_terms }} (admin-text)', 'site-reviews'), '<code>{{ assigned_terms }}</code>'); ?>
        </p>
    </td>
</tr>
