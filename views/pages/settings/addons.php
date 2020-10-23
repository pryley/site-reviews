<?php defined('ABSPATH') || die; ?>

<?php foreach ($settings as $key => $rows) : ?>
<div class="glsr-nav-view-section" id="<?= $key; ?>" style="margin-top:40px;">
    <?php glsr()->action('addon/settings/'.$key, $rows); ?>
</div>
<?php endforeach; ?>
