<?php defined('ABSPATH') || die; ?>

<div class="wp-block-button">
    <button type="button" class="wp-block-button__link glsr-button-loadmore glsr-button button btn btn-primary" data-loading="{{ loading_text }}" data-page="{{ page }}" aria-busy="false" aria-label="{{ screen_reader_text }}">
        <span class="glsr-button-loading"></span>
        <span class="glsr-button-text">{{ text }}</span>
    </button>
</div>
