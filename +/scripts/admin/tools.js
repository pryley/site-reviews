/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Tools = function() {
		$( 'form' ).on( 'click', '#clear-console', this.loadConsole_, this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '#fetch-console', this.loadConsole_, this.onClick_.bind( this ));
		$( 'form' ).on( 'click', '[data-ajax-click]', this.onClick_.bind( this ));
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
				$("html, body").animate({ scrollTop: 0 }, 500);
				$('#glsr-notices').on( 'click', 'a', function() {
					localStorage.setItem('glsr-expand', $(this).data('expand'));
				});
			});
		},
	};
})( jQuery );
