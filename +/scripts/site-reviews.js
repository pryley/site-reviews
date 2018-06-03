/** global: GLSR, StarRating */
GLSR.SCROLL_TIME = 468;
GLSR.activeForm = null;
GLSR.recaptcha = {};

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
	fieldEl.classList.remove( 'glsr-has-error' );
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

GLSR.createExceprts = function( parentEl )
{
	parentEl = parentEl || document;
	var excerpts = parentEl.querySelectorAll( '.glsr-hidden-text' );
	for( var i = 0; i < excerpts.length; i++ ) {
		var readmore = GLSR.insertAfter( excerpts[i], 'span', {
			'class': 'glsr-read-more',
		});
		var readmoreLink = GLSR.appendTo( readmore, 'a', {
			'href': '#',
			'data-text': excerpts[i].getAttribute( 'data-show-less' ),
		});
		readmoreLink.innerHTML = excerpts[i].getAttribute( 'data-show-more' );
	}
	GLSR.on( 'click', '.glsr-read-more a', GLSR.onClickReadMore );
};

GLSR.createStarRatings = function()
{
	new StarRating( 'select.glsr-star-rating', {
		clearable: false,
		showText: false,
		onClick: GLSR.clearFieldError,
	});
};

GLSR.enableSubmitButton = function()
{
	GLSR.activeForm.querySelector( '[type="submit"]' ).removeAttribute( 'disabled' );
};

GLSR.getSelectorOfElement = function( el )
{
	if( !el || el.nodeType !== el.ELEMENT_NODE )return;
	return el.nodeName.toLowerCase() +
		( el.id ? '#' + el.id.trim() : '' ) +
		( el.className ? '.' + el.className.trim().replace( /\s+/g, '.' ) : '' );
};

GLSR.onClickPagination = function( ev )
{
	ev.preventDefault();
	var parentEl = this.closest( '.glsr-reviews' );
	var parentSelector = GLSR.getSelectorOfElement( parentEl );
	parentEl.classList.add( 'glsr-hide' );
	GLSR.getAjax( this.href, function( response ) {
		var html = document.implementation.createHTMLDocument( 'new' );
		html.documentElement.innerHTML = response;
		var newParentEl = parentSelector ? html.querySelectorAll( parentSelector ) : '';
		if( newParentEl.length === 1 ) {
			parentEl.innerHTML = newParentEl[0].innerHTML;
			GLSR.scrollToTop( parentEl );
			parentEl.classList.remove( 'glsr-hide' );
			GLSR.on( 'click', '.glsr-ajax-pagination .glsr-navigation a', GLSR.onClickPagination );
			window.history.pushState( null, '', ev.target.href );
			GLSR.createExceprts( parentEl );
			return;
		}
		window.location = ev.target.href;
	});
};

GLSR.onClickReadMore = function( ev )
{
	ev.preventDefault();
	var el = ev.target;
	var hiddenNode = el.parentNode.previousSibling;
	var text = el.getAttribute( 'data-text' );
	hiddenNode.classList.toggle( 'glsr-hidden' );
	hiddenNode.classList.toggle( 'glsr-visible' );
	el.setAttribute( 'data-text', el.innerText );
	el.innerText = text;
};

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
		for( var obj in value) {
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

GLSR.setDirection = function()
{
	var widgets = document.querySelectorAll( '.glsr-widget, .glsr-shortcode' );
	for( var i = 0; i < widgets.length; i++ ) {
		var direction = window.getComputedStyle( widgets[i], null ).getPropertyValue( 'direction' );
		widgets[i].classList.add( 'glsr-' + direction );
	}
};

GLSR.scrollToTop = function( el, offset )
{
	offset = offset || 16; // 1rem
	var fixedElement;
	for( var i = 0; i < site_reviews.ajaxpagination.length; i++ ) {
		fixedElement = document.querySelector( site_reviews.ajaxpagination[i] );
		if( fixedElement && window.getComputedStyle( fixedElement ).getPropertyValue( 'position' ) === 'fixed' ) {
			offset = offset + fixedElement.clientHeight;
		}
	}
	var clientBounds = el.getBoundingClientRect();
	var offsetTop = clientBounds.top - offset;
	if( offsetTop > 0 )return; // if top is in view, don't scroll!
	if( 'requestAnimationFrame' in window === false ) {
		window.scroll( 0, window.pageYOffset + offsetTop );
		return;
	}
	GLSR.scrollToTopStep({
		endY: offsetTop,
		offset: window.pageYOffset,
		startTime: window.performance.now(),
		startY: el.scrollTop,
	});
};

GLSR.scrollToTopStep = function( context )
{
	var elapsed = ( window.performance.now() - context.startTime ) / GLSR.SCROLL_TIME;
	elapsed = elapsed > 1 ? 1 : elapsed;
	var easedValue = 0.5 * ( 1 - Math.cos( Math.PI * elapsed ));
	var currentY = context.startY + ( context.endY - context.startY ) * easedValue;
	window.scroll( 0, context.offset + currentY );
	if( currentY !== context.endY ) {
		window.requestAnimationFrame( GLSR.scrollToTopStep.bind( window, context ));
	}
};

GLSR.showFormErrors = function( errors )
{
	if( !errors )return;
	var fieldEl, errorsEl;
	for( var error in errors ) {
		if( !errors.hasOwnProperty( error ))continue;
		fieldEl = GLSR.activeForm.querySelector( '[name="' + error + '"]' ).closest( '.glsr-field' );
		fieldEl.classList.add( 'glsr-has-error' );
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
		messageEl.classList.add( 'gslr-has-errors' );
	}
	else {
		messageEl.classList.remove( 'gslr-has-errors' );
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
	if( this.classList.contains( 'no-ajax' ))return;
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
		else {
			GLSR.submitForm( token.value );
		}
	};
});

GLSR.on( 'click', '.glsr-ajax-pagination .glsr-navigation a', GLSR.onClickPagination );

GLSR.ready( function()
{
	GLSR.setDirection();
	GLSR.createExceprts();
	GLSR.createStarRatings();
});
