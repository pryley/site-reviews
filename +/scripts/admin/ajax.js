/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Ajax = function( request, ev ) { // object
		this.event = ev || null;
		this.post = this.post_;
		this.request = request;
	};

	GLSR.Ajax.prototype = {
		/** @return void */
		buildData_: function( el ) { // HTMLElement|null
			this.buildNonce_( el );
			return {
				action: GLSR.action,
				ajax_request: true,
				request: this.request,
			};
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
		post_: function( callback ) { // function|void
			if( this.event ) {
				this.postFromEvent_( callback );
				return;
			}
			$.post( GLSR.ajaxurl, this.buildData_(), function( response ) {
				if( typeof callback !== 'function' )return;
				callback( response );
			});
		},

		/** @return void */
		postFromEvent_: function( callback ) { // Event, function|void
			this.event.preventDefault();
			var el = $( this.event.target );
			if( el.is( ':disabled' ))return;
			el.prop( 'disabled', true );
			$.post( GLSR.ajaxurl, this.buildData_( el ), function( response ) {
				if( typeof callback === 'function' ) {
					callback( response );
				}
				el.prop( 'disabled', false );
			});
		},
	};
})( jQuery );
