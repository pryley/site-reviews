/** global: GLSR, grecaptcha, HTMLFormElement, site_reviews */
;(function( window, document, GLSR, undefined ) {

	'use strict';

	var Form = function( formEl, buttonEl ) { // HTMLElement, HTMLElement
		this.button = buttonEl;
		this.form = formEl;
		this.init();
	};

	Form.prototype = {

		config: {
			fieldErrorsClass: 'glsr-field-errors',
			fieldSelector: '.glsr-field',
			formMessagesClass: 'glsr-form-messages',
			hasErrorClass: 'glsr-has-error',
		},

		/** @return void */
		addRemoveClass: function( el, classValue, bool ) { // HTMLElement, string, bool
			el.classList[bool ? 'add' : 'remove']( classValue );
		},

		/** @return void */
		clearFieldError: function( el ) { // HTMLElement
			var fieldEl = el.closest( this.config.fieldSelector );
			if( fieldEl === null )return;
			fieldEl.classList.remove( this.config.hasErrorClass );
			var errorEl = fieldEl.querySelector( this.config.fieldErrorsSelector );
			if( errorEl !== null ) {
				errorEl.parentNode.removeChild( errorEl );
			}
		},

		/** @return void */
		clearFormErrors: function() {
			this.getResultsEl().innerHTML = '';
			for( var i = 0; i < this.form.length; i++ ) {
				this.clearFieldError( this.form[i] );
			}
		},

		/** @return void */
		disableButton: function() {
			this.button.setAttribute( 'disabled', '' );
		},

		/** @return void */
		enableButton: function() {
			this.button.removeAttribute( 'disabled' );
		},

		/** @return void */
		fallbackSubmit: function() {
			if( this.isAjaxUploadSupported() && this.isFileAPISupported() && this.isFormDataSupported() )return;
			this.form.submit();
		},

		/** @return void */
		handleResponse: function( response ) { // object
			console.log( response );
			if( response.recaptcha === true ) {
				console.log( 'executing recaptcha' );
				return this.recaptchaExecute();
			}
			if( response.recaptcha === 'reset' ) {
				console.log( 'reseting failed recaptcha' );
				this.recaptchaReset();
			}
			if( response.errors === false ) {
				console.log( 'reseting recaptcha' );
				GLSR.recaptchaReset();
				this.form.reset();
			}
			console.log( 'submission finished' );
			this.showFieldErrors( response.errors );
			this.showResults( response );
			this.enableButton();
			response.form = this.form;
			document.dispatchEvent( new CustomEvent( 'site-reviews/after/submission', { detail: response }));
		},

		/** @return bool */
		isAjaxUploadSupported: function() {
			var xhr = new XMLHttpRequest();
			return !!( xhr && ( 'upload' in xhr ) && ( 'onprogress' in xhr.upload ));
		},

		/** @return bool */
		isFileAPISupported: function() {
			var fi = document.createElement('INPUT');
			fi.type = 'file';
			return 'files' in fi;
		},

		/** @return bool */
		isFormDataSupported: function() {
			return !!window.FormData;
		},

		/** @return HTMLDivElement */
		getFieldErrorsEl: function( fieldEl ) { // HTMLElement
			var errorsEl = fieldEl.querySelector( '.' + this.config.fieldErrorsClass );
			if( errorsEl === null ) {
				errorsEl = document.createElement( 'div' );
				errorsEl.setAttribute( 'class', this.config.fieldErrorsClass );
				fieldEl.appendChild( errorsEl );
			}
			return errorsEl;
		},

		/** @return object */
		getFormData: function( recaptchaToken ) { // string|null
			if( recaptchaToken === undefined ) {
				recaptchaToken = '';
			}
			return {
				action: site_reviews.action,
				request: new FormData( this.form ),
				'g-recaptcha-response': recaptchaToken,
			};
		},

		/** @return HTMLDivElement */
		getResultsEl: function() {
			var resultsEl = this.form.querySelector( '.' + this.config.formMessagesClass );
			if( resultsEl === null ) {
				resultsEl = document.createElement( 'div' );
				resultsEl.setAttribute( 'class', this.config.formMessagesClass );
				this.button.parentNode.insertBefore( resultsEl, this.button.nextSibling );
			}
			return resultsEl;
		},

		/** @return void */
		init: function() {
			this.button.addEventListener( 'click', this.onClick.bind( this ));
			this.form.addEventListener( 'change', this.onChange.bind( this ));
			this.form.addEventListener( 'submit', this.onSubmit.bind( this ));
			this.initStarRatings();
		},

		/** @return void */
		initStarRatings: function() {
			new StarRating( 'select.glsr-star-rating', {
				clearable: false,
				showText: false,
				onClick: this.clearFieldError(),
			});
		},

		/** @return void */
		onChange: function( ev ) { // Event
			this.clearFieldError( ev.target );
		},

		/**
		 * This event method handles the mayhem caused by the invisible-recaptcha plugin
		 * and is triggered on the invisible-recaptcha callback
		 * @return void */
		onClick: function() {
			var form = this;
			this.form.onsubmit = null;
			HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
			HTMLFormElement.prototype.submit = function() {
				var token = this.querySelector( '#g-recaptcha-response' );
				if( null !== token && this.querySelector( form.config.fieldSelector )) {
					form.submitForm( token.value );
					return;
				}
				this._submit();
			};
		},

		/** @return void */
		onSubmit: function( ev ) { // HTMLEvent
			if( this.form.classList.contains( 'no-ajax' ))return;
			ev.preventDefault();
			this.recaptchaAddListeners();
			this.clearFormErrors();
			this.submitForm();
		},

		/** @return void */
		recaptchaAddListeners: function() {
			var overlayEl = this.recaptchaGetOverlay();
			if( overlayEl === -1 )return;
			overlayEl.addEventListener( 'click', this.enableButton.bind( this ));
			window.addEventListener( 'keyup', this.recaptchaOnKeyup.bind( this, overlayEl ));
		},

		/** @return void */
		recaptchaExecute: function() {
			var recaptchaId = this.recaptchaGetId();
			if( recaptchaId !== -1 ) {
				grecaptcha.execute( recaptchaId );
				return;
			}
			// recaptcha ID not found so pass through an error
			this.submitForm( false );
		},

		/** @return string|int (-1) */
		recaptchaGetId: function() {
			return this.recaptchaSearch( function( value, id ) {
				if( Object.prototype.toString.call( value ) !== '[object HTMLDivElement]' )return;
				if( value.closest( 'form' ) === this.form ) {
					return id;
				}
			});
		},

		/** @return HTMLDivElement|int (-1) */
		recaptchaGetOverlay: function() {
			return this.recaptchaSearch( function( value ) {
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
		recaptchaOnKeyup: function( ev ) { // KeyboardEvent
			if( ev.keyCode !== 27 )return;
			this.enableButton();
			this.recaptchaRemoveListeners( ev.target );
		},

		/** @return void */
		recaptchaRemoveListeners: function( overlayEl ) { // HTMLDivElement
			overlayEl.removeEventListener( 'click', this.enableButton );
			window.removeEventListener( 'keyup', this.recaptchaOnKeyup );
		},

		/** @return void */
		recaptchaReset: function() {
			var recaptchaId = this.recaptchaGetId();
			if( recaptchaId !== -1 ) {
				grecaptcha.reset( recaptchaId );
			}
		},

		/** @return mixed|int (-1) */
		recaptchaSearch: function( callback ) { // function
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
		postAjax: function( formData, success ) {
			var xhr = new XMLHttpRequest();
			xhr.open( 'POST', site_reviews.ajaxurl );
			xhr.onreadystatechange = function() {
				if( xhr.readyState !== 4 )return;
				success( JSON.parse( xhr.responseText )).bind( this );
			}.bind( this );
			xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
			xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
			xhr.send( formData );
		},

		/** @return void */
		showFieldErrors: function( errors ) { // object
			if( !errors )return;
			var fieldEl, errorsEl;
			for( var error in errors ) {
				if( !errors.hasOwnProperty( error ))continue;
				fieldEl = this.form.querySelector( '[name=' + error + ']' ).closest( this.config.fieldSelector );
				fieldEl.classList.add( this.config.hasErrorClass );
				errorsEl = this.getFieldErrorsEl( fieldEl );
				for( var i = 0; i < errors[error].errors.length; i++ ) {
					errorsEl.innerHTML += errors[error].errors[i];
				}
			}
		},

		/** @return void */
		showResults: function( response ) { // object
			var resultsEl = this.getResultsEl();
			this.addRemoveClass( resultsEl, 'gslr-has-errors', !!response.errors );
			resultsEl.innerHTML = response.message;
		},

		/** @return void */
		submitForm: function( recaptchaToken ) { // string|null
			this.disableButton();
			this.fallbackSubmit();
			this.postAjax( this.getFormData( recaptchaToken ), this.handleResponse );
		},
	};

	GLSR.Forms = function() { // object
		this.nodeList = document.querySelectorAll( 'form.glsr-form' );
		this.forms = [];
		for( var i = 0; i < this.nodeList.length; i++ ) {
			var submitButton = this.nodeList[i].querySelector( '[type=submit]' );
			if( !submitButton )continue;
			this.forms.push( new Form( this.nodeList[i], submitButton ));
		}
	};

})( window, document, GLSR );
