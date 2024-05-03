<?php defined('ABSPATH') || exit; ?>

<div class="glsr-form-wrap nf-form-wrap ninja-forms-form-wrap">
    <div class="nf-form-layout">
        <form class="{{ class }}" method="post" enctype="multipart/form-data">
            <div class="nf-form-content">
                {{ fields }}
                {{ response }}
                {{ submit_button }}
            </div>
        </form>
    </div>
</div>
