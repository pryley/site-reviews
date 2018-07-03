<?php defined( 'WPINC' ) || die; ?>

<?php foreach( $settings as $key => $rows ) : ?>
<div class="glsr-nav-view-section" id="<?= $key; ?>">
	<?php do_action( 'site-reviews/addon/settings/'.$key, $rows ); ?>
</div>
<?php endforeach; ?>
