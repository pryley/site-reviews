/** global: GLSR, grecaptcha */
;(function() {

	'use strict';

	GLSR.Recaptcha = function( form ) { // Form object
		this.Form = form;
	};

	GLSR.Recaptcha.prototype = {

		/** @return void */
		addListeners_: function() {
			var overlayEl = this.getOverlay_();
			if( overlayEl === -1 )return;
			overlayEl.addEventListener( 'click', this.Form.enableButton );
			window.addEventListener( 'keyup', this.onKeyup_.bind( this, overlayEl ));
		},

		/** @return void */
		execute_: function() {
			var recaptchaId = this.getId_();
			if( recaptchaId !== -1 ) {
				grecaptcha.execute( recaptchaId );
				return;
			}
			// recaptcha ID not found so pass through an error
			this.Form.submitForm_( false );
		},

		/** @return string|int (-1) */
		getId_: function() {
			return this.search_( function( value, id ) {
				if( Object.prototype.toString.call( value ) !== '[object HTMLDivElement]' )return;
				if( value.closest( 'form' ) === this.Form.form ) {
					return id;
				}
			});
		},

		/** @return HTMLDivElement|int (-1) */
		getOverlay_: function() {
			return this.search_( function( value ) {
				if( Object.prototype.toString.call( value ) !== '[object Object]' )return;
				for( var obj in value) {
					if( !value.hasOwnProperty( obj ) || Object.prototype.toString.call( value[obj] ) !== '[object HTMLDivElement]' )continue;
					if( value[obj].className === '' ) {
						return value[obj].firstChild;
					}
				}
				return false;
			});
		},

		/** @return void */
		onKeyup_: function( ev ) { // KeyboardEvent
			if( ev.keyCode !== 27 )return;
			this.Form.enableButton();
			this.removeListeners_( ev.target );
		},

		/** @return void */
		removeListeners_: function( overlayEl ) { // HTMLDivElement
			overlayEl.removeEventListener( 'click', this.Form.enableButton );
			window.removeEventListener( 'keyup', this.onKeyup_ );
		},

		/** @return void */
		render_: function() {
			this.Form.form.onsubmit = null;
			var recaptchaEl = this.Form.form.querySelector( '.glsr-recaptcha-holder' );
			if( !recaptchaEl )return;
			recaptchaEl.innerHTML = '';
			var id = grecaptcha.render( recaptchaEl, {
				callback: this.submitForm_.bind( this ),
				'expired-callback': function() {
					grecaptcha.reset( id );
				},
			}, true );
		},

		/** @return void */
		reset_: function() {
			var recaptchaId = this.getId_();
			if( recaptchaId !== -1 ) {
				grecaptcha.reset( recaptchaId );
			}
		},

		/** @return mixed|int (-1) */
		search_: function( callback ) { // function
			var result = -1;
			if( window.hasOwnProperty( '___grecaptcha_cfg' )) {
				var clients = window.___grecaptcha_cfg.clients;
				var i, key;
				for( i in clients ) {
					for( key in clients[i] ) {
						if( !( result = callback( clients[i][key], i ).bind( this )))continue;
						return result;
					}
				}
			}
			return result;
		},

		/** @return void */
		submitForm_: function( token ) { // string
			this.Form.form.submitForm( token );
		},
	};
})();
