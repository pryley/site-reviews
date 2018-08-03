<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>
	<?= $notices; ?>
	<h2 class="glsr-nav-tab-wrapper nav-tab-wrapper">
		<?php foreach( $tabs as $id => $title ) : ?>
		<a class="glsr-nav-tab nav-tab" href="#<?= $id; ?>"><?= $title; ?></a>
		<?php endforeach; ?>
	</h2>
	<form class="glsr-form" action="options.php" enctype="multipart/form-data" method="post">
		<?php foreach( $tabs as $id => $title ) : ?>
		<div class="glsr-nav-view ui-tabs-hide" id="<?= $id; ?>">
			<?= $settings->buildFields( $id ); ?>
		</div>
		<?php endforeach; ?>
		<input type="hidden" name="_active_tab">
		<?php settings_fields( glsr()->id.'-settings' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
