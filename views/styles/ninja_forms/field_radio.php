<?php defined('ABSPATH') || exit; ?>

<div class="nf-field-container listradio-container label-above list-container" data-field="{{ field_name }}">
    <div class="nf-field">
        <div class="field-wrap listradio-wrap list-wrap list-radio-wrap {{ class }}">
            <div class="nf-field-label">
                {{ label }}
            </div>
            <div class="nf-field-element">
                <ul>
                    {{ field }}
                </ul>
            </div>
            {{ errors }}
        </div>
    </div>
</div>
