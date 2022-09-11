<?php defined('ABSPATH') || exit; ?>

<div class="{{ class }} elementor-field-type-{{ field_type }} elementor-field-group elementor-column" data-field="{{ field_name }}">
    {{ label }}
    <div class="elementor-field elementor-select-wrapper">
        {{ field }}
    </div>
    {{ errors }}
</div>
