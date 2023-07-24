<?php defined('ABSPATH') || exit; ?>

<tr class="form-field term-<?= $id; ?>-wrap">
    <th scope="row">
        <label for="<?= $id; ?>">
            <?= _x('Priority', 'admin-text', 'site-reviews'); ?>
        </label>
    </th>
    <td>
        <input name="<?= $id; ?>" id="<?= $id; ?>" type="number" value="<?= esc_attr($value); ?>" size="40" aria-describedby="<?= $id; ?>-description" />
        <p class="description" id="<?= $id; ?>-description">
            <?php printf(_x('Categories with a higher priority will be displayed first when using the %s template tag in the review.', '{{ assigned_terms }} (admin-text)', 'site-reviews'), '<code>{{ assigned_terms }}</code>'); ?>
        </p>
    </td>
</tr>
