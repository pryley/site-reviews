/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	GLSR.Notices = function( notices ) { // string
		if( !notices )return;
		if( !x( '#glsr-notices' ).length ) {
			x( '#message.notice' ).remove();
			x( 'form#post' ).before( '<div id="glsr-notices" />' );
		}
		x( '#glsr-notices' ).html( notices );
		x( document ).trigger( 'wp-updates-notice-added' );
	};
})( jQuery );
