/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Status = function( selector ) {
		var elements = document.querySelectorAll( selector );
		if( !elements.length )return;
		elements.forEach( function( el ) {
			el.addEventListener( 'click', this.onClick_ );
		}.bind( this ));
	};

	GLSR.Status.prototype = {
		/** @return void */
		onClick_: function( ev ) { // MouseEvent
			var post_id = ev.currentTarget.href.match( /post=([0-9]+)/ );
			var status = ev.currentTarget.href.match( /action=([a-z]+)/ );
			if( post_id === null || status === null )return;
			var request = {
				action: 'change-status',
				nonce: GLSR.nonce['change-status'],
				post_id: post_id[1],
				status: status[1],
			};
			(new GLSR.Ajax( request, ev )).post_( function( response ) {
				if( !response.class )return;
				var el = $( ev.currentTarget );
				el.closest( 'tr' ).removeClass( 'status-pending status-publish' ).addClass( response.class );
				el.closest( 'td.column-title' ).find( 'strong' ).html( response.link );
			});
		},
	};
})( jQuery );
