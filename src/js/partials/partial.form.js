/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, CustomEvent, GLSR, grecaptcha, HTMLFormElement, site_reviews, StarRating */

"use strict";

GLSR.activeForm = null;

GLSR.buildFormData = function( recaptchaToken )
{
	if( recaptchaToken === undefined ) {
		recaptchaToken = '';
	}
	// console.log( 'recaptchaToken: ' + recaptchaToken );
	return {
		action: site_reviews.action,
		request: GLSR.parseFormData( GLSR.activeForm ),
		'g-recaptcha-response': recaptchaToken,
	};
};

GLSR.clearFieldError = function( el )
{
	var fieldEl = el.closest( '.glsr-field' );
	if( fieldEl === null )return;
	var errorEl = fieldEl.querySelector( '.glsr-field-errors' );
	GLSR.removeClass( fieldEl, 'glsr-has-error' );
	if( errorEl !== null ) {
		errorEl.parentNode.removeChild( errorEl );
	}
};

GLSR.clearFormErrors = function()
{
	var formEl = GLSR.activeForm;
	GLSR.clearFormMessages();
	for( var i = 0; i < formEl.length; i++ ) {
		GLSR.clearFieldError( formEl[i] );
	}
};

GLSR.clearFormMessages = function()
{
	var messageEl = GLSR.activeForm.querySelector( '.glsr-form-messages' );
	if( messageEl ) {
		messageEl.innerHTML = '';
	}
};

GLSR.enableSubmitButton = function()
{
	GLSR.activeForm.querySelector( '[type="submit"]' ).removeAttribute( 'disabled' );
};

GLSR.showFormErrors = function( errors )
{
	if( !errors )return;
	var fieldEl, errorsEl;
	for( var error in errors ) {
		if( !errors.hasOwnProperty( error ))continue;
		fieldEl = GLSR.activeForm.querySelector( '[name="' + error + '"]' ).closest( '.glsr-field' );
		GLSR.addClass( fieldEl, 'glsr-has-error' );
		errorsEl = fieldEl.querySelector( '.glsr-field-errors' );
		if( errorsEl === null ) {
			errorsEl = GLSR.appendTo( fieldEl, 'span', {
				'class': 'glsr-field-errors',
			});
		}
		for( var i = 0; i < errors[ error ].errors.length; i++ ) {
			if( errors[ error ].errors[i] === null )continue;
			errorsEl.innerHTML += '<span class="glsr-field-error">' + errors[ error ].errors[i] + '</span>';
		}
	}
};

GLSR.showFormMessage = function( response )
{
	var formIdEl  = GLSR.activeForm.querySelector( 'input[name="form_id"]' );
	var messageEl = GLSR.activeForm.querySelector( '.glsr-form-messages' );
	if( messageEl === null ) {
		messageEl = GLSR.insertAfter( formIdEl, 'div', {
			'class': 'glsr-form-messages',
		});
	}
	if( !!response.errors ) {
		GLSR.addClass( messageEl, 'gslr-has-errors' );
	}
	else {
		GLSR.removeClass( messageEl, 'gslr-has-errors' );
	}
	messageEl.innerHTML = '<p>' + response.message + '</p>';
};

GLSR.submitForm = function( recaptchaToken )
{
	GLSR.activeForm.querySelector( '[type="submit"]' ).setAttribute( 'disabled', '' );
	GLSR.postAjax( site_reviews.ajaxurl, GLSR.buildFormData( recaptchaToken ), function( response ) {
		// console.log( response );
		if( response.recaptcha === true ) {
			// console.log( 'executing recaptcha' );
			return GLSR.recaptcha.execute();
		}
		if( response.recaptcha === 'reset' ) {
			// console.log( 'reseting failed recaptcha' );
			GLSR.recaptcha.reset();
		}
		if( response.errors === false ) {
			// console.log( 'reseting recaptcha' );
			GLSR.recaptcha.reset();
			GLSR.activeForm.reset();
		}
		// console.log( 'submission finished' );
		GLSR.showFormErrors( response.errors );
		GLSR.showFormMessage( response );
		GLSR.enableSubmitButton();
		response.form = GLSR.activeForm;
		document.dispatchEvent( new CustomEvent( 'site-reviews/after/submission', { detail: response }));
		GLSR.activeForm = null;
	});
};

GLSR.on( 'change', 'form.glsr-submit-review-form', function( ev )
{
	GLSR.clearFieldError( ev.target );
});

GLSR.on( 'submit', 'form.glsr-submit-review-form', function( ev )
{
	if( GLSR.hasClass( this, 'no-ajax' ))return;
	ev.preventDefault();
	GLSR.activeForm = this;
	GLSR.recaptcha.addListeners();
	GLSR.clearFormErrors();
	GLSR.submitForm();
});

/**
 * This event function exists to undo the mayhem caused by the invisible-recaptcha plugin
 * This function is triggered on the invisible-recaptcha callback
 */
GLSR.on( 'click', '.glsr-field [type="submit"]', function()
{
	this.closest( 'form' ).onsubmit = null;
	HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
	HTMLFormElement.prototype.submit = function() {
		var token = this.querySelector( '#g-recaptcha-response' );
		if( null === token || null === this.querySelector( '.glsr-field' )) {
			this._submit();
		}
		GLSR.submitForm( token.value );
	};
});

