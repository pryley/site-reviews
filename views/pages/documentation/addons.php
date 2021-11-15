<?php defined('ABSPATH') || die; ?>

<?php foreach ($addons as $id => $section) : ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title"><?= glsr($id)->name; ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="addon-<?= $id; ?>" class="inside">
        <?= $section; ?>
    </div>
</div>
<?php endforeach; ?>
