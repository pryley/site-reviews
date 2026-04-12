<?php defined('ABSPATH') || exit; ?>

<div class="{{ class }}" data-field="{{ field_name }}" data-type="{{ field_type }}">
    <div class="glsr-label">{{ label }}</div>
    <div class="glsr-input">
        {{ field }}
        <?php
            if ('avatar' === $field->original_name) {
                echo $review->avatar(64);
            }
        ?>
    </div>
</div>
