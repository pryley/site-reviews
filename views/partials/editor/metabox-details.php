<?php defined('WPINC') || die; ?>

<div class="glsr-metabox-field">
    <div class="glsr-label"><label>Edit Details</label></div>
    <div class="glsr-input wp-clearfix">
        <div class="glsr-toggle-field">
            <span class="glsr-toggle">
                <input name="<?= glsr()->id; ?>[is_editing_review]" class="glsr-toggle__input" type="checkbox">
                <span class="glsr-toggle__track"></span>
                <span class="glsr-toggle__thumb"></span>
                <svg width="6" height="6" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="glsr-toggle__off"><path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path></svg>
                <svg width="2" height="6" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="glsr-toggle__on"><path d="M0 0h2v6H0z"></path></svg>
            </span>
        </div>
    </div>
</div>
<?php foreach ($metabox as $field) : ?>
    <?= $field; ?>
<?php endforeach; ?>
