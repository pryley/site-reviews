/** global: GLSR */
(function( tinymce ) {
	'use strict';
	tinymce.PluginManager.add( 'glsr_shortcode', function( editor ) {
		editor.addCommand( 'GLSR_Shortcode', function() {
			GLSR.modules.shortcode.create( editor.id );
		});
	});
})( window.tinymce );
