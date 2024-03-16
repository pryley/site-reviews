<?php defined('ABSPATH') || exit; ?>

<div class="{{ class }}">
    <div class="glsr-label">{{ label }}</div>
    <div class="glsr-input wp-clearfix">
        {{ field }}
        <?php
            if ('avatar' === $field->original_name) {
                echo $review->avatar(64);
            }
        ?>
    </div>
</div>
