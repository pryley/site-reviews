<?php defined('ABSPATH') || exit; ?>

<div class="wpcf7">
    <div class="glsr-form-wrap">
        <form class="{{ class }}" method="post" enctype="multipart/form-data">
            {{ fields }}
            {{ submit_button }}
            {{ response }}
        </form>
    </div>
</div>
