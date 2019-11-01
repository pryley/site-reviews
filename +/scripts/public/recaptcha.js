/** global: GLSR, grecaptcha, MutationObserver */
;(function() {

	'use strict';

	GLSR.Recaptcha = function( Form ) { // Form object
		this.Form = Form;
		this.counter = 0;
		this.id = -1;
		this.is_submitting = false;
		this.recaptchaEl = Form.form.querySelector('.glsr-recaptcha-holder');
		this.observer = new MutationObserver( function( mutations ) {
			var mutation = mutations.pop();
			if( !mutation.target || mutation.target.style.visibility === 'visible' )return;
			this.observer.disconnect();
			setTimeout( function() {
				if( this.is_submitting )return;
				this.Form.enableButton_();
			}.bind( this ), 250 );
		}.bind( this ));
	};

	GLSR.Recaptcha.prototype = {

		/** @return void */
		destroy_: function() {
			this.counter = 0;
			this.id = -1;
			this.is_submitting = false;
			if (this.recaptchaEl) {
				this.recaptchaEl.innerHTML = '';
			}
		},

		/** @return void */
		execute_: function() {
			if( this.id !== -1 ) {
				this.counter = 0;
				this.observeMutations_( this.id );
				grecaptcha.execute( this.id );
				return;
			}
			setTimeout( function() {
				this.counter++;
				this.submitForm_.call( this.Form, this.counter );
			}.bind( this ), 1000 );
		},

		/** @return void */
		observeMutations_: function( id ) {
			var client = window.___grecaptcha_cfg.clients[id];
			for( var property in client) {
				if( !client.hasOwnProperty( property ))continue;
				if( Object.prototype.toString.call( client[property] ) !== '[object String]' )continue;
				var overlayEl = document.querySelector( 'iframe[name=c-' + client[property] + ']' );
				if( overlayEl ) {
					this.observer.observe( overlayEl.parentElement.parentElement, {
						attributeFilter: ['style'],
						attributes: true,
					});
					break;
				}
			}
		},

		/** @return void */
		render_: function() {
			this.Form.form.onsubmit = null;
			this.destroy_();
			this.renderWait_();
		},

		/** @return void */
		renderWait_: function() {
			if (!this.recaptchaEl) return;
			setTimeout( function() {
				if( typeof grecaptcha === 'undefined' || typeof grecaptcha.render === 'undefined' ) {
					return this.renderWait_();
				}
				this.id = grecaptcha.render(this.recaptchaEl, {
					callback: this.submitForm_.bind( this.Form, this.counter ),
					// 'error-callback': this.reset_.bind( this ), //@todo
					// error-callback: The name of your callback function, executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. If you specify a function here, you are responsible for informing the user that they should retry.
					'expired-callback': this.reset_.bind( this ),
					isolated: true,
				}, true );
			}.bind( this ), 250 );
		},

		/** @return void */
		reset_: function() {
			this.counter = 0;
			this.is_submitting = false;
			if( this.id !== -1 ) {
				grecaptcha.reset( this.id );
			}
		},

		/** @return void */
		submitForm_: function( counter ) { // int
			this.recaptcha.is_submitting = true;
			if( !this.useAjax ) {
				this.disableButton_();
				this.form.submit();
				return;
			}
			this.submitForm_( counter );
		},
	};
})();
