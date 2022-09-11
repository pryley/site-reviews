<?php defined('ABSPATH') || exit; ?>

<span class="elementor-field-option">
    <span class="{{ class }}">
        <span class="glsr-toggle">
            <label for="{{ id }}">{{ text }}</label>
            <span class="glsr-toggle-switch">
                {{ input }} &#8203; <!-- zero-space character for alignment -->
                <span class="glsr-toggle-track"></span>
            </span>
        </span>
    </span>
</span>
