<?php defined('ABSPATH') || exit; ?>

<div class="{{ class }} breakdance-form-field--{{ field_type }}" data-field="{{ field_name }}" data-type="{{ field_type }}">
    {{ label }}
    <div class="glsr-field-subgroup">
        {{ field }}
    </div>
    {{ errors }}
</div>
