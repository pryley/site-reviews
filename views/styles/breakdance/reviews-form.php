<?php defined('ABSPATH') || exit; ?>

<div class="glsr-form-wrap bde-form-builder">
    <form class="{{ class }}" method="post" enctype="multipart/form-data">
        {{ fields }}
        {{ submit_button }}
        {{ response }}
    </form>
</div>
