/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Console = function() {
		x( 'form' ).on( 'click', '#clear-console', this.onClick_ );
	};

	Console.prototype = {
		onClick_: function( ev ) {
		 	var request = {
				action: 'clear-console',
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				GLSR.Notices( response.notices );
				x( '#console' ).val( response.console );
			});
		},
	};

	GLSR.Console = Console;
})( jQuery );
