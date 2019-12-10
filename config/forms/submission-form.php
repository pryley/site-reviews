<?php

return [
	'rating' => [
		'label' => __( 'Rate Your Expirence', 'site-reviews' ),
		'type' => 'rating',
	],
	'FirstName' => [
		'label' => __( 'First Name', 'site-reviews' ),
		'placeholder' => __( '', 'site-reviews' ),
		'type' => 'text',
		'required' => true,
	],
	'LastName' => [
		'label' => __( 'Last Name', 'site-reviews' ),
		'placeholder' => __( '', 'site-reviews' ),
		'type' => 'text',
		'required' => true,
	],
	'email' => [
		'label' => __( 'Email', 'site-reviews' ),
		'placeholder' => __( '', 'site-reviews' ),
		'type' => 'email',
		'class' => 'vin-email'
	],
	'product' => [
		'label' => __( 'Select Product', 'site-reviews' ),
		'placeholder' => __( 'Select Product', 'site-reviews' ),
		'type' => 'text',
	],
	'title' => [
		'label' => __( 'Title', 'site-reviews' ),
		'placeholder' => __( 'Your overall impression', 'site-reviews' ),
		'type' => 'text',
	],
	'content' => [
		'label' => __( 'Review', 'site-reviews' ),
		'placeholder' => __( 'We would love to hear about your expirence, level of satisfaction, complaints, comments and feedback here!', 'site-reviews' ),
		'rows' => 5,
		'type' => 'textarea',
	],
	'terms' => [
        'label' => __('This review is based on my own experience and is my genuine opinion.', 'site-reviews'),
        'type' => 'checkbox',
    ],
];
