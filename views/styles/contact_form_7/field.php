<?php defined('ABSPATH') || exit; ?>

<p class="{{ class }}" data-field="{{ field_name }}">
    <label for="<?php echo esc_attr($field->id); ?>">
        <?php echo esc_html($field->label); ?><br>
        <span class="wpcf7-form-control-wrap">
            {{ field }}
            {{ errors }}
        </span>
    </label>
</p>
