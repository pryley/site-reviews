<?php defined('ABSPATH') || exit; ?>

<tr>
    <th scope="row">
        <?php echo esc_html_x('Synchronize Categories', 'admin-text', 'site-reviews'); ?>
    </th>
    <td class="mlp-taxonomy-sync">
        <label for="<?php echo esc_attr($id); ?>">
            <input
                type="checkbox"
                name="<?php echo esc_attr($name); ?>"
                value="1"
                id="<?php echo esc_attr($id); ?>"
                <?php checked($checked); ?>
            />
            <?php echo esc_html_x(
                'Overwrite the Assigned Categories of the target review with the Assigned Categories of the source review.',
                'admin-text',
                'site-reviews'
            ); ?>
        </label>
    </td>
</tr>
