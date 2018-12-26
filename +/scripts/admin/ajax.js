/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Ajax = function( request, ev, form ) { // object
		this.event = ev || null;
		this.form = form || null;
		this.notice = null;
		this.request = request || {};
	};

	GLSR.Ajax.prototype = {
		/** @return void */
		buildData_: function( el ) { // HTMLElement|null
			var data = {
				action: GLSR.action,
				_ajax_request: true,
			};
			if( this.form ) {
				var formdata = new GLSR.Serializer( this.form );
				if( formdata[GLSR.nameprefix] ) {
					this.request = formdata[GLSR.nameprefix];
				}
			}
			this.buildNonce_( el );
			data[GLSR.nameprefix] = this.request;
			return data;
		},

		/** @return void */
		buildNonce_: function( el ) { // HTMLElement|null
			if( this.request._nonce )return;
			if( GLSR.nonce[this.request._action] ) {
				this.request._nonce = GLSR.nonce[this.request._action];
				return;
			}
			if( !el )return;
			this.request._nonce = el.closest( 'form' ).find( '#_wpnonce' ).val();
		},

		/** @return void */
		doPost_: function( callback, el ) {
			$.post( GLSR.ajaxurl, this.buildData_( el )).done( function( response ) {
				if( typeof callback === 'function' ) {
					callback( response.data, response.success );
				}
				if( el ) {
					el.prop( 'disabled', false );
				}
			}).always( function( response ) {
				if( !response.data ) {
					GLSR.Notices( '<div class="notice notice-error inline is-dismissible"><p>Unknown error.</p></div>' );
				}
				else if( response.data.notices ) {
					GLSR.Notices( response.data.notices );
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
