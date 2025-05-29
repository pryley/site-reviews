<?php defined('ABSPATH') || exit; ?>

<div class="{{ class }} breakdance-form-field--{{ field_type }}" data-field="{{ field_name }}">
    <fieldset role="group" aria-label="{{ field_label }}">
        <legend class="breakdance-form-field__label">{{ field_label }}</legend>
        <div class="glsr-field-subgroup">
            {{ field }}
        </div>
        {{ errors }}
    </fieldset>
</div>
