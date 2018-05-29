<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>

<?php

	$html->renderNotices();
	$html->renderPartial( 'tabs' , [
		'page' => $page,
		'tab' => $currentTab,
		'tabs' => $tabs,
	]);
	$html->renderPartial( 'sections' , [
		'page' => $page,
		'section' => $currentSection,
		'tab' => $currentTab,
		'tabs' => $tabs,
	]);
	$file = $currentSection ? $currentTab.'/'.$currentSection : $currentTab;
	$file = trailingslashit( __DIR__ ).$page.'/'.$file.'.php';
	$file = apply_filters( 'site-reviews/addon/views/file', $file, $view, $data );

	if( file_exists( $file ) ) {
		include $file;
	}
	else {
		glsr_log()->error( 'File not found: '.$file );
	}

?>

</div>
