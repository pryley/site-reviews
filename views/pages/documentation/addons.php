<?php defined('ABSPATH') || exit; ?>

<?php foreach ($addons as $id => $section) { ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title"><?php echo glsr($id)->name; ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="addon-<?php echo esc_attr($id); ?>" class="inside">
        <?php echo $section; ?>
    </div>
</div>
<?php } ?>
