<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>
	<div class="notice notice-warning is-dismissible">
		<p><?= __( 'Now that Site Reviews v3.0 (a major overhaul of the plugin) has been released, the only thing delaying the add-ons is the distribution platform which is currently being built. Expect to see them in the coming months!', 'site-reviews' ); ?></p>
		<p><?= __( 'Thank you for your patience.', 'site-reviews' ); ?></p>
	</div>
	<p><?= __( 'Add-ons extend the functionality of Site Reviews.', 'site-reviews' ); ?></p>
	<div class="glsr-addons wp-clearfix">
	<?php
		$template->render( 'partials/addons/addon', [
			'context' => [
				'description' => __( 'Allow your visitors to submit multiple images with their reviews.', 'site-reviews' ),
				'link' => 'https://niftyplugins.com/addons/site-reviews-images/',
				'slug' => 'images',
				'title' => 'Images',
			],
			'plugin' => 'site-reviews-images/site-reviews-images.php',
		]);
		$template->render( 'partials/addons/addon', [
			'context' => [
				'description' => __( 'Sync your Tripadvisor reviews to your website and manage them with Site Reviews.', 'site-reviews' ),
				'link' => 'https://niftyplugins.com/addons/site-reviews-tripadvisor/',
				'slug' => 'tripadvisor',
				'title' => 'Tripadvisor Reviews',
			],
			'plugin' => 'site-reviews-tripadvisor/site-reviews-tripadvisor.php',
		]);
	?>
	</div>
</div>
