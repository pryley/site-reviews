<?php defined('ABSPATH') || die; ?>

<div class="glsr-metabox-field">
    <div class="glsr-label"><label>Edit Details</label></div>
    <div class="glsr-input wp-clearfix">
        <div class="glsr-toggle-field">
            <span class="glsr-toggle">
                <input name="<?= glsr()->id; ?>[is_editing_review]" class="glsr-toggle__input" type="checkbox" data-edit-review>
                <span class="glsr-toggle__track"></span>
                <span class="glsr-toggle__thumb"></span>
            </span>
        </div>
    </div>
</div>
<?php foreach ($metabox as $field) : ?>
    <?= $field; ?>
<?php endforeach; ?>
