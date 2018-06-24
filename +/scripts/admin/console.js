/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	GLSR.Console = function() {
		x( 'form' ).on( 'click', '#clear-console', this.onClick_ );
	};

	GLSR.Console.prototype = {
		onClick_: function( ev ) {
		 	var request = {
				action: 'clear-console',
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				GLSR.Notices( response.notices );
				x( '#log-file' ).val( response.console );
			});
		},
	};
})( jQuery );
