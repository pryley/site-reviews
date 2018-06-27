/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Notices = function( notices ) { // string
		if( !notices )return;
		if( !$( '#glsr-notices' ).length ) {
			$( '#message.notice' ).remove();
			$( 'form#post' ).before( '<div id="glsr-notices" />' );
		}
		$( '#glsr-notices' ).html( notices );
		$( document ).trigger( 'wp-updates-notice-added' );
	};
})( jQuery );
