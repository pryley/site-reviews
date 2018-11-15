/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Tools = function() {
		$( 'form' ).on( 'click', '#clear-console', this.loadConsole_, this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#fetch-console', this.loadConsole_, this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#count-reviews', this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#sync-reviews', this.onSync_.bind( this ));
	};

	GLSR.Tools.prototype = {
		loadConsole_: function( response, success ) {
			if( success ) {
				$( '#log-file' ).val( response.console );
			}
		},
		onClick_: function( ev ) {
			(new GLSR.Ajax( {}, ev, ev.currentTarget.closest( 'form' ))).post_( function( response, success ) {
				if( typeof ev.data === 'function' ) {
					ev.data( response, success );
				}
			});
		},
		onSync_: function( ev ) {
			if( !$( '[name="'+GLSR.nameprefix+'[site]"]' ).val() ) {
				ev.preventDefault();
				return;
			}
			this.onClick_( ev );
		},
	};
})( jQuery );
