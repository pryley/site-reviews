/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */
;(function() {

	'use strict';

	var GLSR_Form = function( formEl, buttonEl ) { // HTMLElement, HTMLElement
		this.button = buttonEl;
		this.config = GLSR.validationconfig;
		this.form = formEl;
		this.recaptcha = new GLSR.Recaptcha( this );
		this.strings = GLSR.validationstrings;
		this.useAjax = this.isAjaxEnabled_();
		this.validation = new GLSR.Validation( formEl );
	};

	GLSR_Form.prototype = {

		/** @return void */
		addRemoveClass_: function( el, classValue, bool ) { // HTMLElement, string, bool
			el.classList[bool ? 'add' : 'remove']( classValue );
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
		handleResponse_: function( response, success ) { // object
			var wasSuccessful = success === true;
			if( response.recaptcha === 'unset' ) {
				this.recaptcha.execute_();
				return;
			}
			if( response.recaptcha === 'reset' ) {
				this.recaptcha.reset_();
			}
			if( wasSuccessful ) {
				this.recaptcha.reset_();
				this.form.reset();
			}
			this.showFieldErrors_( response.errors );
			this.showResults_( response.message, wasSuccessful );
			this.enableButton_();
			response.form = this.form;
			document.dispatchEvent( new CustomEvent( 'site-reviews/after/submission', { detail: response }));
			if( wasSuccessful && response.redirect !== '' ) {
				window.location = response.redirect;
			}
		},

		/** @return void */
		init_: function() {
			this.form.addEventListener( 'submit', this.onSubmit_.bind( this ));
			this.initStarRatings_();
			this.recaptcha.render_();
		},

		/** @return void */
		initStarRatings_: function() {
			var select = this.form.querySelector( 'select.glsr-star-rating' );
			if( select ) {
				new StarRating( select, {
					clearable: false,
					showText: false,
				});
			}
		},

		/** @return bool */
		isAjaxEnabled_: function() {
			var ajax = new GLSR.Ajax();
			var isUploadSupported = true;
			[].forEach.call( this.form.elements, function( el ) {
				if( el.type !== 'file' )return;
				isUploadSupported = ajax.isFileSupported() && ajax.isUploadSupported();
			});
			return isUploadSupported && !this.form.classList.contains( 'no-ajax' );
		},

		/** @return void */
		onSubmit_: function( ev ) { // HTMLEvent
			if( !this.validation.validate_() ) {
				ev.preventDefault();
				this.showResults_( this.strings.errors, false );
				return;
			}
			this.resetErrors_();
			if( !this.form['g-recaptcha-response'] || this.form['g-recaptcha-response'].value !== '' ) {
				if( !this.useAjax )return;
			}
			ev.preventDefault();
			this.submitForm_();
		},

		/** @return void */
		resetErrors_: function() {
			this.showResults_( '', true );
			this.validation.reset_();
		},

		/** @return void */
		showFieldErrors_: function( errors ) { // object
			if( !errors )return;
			for( var error in errors ) {
				if( !errors.hasOwnProperty( error ))continue;
				var nameSelector = GLSR.nameprefix ? GLSR.nameprefix + '[' + error + ']' : error;
				var inputEl = this.form.querySelector( '[name="' + nameSelector + '"]' );
				this.validation.setErrors_( inputEl, errors[error] );
				this.validation.toggleError_( inputEl.validation, 'add' );
			}
		},

		/** @return void */
		showResults_: function( message, success ) { // object, bool
			var resultsEl = this.form.querySelector( '.' + this.config.message_tag_class );
			if( resultsEl === null ) {
				resultsEl = document.createElement( this.config.message_tag );
				resultsEl.className = this.config.message_tag_class;
				this.button.parentNode.insertBefore( resultsEl, this.button.nextSibling );
			}
			this.addRemoveClass_( resultsEl, this.config.message_error_class, !success );
			this.addRemoveClass_( resultsEl, this.config.message_success_class, success );
			resultsEl.classList.remove( this.config.message_initial_class );
			resultsEl.innerHTML = message;
		},

		/** @return void */
		submitForm_: function( counter ) { // int|null
			var ajax = new GLSR.Ajax();
			if( !ajax.isFormDataSupported() ) {
				this.showResults_( this.strings.unsupported, false );
				return;
			}
			this.disableButton_();
			this.form[GLSR.nameprefix + '[_counter]'].value = counter || 0;
			(new GLSR.Ajax()).post( this.form, this.handleResponse_.bind( this ));
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
				form.init_();
			}
			this.forms.push( form );
		}
	};
})();
