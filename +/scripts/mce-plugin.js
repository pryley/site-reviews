/** global: GLSR */
(function( tinymce ) {
	'use strict';
	tinymce.PluginManager.add( 'glsr_shortcode', function( editor ) {
		editor.addCommand( 'GLSR_Shortcode', function() {
			(new GLSR.Shortcode( '.glsr-mce' )).create( editor.id );
		});
	});
})( window.tinymce );
