<?php defined('ABSPATH') || exit; ?>

<div class="nf-field-container label-above list-container listselect-container {{ class }}" data-field="{{ field_name }}">
    <div class="nf-field">
        <div class="field-wrap listselect-wrap list-wrap list-select-wrap">
            <div class="nf-field-label">
                {{ label }}
            </div>
            <div class="nf-field-element">
                {{ field }}
                <div></div>
            </div>
            {{ errors }}
        </div>
    </div>
</div>
