/** global: GLSR, grecaptcha */
;(function() {

	'use strict';

	GLSR.Recaptcha = function( Form ) { // Form object
		this.Form = Form;
		this.id = -1;
	};

	GLSR.Recaptcha.prototype = {

		/** @return void */
		execute_: function() {
			if( this.id !== -1 ) {
				grecaptcha.execute( this.id );
				return;
			}
			this.submitForm_.call( this.Form, 'invalid' );
		},

		/** @return void */
		render_: function() {
			this.Form.form.onsubmit = null;
			var recaptchaEl = this.Form.form.querySelector( '.glsr-recaptcha-holder' );
			if( !recaptchaEl )return;
			recaptchaEl.innerHTML = '';
			this.renderWait_( recaptchaEl );
		},

		/** @return void */
		renderWait_: function( recaptchaEl ) {
			setTimeout( function() {
				if( typeof grecaptcha === 'undefined' || typeof grecaptcha.render === 'undefined' ) {
					return this.renderWait_( recaptchaEl );
				}
				this.id = grecaptcha.render( recaptchaEl, {
					callback: this.submitForm_.bind( this.Form ),
					'expired-callback': this.reset_.bind( this ),
					isolated: true,
				}, true );
			}.bind( this ), 250 );
		},

		/** @return void */
		reset_: function() {
			if( this.id !== -1 ) {
				grecaptcha.reset( this.id );
			}
		},

		/** @return void */
		submitForm_: function( token ) { // string
			var tokenEl = this.form['g-recaptcha-response'];
			if( tokenEl ) {
				tokenEl.value = token;
			}
			if( !this.useAjax ) {
				this.disableButton_();
				this.form.submit();
				return;
			}
			this.submitForm_();
		},
	};
})();
