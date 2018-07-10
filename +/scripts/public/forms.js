/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */
;(function() {

	'use strict';

	var GLSR_Form = function( formEl, buttonEl ) { // HTMLElement, HTMLElement
		this.button = buttonEl;
		this.form = formEl;
		this.init = this.init_.bind( this );
		this.recaptcha = new GLSR.Recaptcha( this );
	};

	GLSR_Form.prototype = {
		config: {
			fieldErrorsClass: 'glsr-field-errors',
			fieldSelector: '.glsr-field',
			formMessagesClass: 'glsr-form-messages',
			hasErrorClass: 'glsr-has-error',
		},

		/** @return void */
		addRemoveClass_: function( el, classValue, bool ) { // HTMLElement, string, bool
			el.classList[bool ? 'add' : 'remove']( classValue );
		},

		/** @return void */
		clearFieldError_: function( el ) { // HTMLElement
			var fieldEl = el.closest( this.config.fieldSelector );
			if( fieldEl === null )return;
			fieldEl.classList.remove( this.config.hasErrorClass );
			var errorEl = fieldEl.querySelector( this.config.fieldErrorsSelector );
			if( errorEl !== null ) {
				errorEl.parentNode.removeChild( errorEl );
			}
		},

		/** @return void */
		clearFormErrors_: function() {
			this.getResultsEl_().innerHTML = '';
			for( var i = 0; i < this.form.length; i++ ) {
				this.clearFieldError_( this.form[i] );
			}
		},

		/** @return void */
		disableButton_: function() {
			this.button.setAttribute( 'disabled', '' );
		},

		/** @return void */
		enableButton_: function() {
			this.button.removeAttribute( 'disabled' );
		},

		/** @return void */
		fallbackSubmit_: function() {
			var ajax = new GLSR.Ajax();
			if( ajax.isFileAPISupported() && ajax.isFormDataSupported() && ajax.isUploadSupported() )return;
			this.form.submit();
		},

		/** @return HTMLDivElement */
		getFieldErrorsEl_: function( fieldEl ) { // HTMLElement
			var errorsEl = fieldEl.querySelector( '.' + this.config.fieldErrorsClass );
			if( errorsEl === null ) {
				errorsEl = document.createElement( 'div' );
				errorsEl.setAttribute( 'class', this.config.fieldErrorsClass );
				fieldEl.appendChild( errorsEl );
			}
			return errorsEl;
		},

		/** @return HTMLFormElement */
		getForm_: function( recaptchaToken ) { // string|null
			var tokenEl = this.form['g-recaptcha-response'];
			if( tokenEl ) {
				tokenEl.value = recaptchaToken || '';
			}
			return this.form;
		},

		/** @return HTMLDivElement */
		getResultsEl_: function() {
			var resultsEl = this.form.querySelector( '.' + this.config.formMessagesClass );
			if( resultsEl === null ) {
				resultsEl = document.createElement( 'div' );
				resultsEl.setAttribute( 'class', this.config.formMessagesClass );
				this.button.parentNode.insertBefore( resultsEl, this.button.nextSibling );
			}
			return resultsEl;
		},

		/** @return void */
		handleResponse_: function( response, success ) { // object
			if( response.recaptcha === true ) {
				return this.recaptcha.execute_();
			}
			if( response.recaptcha === 'reset' ) {
				this.recaptcha.reset_();
			}
			if( !!success ) {
				this.recaptcha.reset_();
				this.form.reset();
			}
			this.showFieldErrors_( response.errors );
			this.showResults_( response, success );
			this.enableButton_();
			response.form = this.form;
			document.dispatchEvent( new CustomEvent( 'site-reviews/after/submission', { detail: response }));
		},

		/** @return void */
		init_: function() {
			this.button.addEventListener( 'click', this.onClick_.bind( this ));
			this.form.addEventListener( 'change', this.onChange_.bind( this ));
			this.form.addEventListener( 'submit', this.onSubmit_.bind( this ));
			this.initStarRatings_();
		},

		/** @return void */
		initStarRatings_: function() {
			new StarRating( 'select.glsr-star-rating', {
				clearable: false,
				showText: false,
				onClick: this.clearFieldError_.bind( this ),
			});
		},

		/** @return void */
		onChange_: function( ev ) { // Event
			this.clearFieldError_( ev.target );
		},

		/**
		 * This event method handles the mayhem caused by the invisible-recaptcha plugin
		 * and is triggered on the invisible-recaptcha callback
		 * @return void */
		onClick_: function() {
			var form = this;
			this.form.onsubmit = null;
			HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
			HTMLFormElement.prototype.submit = function() {
				var token = this['g-recaptcha-response'];
				if( token && this.querySelector( form.config.fieldSelector )) {
					form.submitForm_( token.value );
					return;
				}
				this._submit();
			};
		},

		/** @return void */
		onSubmit_: function( ev ) { // HTMLEvent
			if( this.form.classList.contains( 'no-ajax' ))return;
			ev.preventDefault();
			this.recaptcha.addListeners_();
			this.clearFormErrors_();
			this.submitForm_();
		},

		/** @return void */
		showFieldErrors_: function( errors ) { // object
			if( !errors )return;
			var fieldEl, errorsEl;
			for( var error in errors ) {
				fieldEl = this.form.querySelector( '[name=' + error + ']' ).closest( this.config.fieldSelector );
				fieldEl.classList.add( this.config.hasErrorClass );
				errorsEl = this.getFieldErrorsEl_( fieldEl );
				for( var i = 0; i < errors[error].errors.length; i++ ) {
					errorsEl.innerHTML += errors[error].errors[i];
				}
			}
		},

		/** @return void */
		showResults_: function( response, success ) { // object, bool
			var resultsEl = this.getResultsEl_();
			this.addRemoveClass_( resultsEl, 'glsr-has-errors', !success );
			resultsEl.innerHTML = '<p>' + response.message + '</p>';
		},

		/** @return void */
		submitForm_: function( recaptchaToken ) { // string|null
			this.disableButton_();
			this.fallbackSubmit_();
			(new GLSR.Ajax()).post( this.getForm_( recaptchaToken ), this.handleResponse_.bind( this ));
		},
	};

	GLSR.Forms = function( shouldInit ) { // bool
		var form, submitButton;
		this.nodeList = document.querySelectorAll( 'form.glsr-form' );
		this.forms = [];
		for( var i = 0; i < this.nodeList.length; i++ ) {
			submitButton = this.nodeList[i].querySelector( '[type=submit]' );
			if( !submitButton )continue;
			form = new GLSR_Form( this.nodeList[i], submitButton );
			if( shouldInit ) {
				form.init();
			}
			this.forms.push( form );
		}
		this.renderRecaptcha = function() {
			this.forms.forEach( function( form ) {
				form.recaptcha.render_();
			});
		};
	};
})();
