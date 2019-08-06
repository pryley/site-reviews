<?php defined('WPINC') || die; ?>

<span class="glsr-assigned-post">
    <button type="button" class="glsr-remove-button">
        <span class="glsr-remove-icon" aria-hidden="true"></span>
        <span class="screen-reader-text"><?= __('Remove assignment', 'site-reviews'); ?></span>
    </button>
    <a href="{{ data.url }}">{{ data.title }}</a>
</span>
