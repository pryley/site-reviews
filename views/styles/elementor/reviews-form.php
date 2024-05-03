<?php defined('ABSPATH') || exit; ?>

<div class="glsr-form-wrap">
    <form class="{{ class }}" method="post" enctype="multipart/form-data">
        <div class="elementor-form-fields-wrapper elementor-labels-above">
            {{ fields }}
            {{ submit_button }}
        </div>
        {{ response }}
    </form>
</div>
