/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Tools = function() {
		$( 'form' ).on( 'click', '#clear-console', this.loadConsole_, this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#fetch-console', this.loadConsole_, this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#count-reviews', this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#migrate-plugin', this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#reset-permissions', this.onClick_.bind( this ));
	};

	GLSR.Tools.prototype = {
		loadConsole_: function( response, success ) {
			if( success ) {
				$( '#log-file' ).val( response.console );
			}
		},
		onClick_: function( ev ) {
			(new GLSR.Ajax( {}, ev, ev.currentTarget.closest( 'form' ))).post( function( response, success ) {
				if( typeof ev.data === 'function' ) {
					ev.data( response, success );
				}
			});
		},
	};
})( jQuery );
