<?php defined('WPINC') || die; ?>

<div class="glsr-field {{ class }}">
    <div class="glsr-field-choice">
        <div class="glsr-checkbox-input">
            &#8203;{{ field }} <!-- A zero-width space character to assist with alignment -->
        </div>
        {{ label }}
    </div>
    {{ errors }}
</div>
