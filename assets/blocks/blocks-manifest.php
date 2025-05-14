<?php
// This file is generated. Do not modify it manually.
return array(
	'site_review' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/review',
		'title' => 'Single Review',
		'category' => 'site-reviews',
		'description' => 'Display a single review.',
		'keywords' => array(
			'review',
			'site reviews'
		),
		'version' => '2.0.0',
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
			),
			'styleRatingColor' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleRatingColorCustom' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleStarSize' => array(
				'default' => '1.25em',
				'type' => 'string'
			)
		),
		'supports' => array(
			'color' => array(
				'background' => false,
				'enableContrastChecker' => false,
				'heading' => true,
				'link' => true,
				'text' => true
			),
			'html' => false,
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
		'example' => array(
			
		),
		'editorScript' => array(
			'file:./index.js'
		),
		'editorStyle' => array(
			
		),
		'script' => array(
			'site-reviews'
		),
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		)
	),
	'site_reviews' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/reviews',
		'title' => 'Latest Reviews',
		'category' => 'site-reviews',
		'description' => 'Display your reviews.',
		'keywords' => array(
			'reviews',
			'site reviews'
		),
		'version' => '2.0.0',
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
			'styleRatingColor' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleRatingColorCustom' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleReviewSpacing' => array(
				'default' => array(
					'top' => '2em',
					'bottom' => '2em'
				),
				'type' => 'object'
			),
			'styleStarSize' => array(
				'default' => '1.25em',
				'type' => 'string'
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
		'supports' => array(
			'color' => array(
				'background' => false,
				'enableContrastChecker' => false,
				'heading' => true,
				'link' => true,
				'text' => true
			),
			'html' => false,
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
		'example' => array(
			
		),
		'editorScript' => array(
			'file:./index.js'
		),
		'editorStyle' => array(
			
		),
		'script' => array(
			'site-reviews'
		),
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		)
	),
	'site_reviews_form' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/form',
		'title' => 'Review Form',
		'category' => 'site-reviews',
		'description' => 'Display a review form.',
		'keywords' => array(
			'review form',
			'site reviews'
		),
		'textdomain' => 'site-reviews',
		'version' => '2.0.0',
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
			),
			'styleFieldSpacing' => array(
				'default' => array(
					'top' => '0.75em',
					'left' => '0.75em',
					'bottom' => '0.75em',
					'right' => '0.75em'
				),
				'type' => 'object'
			),
			'styleRatingColor' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleRatingColorCustom' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleStarSize' => array(
				'default' => '2em',
				'type' => 'string'
			),
			'summary_id' => array(
				'default' => '',
				'type' => 'string'
			)
		),
		'supports' => array(
			'color' => array(
				'background' => false,
				'button' => true,
				'enableContrastChecker' => false,
				'link' => true,
				'text' => true
			),
			'html' => false,
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
		'example' => array(
			
		),
		'editorScript' => array(
			'file:./index.js'
		),
		'editorStyle' => array(
			'file:./index.css'
		),
		'script' => array(
			'site-reviews'
		),
		'style' => array(
			'file:./style-index.css',
			'site-reviews',
			'wp-block-button'
		)
	),
	'site_reviews_summary' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/summary',
		'title' => 'Rating Summary',
		'category' => 'site-reviews',
		'description' => 'Display a rating summary of your reviews.',
		'keywords' => array(
			'rating summary',
			'site reviews'
		),
		'version' => '2.0.0',
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
			'styleAlign' => array(
				'default' => 'left',
				'type' => 'string'
			),
			'styleBarSize' => array(
				'default' => '1em',
				'type' => 'string'
			),
			'styleBarSpacing' => array(
				'default' => '0.5em',
				'type' => 'string'
			),
			'styleRatingColor' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleRatingColorCustom' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleStarSize' => array(
				'default' => '1.5em',
				'type' => 'string'
			),
			'styleMaxWidth' => array(
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
		'supports' => array(
			'color' => array(
				'background' => false,
				'enableContrastChecker' => false,
				'link' => false,
				'text' => true
			),
			'html' => false,
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
		'example' => array(
			
		),
		'editorScript' => array(
			'file:./index.js'
		),
		'editorStyle' => array(
			
		),
		'script' => array(
			'site-reviews'
		),
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		)
	),
	'surecart_product_rating' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/surecart-product-rating',
		'title' => 'Product Rating',
		'category' => 'surecart-product-page',
		'ancestor' => array(
			'surecart/product-page',
			'surecart/product-template',
			'surecart/upsell'
		),
		'description' => 'Display the product rating.',
		'keywords' => array(
			'site reviews'
		),
		'version' => '1.0.0',
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
				'default' => '#product-reviews',
				'type' => 'string'
			),
			'style' => array(
				'type' => 'object',
				'default' => array(
					'spacing' => array(
						'margin' => array(
							'bottom' => '0',
							'top' => '0'
						)
					)
				)
			),
			'styleRatingColor' => array(
				'default' => '',
				'type' => 'string'
			),
			'styleRatingColorCustom' => array(
				'default' => '',
				'type' => 'string'
			),
			'text' => array(
				'attribute' => 'title',
				'default' => '{num} customer reviews',
				'type' => 'string'
			)
		),
		'usesContext' => array(
			'postId'
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
		'example' => array(
			
		),
		'editorScript' => 'file:./index.js',
		'viewScriptModule' => 'file:./view.js',
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		),
		'render' => 'file:./view.php'
	),
	'surecart_product_reviews' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site-reviews/surecart-product-reviews',
		'title' => 'Product Reviews',
		'category' => 'surecart-product-page',
		'description' => 'Display the product reviews section.',
		'keywords' => array(
			'site reviews'
		),
		'version' => '1.0.0',
		'textdomain' => 'site-reviews',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'wide'
			),
			'anchor' => array(
				'type' => 'string',
				'default' => 'product-reviews'
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
		'usesContext' => array(
			'postId'
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
		'example' => array(
			
		),
		'editorScript' => 'file:./index.js',
		'style' => array(
			'file:./style-index.css',
			'site-reviews'
		)
	)
);
