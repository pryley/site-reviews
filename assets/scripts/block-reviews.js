/** global: GLSR */
(function( blocks ) {
	'use strict';
	// var el = element.createElement;
	// var __ = i18n.__;

	blocks.registerBlockType( 'site-reviews/reviews', {
		category: 'site-reviews',
		description: 'Display your most recent reviews.',
		edit: function() {
			return null;
		},
		icon: 'star-half',
		keywords: ['recent reviews'],
		save: function() {
			return null;
		},
		supports: {
			html: false,
		},
		title: 'Latest Reviews',
	});
})( window.wp.blocks );
