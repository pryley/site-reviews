/** global: GLSR */
(function( blocks ) {
	'use strict';
	// var el = element.createElement;
	// var __ = i18n.__;

	blocks.registerBlockType( 'site-reviews/summary', {
		title: 'Rating Summary',
		icon: 'star-half',
		category: 'site-reviews',
		edit: function() {
			return null;
		},
		save: function() {
			return null;
		},
	});
})( window.wp.blocks );
