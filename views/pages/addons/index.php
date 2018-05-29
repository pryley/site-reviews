<?php defined( 'WPINC' ) || die; ?>

<div class="wrap">
	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>
	<p><?= __( 'The following Add-Ons extend the functionality of Site Reviews.', 'site-reviews' ); ?></p>
	<div class="glsr-addons wp-clearfix">
	<?php
		$template->render( 'addons/addon', [
			'context' => [
				'description' => __( 'Sync your Tripadvisor reviews and display them on your site.', 'site-reviews' ),
				'link' => 'https://niftyplugins.com/addons/site-reviews-tripadvisor/',
				'name' => 'tripadvisor',
				'title' => 'Tripadvisor Reviews',
			],
		]);
		$template->render( 'addons/addon', [
			'context' => [
				'description' => __( 'Sync your Yelp reviews and display them on your site.', 'site-reviews' ),
				'link' => 'https://niftyplugins.com/addons/site-reviews-yelp/',
				'name' => 'yelp',
				'title' => 'Yelp Reviews',
			],
		]);
	?>
	</div>
</div>
