// jshint unused:false
var glsr_render_recaptcha = function() {
	var id;
	var recaptchaEl;
	[].forEach.call( document.querySelectorAll( '.glsr-submit-review-form' ), function( formEl ) {
		formEl.onsubmit = null;
		recaptchaEl = formEl.querySelector( '.glsr-recaptcha-holder' );
		if( !recaptchaEl )return;
		recaptchaEl.innerHTML = '';
		id = grecaptcha.render( recaptchaEl, {
			callback: function( token ) {
				GLSR.submitForm( token );
			},
			'expired-callback': function() {
				grecaptcha.reset( id );
			},
		}, true );
		// recaptchaEl.setAttribute( 'data-id', id );
	});
};
