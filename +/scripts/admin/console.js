/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Console = function() {
		$( 'form' ).on( 'click', '#clear-console', this.onClick_ );
	};

	GLSR.Console.prototype = {
		onClick_: function( ev ) {
		 	var request = {
				action: 'clear-console',
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				GLSR.Notices( response.notices );
				$( '#log-file' ).val( response.console );
			});
		},
	};
})( jQuery );
