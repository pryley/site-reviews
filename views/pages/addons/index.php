<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>
	<p><?= __( 'The following Add-Ons extend the functionality of Site Reviews.', 'site-reviews' ); ?></p>
	<div class="glsr-addons wp-clearfix">
	<?php
		$template->render( 'partials/addons/addon', [
			'context' => [
				'description' => __( 'Add images to your review submissions.', 'site-reviews' ),
				'link' => 'https://niftyplugins.com/addons/site-reviews-images/',
				'slug' => 'images',
				'title' => 'Images',
			],
			'plugin' => 'site-reviews-images/site-reviews-images.php',
		]);
		$template->render( 'partials/addons/addon', [
			'context' => [
				'description' => __( 'Sync your Tripadvisor reviews and display them on your site.', 'site-reviews' ),
				'link' => 'https://niftyplugins.com/addons/site-reviews-tripadvisor/',
				'slug' => 'tripadvisor',
				'title' => 'Tripadvisor Reviews',
			],
			'plugin' => 'site-reviews-tripadvisor/site-reviews-tripadvisor.php',
		]);
	?>
	</div>
</div>
