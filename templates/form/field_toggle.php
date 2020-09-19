<?php defined('WPINC') || die; ?>

<div class="{{ class }}">
    <div class="glsr-toggle-switch">
        {{ label }}
        <div class="glsr-toggle-wrap">
            {{ field }} &#8203; <!-- zero-space character used for alignment -->
            <span class="glsr-toggle-track"></span>
        </div>
    </div>
    {{ errors }}
</div>
