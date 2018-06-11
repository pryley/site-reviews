/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	GLSR.ColorPicker = function() {
		if( typeof x.wp !== 'object' || typeof x.wp.wpColorPicker !== 'function' )return;
		x( document ).find( 'input[type=text].color-picker-hex' ).each( function() {
			x( this ).wpColorPicker( x( this ).data( 'colorpicker' ) || {} );
		});
	};
})( jQuery );
