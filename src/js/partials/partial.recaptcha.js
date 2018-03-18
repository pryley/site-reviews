/* jshint browser:true, globalstrict:true, esversion:6 */
/* global console, CustomEvent, GLSR, grecaptcha, HTMLFormElement, site_reviews, StarRating */

"use strict";

GLSR.recaptcha = {};

GLSR.recaptcha.addListeners = function()
{
	var overlayEl = GLSR.recaptcha.overlay();
	if( Object.prototype.toString.call( overlayEl ) === '[object HTMLDivElement]' ) {
		overlayEl.addEventListener( 'click', GLSR.enableSubmitButton, false );
		window.addEventListener( 'keyup', GLSR.recaptcha.onKeyup.bind( overlayEl ), false );
	}
};

GLSR.recaptcha.execute = function()
{
	var recaptchaId = GLSR.recaptcha.id();
	if( recaptchaId !== -1 ) {
		return grecaptcha.execute( recaptchaId );
	}
	// recaptcha ID not found so pass through an error
	return GLSR.submitForm( false );
};

GLSR.recaptcha.id = function()
{
	return GLSR.recaptcha.search( function( value, id ) {
		if( Object.prototype.toString.call( value ) !== '[object HTMLDivElement]' )return;
		if( value.closest( 'form' ) === GLSR.activeForm ) {
			return id;
		}
	});
};

GLSR.recaptcha.onKeyup = function( ev )
{
	if( ev.keyCode !== 27 )return;
	GLSR.enableSubmitButton();
	GLSR.recaptcha.removeListeners( this );
};

GLSR.recaptcha.overlay = function()
{
	return GLSR.recaptcha.search( function( value ) {
		if( Object.prototype.toString.call( value ) !== '[object Object]' )return;
		for( var obj in value ) {
			if( !value.hasOwnProperty( obj ) || Object.prototype.toString.call( value[obj] ) !== '[object HTMLDivElement]' )continue;
			if( value[obj].className === '' ) {
				return value[obj].firstChild;
			}
		}
		return false;
	});
};

GLSR.recaptcha.removeListeners = function( overlayEl )
{
	overlayEl.removeEventListener( 'click', GLSR.enableSubmitButton, false );
	window.removeEventListener( 'keyup', GLSR.recaptcha.onKeyup, false );
};

GLSR.recaptcha.reset = function()
{
	var recaptchaId = GLSR.recaptcha.id();
	if( recaptchaId !== -1 ) {
		grecaptcha.reset( recaptchaId );
	}
};

GLSR.recaptcha.search = function( callback )
{
	var result = -1;
	if( window.hasOwnProperty( '___grecaptcha_cfg' )) {
		var clients = window.___grecaptcha_cfg.clients;
		var i, key;
		for( i in clients ) {
			for( key in clients[i] ) {
				if( !( result = callback( clients[i][key], i )))continue;
				return result;
			}
		}
	}
	return result;
};
