/* globals GLSR, x */

(function( tinymce ) {

	"use strict";

	tinymce.PluginManager.add( 'glsr_shortcode', function( editor ) {
		editor.addCommand( 'GLSR_Shortcode', function() {
			GLSR.shortcode.create( editor.id );
		});
	});

})( window.tinymce );
