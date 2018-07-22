/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Tools = function() {
		$( 'form' ).on( 'click', '#clear-console', this.clearConsole_, this.onClick_ );
		$( 'form' ).on( 'click', '#count-reviews', this.onClick_ );
	};

	GLSR.Tools.prototype = {
		clearConsole_: function( response ) {
			$( '#log-file' ).val( response.console );
		},
		onClick_: function( ev ) {
		 	var request = {
				action: ev.currentTarget.name,
			};
			(new GLSR.Ajax( request, ev )).post_( function( response ) {
				GLSR.Notices( response.notices );
				if( typeof ev.data === 'function' ) {
					ev.data( response );
				}
			});
		},
	};
})( jQuery );
