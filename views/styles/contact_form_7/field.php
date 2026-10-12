<?php defined('ABSPATH') || exit; ?>

<p class="{{ class }}" data-field="{{ field_name }}">
    <label for="{{ field_id }}">
        {{ field_label }}<br>
        <span class="wpcf7-form-control-wrap">
            {{ field }}
            {{ errors }}
        </span>
    </label>
</p>
