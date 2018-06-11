/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Logger = function() {
		x( 'form' ).on( 'click', '#clear-log', this.onClick_ );
	};

	Logger.prototype = {
		onClick_: function( ev ) {
		 	var request = {
				action: 'clear-log',
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				GLSR.Notices( response.notices );
				x( '#log-file' ).val( response.logger );
			});
		},
	};

	GLSR.Logger = Logger;
})( jQuery );
