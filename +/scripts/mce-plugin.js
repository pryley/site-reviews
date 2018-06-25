/** global: GLSR */
(function( tinymce ) {
	'use strict';
	tinymce.PluginManager.add( 'glsr_shortcode', function( editor ) {
		var shortcode = new GLSR.Shortcode( '.glsr-mce' );
		editor.addCommand( 'GLSR_Shortcode', function() {
			shortcode.create( editor.id );
		});
	});
})( window.tinymce );
