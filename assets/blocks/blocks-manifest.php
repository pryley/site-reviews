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
			'labels' => array(
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
			'summary_bar_size' => array(
				'default' => '1em',
				'type' => 'string'
			),
			'summary_star_size' => array(
				'default' => '1.5em',
				'type' => 'string'
			),
			'summary_max_width' => array(
				'default' => '48ch',
				'type' => 'string'
			),
			'terms' => array(
				'default' => '',
				'type' => 'string'
			),
			'text' => array(
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
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		),
		'styles' => array(
			array(
				'name' => '1',
				'label' => 'Style 1'
			),
			array(
				'name' => '2',
				'label' => 'Style 2'
			),
			array(
				'name' => '3',
				'label' => 'Style 3'
			)
		),
		'keywords' => array(
			'rating summary',
			'site reviews'
		),
		'supports' => array(
			'html' => false,
			'spacing' => array(
				'padding' => true,
				'margin' => array(
					'top',
					'bottom'
				)
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true,
				'textAlign' => false,
				'__experimentalTextDecoration' => false,
				'__experimentalFontFamily' => true,
				'__experimentalFontWeight' => true,
				'__experimentalFontStyle' => true,
				'__experimentalTextTransform' => false,
				'__experimentalLetterSpacing' => true,
				'__experimentalDefaultControls' => array(
					'fontSize' => true,
					'lineHeight' => true
				)
			)
		)
	),
	'surecart_product_rating' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/surecart-product-rating',
		'version' => '1.0.0',
		'title' => 'Product Rating',
		'description' => 'Display the product rating.',
		'icon' => 'star-half',
		'category' => 'surecart-product-page',
		'example' => array(
			
		),
		'usesContext' => array(
			'postId'
		),
		'ancestor' => array(
			'surecart/product-page',
			'surecart/product-template',
			'surecart/upsell'
		),
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'has_text' => array(
				'default' => true,
				'type' => 'boolean'
			),
			'is_link' => array(
				'default' => false,
				'type' => 'boolean'
			),
			'link_url' => array(
				'attribute' => 'href',
				'default' => '#reviews',
				'type' => 'string'
			),
			'text' => array(
				'attribute' => 'title',
				'default' => '{num} customer reviews',
				'type' => 'string'
			)
		),
		'keywords' => array(
			'site reviews'
		),
		'supports' => array(
			'interactivity' => true,
			'color' => array(
				'background' => false,
				'gradients' => false,
				'link' => true,
				'__experimentalDefaultControls' => array(
					'text' => true
				)
			),
			'spacing' => array(
				'padding' => true,
				'margin' => array(
					'top',
					'bottom'
				)
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true,
				'textAlign' => false,
				'__experimentalTextDecoration' => false,
				'__experimentalFontFamily' => true,
				'__experimentalFontWeight' => true,
				'__experimentalFontStyle' => true,
				'__experimentalTextTransform' => false,
				'__experimentalLetterSpacing' => true,
				'__experimentalDefaultControls' => array(
					'fontSize' => true,
					'lineHeight' => true
				)
			)
		),
		'render' => 'file:./view.php',
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		),
		'editorScript' => 'file:./index.js',
		'viewScriptModule' => 'file:./view.js'
	),
	'surecart_product_reviews' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/surecart-product-reviews',
		'version' => '1.0.0',
		'title' => 'Product Reviews',
		'description' => 'Display the product reviews section.',
		'icon' => 'star-filled',
		'category' => 'surecart-product-page',
		'example' => array(
			
		),
		'usesContext' => array(
			'postId'
		),
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'wide'
			),
			'anchor' => array(
				'type' => 'string',
				'default' => 'reviews'
			),
			'layout' => array(
				'type' => 'object',
				'default' => array(
					'type' => 'constrained'
				)
			),
			'style' => array(
				'type' => 'object',
				'default' => array(
					'spacing' => array(
						'margin' => array(
							'top' => '40px'
						)
					)
				)
			)
		),
		'keywords' => array(
			'site reviews'
		),
		'supports' => array(
			'anchor' => true,
			'align' => true,
			'layout' => array(
				'default' => array(
					'type' => 'constrained'
				)
			),
			'spacing' => array(
				'padding' => true,
				'margin' => true,
				'blockGap' => true
			)
		),
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		),
		'editorScript' => 'file:./index.js'
	)
);
