<?php defined('ABSPATH') || exit; ?>

<p class="{{ class }}" data-field="{{ field_name }}" data-type="{{ field_type }}">
    <span class="et_pb_contact_field_options_wrapper">
        <span class="et_pb_contact_field_options_title">
            {{ label }}
        </span>
        <span class="et_pb_contact_field_options_list">
            {{ field }}
        </span>
        {{ errors }}
    </span>
</p>
