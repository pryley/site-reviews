<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>
	<h2 class="nav-tab-wrapper">
	<?php foreach( $tabs as $id => $title ) : ?>
		<a class="glsr-nav-tab nav-tab" href="#<?= $id; ?>"><?= $title; ?></a>
	<?php endforeach; ?>
	</h2>
	<?php foreach( $tabs as $id => $title ) : ?>
	<div class="glsr-nav-view" id="<?= $id; ?>">
		<?php glsr()->render( 'pages/documentation/'.$id ); ?>
	</div>
	<?php endforeach; ?>
	<input type="hidden" name="_wp_http_referer" value="<?= wp_get_referer(); ?>">
	<input type="hidden" name="_active_tab">
</div>
