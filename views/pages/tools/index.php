<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>
	<?php $html->renderNotices(); ?>
	<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $id => $title ) : ?>
		<a class="glsr-nav-tab nav-tab" href="#<?= $id; ?>"><?= $title; ?></a>
		<?php endforeach; ?>
	</h2>
	<?php foreach( $tabs as $id => $title ) : ?>
	<div class="glsr-nav-view ui-tabs-hide" id="<?= $id; ?>">
		<?php $html->renderTemplate( 'pages/tools/'.$id, $data ); ?>
	</div>
	<?php endforeach; ?>
	<input type="hidden" name="_active_tab">
	<input type="hidden" name="_wp_http_referer" value="<?= $http_referer; ?>">
</div>
