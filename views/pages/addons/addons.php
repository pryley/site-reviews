<?php defined( 'WPINC' ) || die; ?>

<p><?= __( 'The following Add-Ons extend the functionality of Site Reviews.', 'site-reviews' ); ?></p>
<div class="glsr-addons wp-clearfix">
<?php
glsr( 'Modules\Html' )->renderTemplate( 'addons/addon', [
	'description' => __( 'Sync your Tripadvisor reviews and display them on your site.', 'site-reviews' ),
	'link' => 'https://niftyplugins.com/addons/site-reviews-tripadvisor/',
	'name' => 'tripadvisor',
	'title' => 'Tripadvisor Reviews',
]);
glsr( 'Modules\Html' )->renderTemplate( 'addons/addon', [
	'description' => __( 'Sync your Yelp reviews and display them on your site.', 'site-reviews' ),
	'link' => 'https://niftyplugins.com/addons/site-reviews-yelp/',
	'name' => 'yelp',
	'title' => 'Yelp Reviews',
]);
?>
</div>
