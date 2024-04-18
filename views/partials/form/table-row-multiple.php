<?php defined('ABSPATH') || exit; ?>

<tr class="{{ class }}" data-field="<?php echo esc_js($field->original_name); ?>">
    <th scope="row">{{ label }}</th>
    <td>
        <fieldset data-depends="{{ depends_on }}">
            <legend class="screen-reader-text"><span>{{ legend }}</span></legend>
            {{ field }}
        </fieldset>
    </td>
</tr>
