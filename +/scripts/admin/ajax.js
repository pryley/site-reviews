/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Ajax = function( request, ev ) { // object
		this.event = ev || null;
		this.notice = null;
		this.request = request;
	};

	GLSR.Ajax.prototype = {
		/** @return void */
		buildData_: function( el ) { // HTMLElement|null
			this.buildNonce_( el );
			var data = {
				action: GLSR.action,
				ajax_request: true,
			};
			data[GLSR.nameprefix] = this.request;
			return data;
		},

		/** @return void */
		buildNonce_: function( el ) { // HTMLElement|null
			if( this.request.nonce )return;
			if( GLSR.nonce[this.request.action] ) {
				this.request.nonce = GLSR.nonce[this.request.action];
				return;
			}
			if( !el )return;
			this.request.nonce = el.closest( 'form' ).find( '#_wpnonce' ).val();
		},

		/** @return void */
		doPost_: function( callback, el ) {
			var self = this;
			$.post( GLSR.ajaxurl, this.buildData_( el )).done( function( response ) {
				self.notice = ( response.data.notices || null );
				if( typeof callback === 'function' ) {
					callback( response.data, response.success );
				}
				if( el ) {
					el.prop( 'disabled', false );
				}
			}).always( function() {
				if( self.notice ) {
					GLSR.Notices( self.notice );
				}
			});
		},

		/** @return void */
		post_: function( callback ) { // function|void
			if( this.event ) {
				this.postFromEvent_( callback );
				return;
			}
			this.doPost_( callback );
		},

		/** @return void */
		postFromEvent_: function( callback ) { // Event, function|void
			this.event.preventDefault();
			var el = $( this.event.currentTarget );
			if( el.is( ':disabled' ))return;
			el.prop( 'disabled', true );
			this.doPost_( callback, el );
		},
	};
})( jQuery );
