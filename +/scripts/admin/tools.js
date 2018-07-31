/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Tools = function() {
		$( 'form' ).on( 'click', '#clear-console', this.loadConsole_, this.onClick_ );
		$( 'form' ).on( 'click', '#fetch-console', this.loadConsole_, this.onClick_ );
		$( 'form' ).on( 'click', '#count-reviews', this.onClick_ );
	};

	GLSR.Tools.prototype = {
		loadConsole_: function( response, success ) {
			if( success ) {
				$( '#log-file' ).val( response.console );
			}
		},
		onClick_: function( ev ) {
			var request = {
				action: ev.currentTarget.name,
			};
			(new GLSR.Ajax( request, ev )).post_( function( response, success ) {
				if( typeof ev.data === 'function' ) {
					ev.data( response, success );
				}
			});
		},
	};
})( jQuery );
