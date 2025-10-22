<?php defined('ABSPATH') || exit; ?>

<tr class="{{ class }}" data-field="<?php echo esc_attr($field->original_name); ?>">
    <th scope="row">{{ label }}</th>
    <td>
        {{ field }}
    </td>
</tr>
