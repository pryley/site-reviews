<?php defined('WPINC') || die; ?>

<div class="glsr-field nf-field-container label-above {{ class }}">
    <div class="nf-field">
        <div class="field-wrap">
            <div class="nf-field-label">
                {{ label }}
            </div>
            <div class="nf-field-element">
                {{ field }}
            </div>
        </div>
    </div>
    <div class="nf-after-field">
        {{ errors }}
    </div>
</div>
