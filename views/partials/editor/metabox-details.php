<?php defined('WPINC') || die; ?>

<table class="glsr-metabox-table">
    <tbody>
    <?php foreach ($metabox as $key => $value) : ?>
        <tr>
            <td><?= $key; ?></td>
            <td><?= $value; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="glsr-metabox-actions clearfix">
    <div class="glsr-revert-action">
        <span class="spinner"></span>
        <?= $button; ?>
    </div>
    <div class="clear"></div>
</div>
