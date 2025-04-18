<?php
// This file is generated. Do not modify it manually.
return array(
	'site_review' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/review',
		'version' => '2.0.0',
		'title' => 'Single Review',
		'description' => 'Display a single review.',
		'category' => 'site-reviews',
		'example' => array(
			
		),
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'className' => array(
				'default' => '',
				'type' => 'string'
			),
			'hide' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'id' => array(
				'default' => '',
				'type' => 'string'
			),
			'post_id' => array(
				'default' => '',
				'type' => 'string'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'script' => 'site-reviews',
		'style' => 'site-reviews',
		'keywords' => array(
			'review',
			'site reviews'
		),
		'supports' => array(
			'html' => false
		)
	),
	'site_reviews' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/reviews',
		'version' => '2.0.0',
		'title' => 'Latest Reviews',
		'description' => 'Display your reviews.',
		'category' => 'site-reviews',
		'example' => array(
			
		),
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'assigned_posts' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'assigned_terms' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'assigned_users' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'className' => array(
				'default' => '',
				'type' => 'string'
			),
			'display' => array(
				'default' => 5,
				'type' => 'number'
			),
			'hide' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'id' => array(
				'default' => '',
				'type' => 'string'
			),
			'pagination' => array(
				'default' => '',
				'type' => 'string'
			),
			'post_id' => array(
				'default' => '',
				'type' => 'string'
			),
			'rating' => array(
				'default' => 0,
				'type' => 'number'
			),
			'schema' => array(
				'default' => 0,
				'enum' => array(
					0,
					1
				),
				'type' => 'number'
			),
			'terms' => array(
				'default' => '',
				'type' => 'string'
			),
			'type' => array(
				'default' => 'local',
				'type' => 'string'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'script' => 'site-reviews',
		'style' => 'site-reviews',
		'keywords' => array(
			'reviews',
			'site reviews'
		),
		'supports' => array(
			'html' => false
		)
	),
	'site_reviews_form' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/form',
		'version' => '2.0.0',
		'title' => 'Review Form',
		'description' => 'Display a review form.',
		'category' => 'site-reviews',
		'example' => array(
			
		),
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'assigned_posts' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'assigned_terms' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'assigned_users' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'className' => array(
				'default' => '',
				'type' => 'string'
			),
			'hide' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'id' => array(
				'default' => '',
				'type' => 'string'
			),
			'reviews_id' => array(
				'default' => '',
				'type' => 'string'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'script' => 'site-reviews',
		'style' => array(
			'core/button',
			'site-reviews'
		),
		'keywords' => array(
			'review form',
			'site reviews'
		),
		'supports' => array(
			'html' => false
		)
	),
	'site_reviews_summary' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/summary',
		'version' => '2.0.0',
		'title' => 'Rating Summary',
		'description' => 'Display a rating summary of your reviews.',
		'category' => 'site-reviews',
		'example' => array(
			
		),
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'assigned_posts' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'assigned_terms' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'assigned_users' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'className' => array(
				'default' => '',
				'type' => 'string'
			),
			'hide' => array(
				'default' => array(
					
				),
				'items' => array(
					'type' => 'string'
				),
				'type' => 'array'
			),
			'id' => array(
				'default' => '',
				'type' => 'string'
			),
			'post_id' => array(
				'default' => '',
				'type' => 'string'
			),
			'rating' => array(
				'default' => 1,
				'type' => 'number'
			),
			'rating_field' => array(
				'default' => '',
				'type' => 'string'
			),
			'schema' => array(
				'default' => 0,
				'enum' => array(
					0,
					1
				),
				'type' => 'number'
			),
			'terms' => array(
				'default' => '',
				'type' => 'string'
			),
			'type' => array(
				'default' => 'local',
				'type' => 'string'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'script' => 'site-reviews',
		'style' => 'site-reviews',
		'keywords' => array(
			'rating summary',
			'site reviews'
		),
		'supports' => array(
			'html' => false
		)
	)
);
