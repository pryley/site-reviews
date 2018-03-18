/*!
 * Star Rating
 * @version: 2.1.0
 * @author: Paul Ryley (http://geminilabs.io)
 * @url: https://github.com/geminilabs/star-rating.js
 * @license: MIT
 */

/** global: define */

;(function( window, document, undefined ) {

	"use strict";

	/** @return array */
	var Plugin = function( selector, options ) { // string|object, object
		this.selects = {}.toString.call( selector ) === '[object String]' ? document.querySelectorAll( selector ) : [selector];
		this.destroy = function() {
			this.widgets.forEach( function( widget ) {
				widget.destroy();
			})
		};
		this.rebuild = function() {
			this.widgets.forEach( function( widget ) {
				widget.rebuild();
			})
		};
		this.widgets = [];
		for( var i = 0; i < this.selects.length; i++ ) {
			if( this.selects[i].tagName !== 'SELECT' )continue;
			var widget = new Widget( this.selects[i], options );
			if( widget.direction === undefined )continue;
			this.widgets.push( widget );
		}
	};

	/** @return void */
	var Widget = function( el, options ) { // HTMLElement, object
		this.el = el;
		this.options = this.extend( {}, this.defaults, options || {}, JSON.parse( el.getAttribute( 'data-options' ))),
		this.setStarCount();
		if( this.stars < 1 || this.stars > this.options.maxStars )return;
		this.init();
	}

	Widget.prototype = {

		defaults: {
			clearable: true,
			initialText: 'Select a Rating',
			maxStars: 10,
			onClick: null,
			showText: true,
		},

		/** @return void */
		init: function() {
			this.initEvents();
			this.current = this.selected = this.getSelectedValue();
			this.wrapEl();
			this.buildWidgetEl();
			this.setDirection();
			this.setValue( this.current );
			this.handleEvents( 'add' );
		},

		/** @return void */
		buildLabelEl: function() {
			if( !this.options.showText )return;
			this.textEl = this.insertSpanEl( this.widgetEl, {
				class: 'gl-star-rating-text',
			}, true );
		},

		/** @return void */
		buildWidgetEl: function() {
			var values = this.getOptionValues();
			var widgetEl = this.insertSpanEl( this.el, {
				class: 'gl-star-rating-stars',
			}, true );
			for( var key in values ) {
				if( !values.hasOwnProperty( key ))continue;
				var newEl = this.createSpanEl({
					'data-value': key,
					'data-text': values[key],
				});
				widgetEl.innerHTML += newEl.outerHTML;
			}
			this.widgetEl = widgetEl;
			this.buildLabelEl();
		},

		/** @return void */
		changeTo: function( index ) { // int
			if( index < 0 || index === '' ) {
				index = 0;
			}
			if( index > this.stars ) {
				index = this.stars;
			}
			this.widgetEl.classList.remove( 's' + ( 10 * this.current ));
			this.widgetEl.classList.add( 's' + ( 10 * index ));
			if( this.options.showText ) {
				this.textEl.textContent = index < 1 ? this.options.initialText : this.widgetEl.childNodes[index - 1].dataset.text;
			}
			this.current = index;
		},

		/** @return HTMLElement */
		createSpanEl: function( attributes ) { // object
			var el = document.createElement( 'span' );
			attributes = attributes || {};
			for( var key in attributes ) {
				if( !attributes.hasOwnProperty( key ))continue;
				el.setAttribute( key, attributes[key] );
			}
			return el;
		},

		/** @return void */
		destroy: function() {
			this.handleEvents( 'remove' );
			var wrapEl = this.el.parentNode;
			wrapEl.parentNode.replaceChild( this.el, wrapEl );
		},

		/** @return void */
		eventListener: function( el, action, events ) { // HTMLElement, string, array
			events.forEach( function( event ) {
				el[action + 'EventListener']( event, this.events[event] );
			}.bind( this ));
		},

		/** @return object */
		extend: function() { // object ...
			var args = [].slice.call( arguments );
			var result = args[0];
			var extenders = args.slice(1);
			Object.keys( extenders ).forEach( function( i ) {
				for( var key in extenders[i] ) {
					if( !extenders[i].hasOwnProperty( key ))continue;
					result[key] = extenders[i][key];
				}
			});
			return result;
		},

		/** @return int */
		getIndexFromPosition: function( pageX ) { // int
			var direction = {};
			var widgetWidth = this.widgetEl.offsetWidth;
			direction.ltr = Math.max( pageX - this.offsetLeft, 1 );
			direction.rtl = widgetWidth - direction.ltr;
			return Math.min(
				Math.ceil( direction[this.direction] / Math.round( widgetWidth / this.stars )),
				this.stars
			);
		},

		/** @return object */
		getOptionValues: function() {
			var el = this.el;
			var unorderedValues = {};
			var orderedValues = {};
			for( var i = 0; i < el.length; i++ ) {
				if( el[i].value === '' )continue;
				unorderedValues[el[i].value] = el[i].text;
			}
			Object.keys( unorderedValues ).sort().forEach( function( key ) {
				orderedValues[key] = unorderedValues[key];
			});
			return orderedValues;
		},

		/** @return int */
		getSelectedValue: function() {
			return parseInt( this.el.options[Math.max( this.el.selectedIndex, 0 )].value ) || 0;
		},

		/** @return void */
		handleEvents: function( action ) { // string
			var formEl = this.el.closest( 'form' );
			this.eventListener( this.el, action, ['change'] );
			this.eventListener( this.el, action, ['keydown'] );
			this.eventListener( this.widgetEl, action, ['click', 'mouseenter', 'mouseleave'] );
			if( formEl ) {
				this.eventListener( formEl, action, ['reset'] );
			}
		},

		/** @return void */
		initEvents: function() {
			this.events = {
				change: this.onChange.bind( this ),
				click: this.onClick.bind( this ),
				keydown: this.onKeydown.bind( this ),
				mouseenter: this.onMouseenter.bind( this ),
				mouseleave: this.onMouseleave.bind( this ),
				mousemove: this.onMousemove.bind( this ),
				reset: this.onReset.bind( this ),
			};
		},

		/** @return void */
		insertSpanEl: function( el, attributes, after ) { // HTMLElement, object, bool
			var newEl = this.createSpanEl( attributes );
			el.parentNode.insertBefore( newEl, after === true ? el.nextSibling : el );
			return newEl;
		},

		/** @return bool */
		isCloneable: function( obj ) { // mixed
			return Array.isArray( obj ) || {}.toString.call( obj ) == '[object Object]';
		},

		/** @return void */
		onChange: function() {
			this.changeTo( this.getSelectedValue() );
		},

		/** @return void */
		onClick: function( ev ) { // MouseEvent
			var index = this.getIndexFromPosition( ev.pageX );
			if( this.current !== 0 && parseFloat( this.selected ) === index  && this.options.clearable ) {
				return this.onReset();
			}
			this.setValue( index );
			if( typeof this.options.onClick === 'function' ) {
				this.options.onClick.call( this, this.el );
			}
		},

		/** @return void */
		onKeydown: function( ev ) { // KeyboardEvent
			if( ['ArrowLeft', 'ArrowRight'].indexOf( ev.key ) === -1 )return;
			var increment = ev.key === 'ArrowLeft' ? -1 : 1;
			if( this.direction === 'rtl' ) {
				increment *= -1;
			}
			this.setValue( Math.min( Math.max( this.getSelectedValue() + increment, 0 ), this.stars ));
		},

		/** @return void */
		onMouseenter: function() {
			var rect = this.widgetEl.getBoundingClientRect();
			this.offsetLeft = rect.left + document.body.scrollLeft;
			this.widgetEl.addEventListener( 'mousemove', this.events.mousemove );
		},

		/** @return void */
		onMouseleave: function() {
			this.widgetEl.removeEventListener( 'mousemove', this.events.mousemove );
			this.changeTo( this.selected );
		},

		/** @return void */
		onMousemove: function( ev ) { // MouseEvent
			this.changeTo( this.getIndexFromPosition( ev.pageX ));
		},

		/** @return void */
		onReset: function() {
			var originallySelected = this.el.querySelector( '[selected]' );
			var value = originallySelected ? originallySelected.value : '';
			this.el.value = value;
			this.selected = parseInt( value ) || 0;
			this.changeTo( value );
		},

		/** @return void */
		rebuild: function() {
			if( this.el.parentNode.classList.contains( 'gl-star-rating' )) {
				this.destroy();
			}
			this.init();
		},

		/** @return void */
		setDirection: function() {
			var wrapEl = this.el.parentNode;
			this.direction = window.getComputedStyle( wrapEl, null ).getPropertyValue( 'direction' );
			wrapEl.classList.add( 'gl-star-rating-' + this.direction );
		},

		/** @return void */
		setValue: function( index ) {
			this.el.value = index;
			this.selected = index;
			this.changeTo( index );
		},

		/** @return void */
		setStarCount: function() {
			var el = this.el;
			this.stars = 0;
			for( var i = 0; i < el.length; i++ ) {
				if( el[i].value === '' )continue;
				if( isNaN( parseFloat( el[i].value )) || !isFinite( el[i].value )) {
					this.stars = 0;
					return;
				}
				this.stars++;
			}
		},

		/** @return void */
		wrapEl: function() {
			var wrapEl = this.insertSpanEl( this.el, {
				class: 'gl-star-rating',
				'data-star-rating': '',
			});
			wrapEl.appendChild( this.el );
		},
	};

	if( typeof define === 'function' && define.amd ) {
		define( [], function() { return Plugin; });
	}
	else if( typeof module === 'object' && module.exports ) {
		module.exports = Plugin;
	}
	else {
		window.StarRating = Plugin;
	}

})( window, document );

var GLSR = {};

GLSR.addClass = function( el, className ) {
	if( el.classList ) {
		el.classList.add( className );
	}
	else if( !GLSR.hasClass( el, className )) {
		el.className += ' ' + className;
	}
};

GLSR.convertValue = function( value ) {
	if( GLSR.isNumeric( value )) {
		return parseFloat( value );
	}
	else if( value === 'true') {
		return true;
	}
	else if( value === 'false' ) {
		return false;
	}
	else if( value === '' || value === null ) {
		return undefined;
	}
	return value;
};

GLSR.getAjax = function( url, success ) {
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( 'Microsoft.XMLHTTP' );
	xhr.open( 'GET', url );
	xhr.onreadystatechange = function() {
		if( xhr.readyState > 3 && xhr.status === 200 ) {
			success( xhr.responseText );
		}
	};
	xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
	xhr.send();
	return xhr;
};

GLSR.hasClass = function( el, className ) {
	if( el.classList ) {
		return el.classList.contains( className );
	}
	return new RegExp( '\\b' + className + '\\b' ).test( el.className );
};

GLSR.inArray = function( needle, haystack ) {
	var length = haystack.length;
	while( length-- ) {
		if( haystack[ length ] === needle ) {
			return true;
		}
	}
	return false;
};

GLSR.isNumeric = function( value ) {
	return !( isNaN( parseFloat( value )) || !isFinite( value ));
};

GLSR.isString = function( str ) {
	return Object.prototype.toString.call( str ) === '[object String]';
};

GLSR.on = function( type, el, handler ) {
	if( GLSR.isString( el )) {
		el = document.querySelectorAll( el );
	}
	[].forEach.call( el, function( node ) {
		node.addEventListener( type, handler );
	});
};

GLSR.off = function( type, el, handler ) {
	if( GLSR.isString( el )) {
		el = document.querySelectorAll( el );
	}
	[].forEach.call( el, function( node ) {
		node.removeEventListener( type, handler );
	});
};

/**
 * Adapted from https://github.com/bitovi/jquerypp/blob/master/dom/form_params/form_params.js
 */
GLSR.parseFormData = function( form, convert ) {
	convert = !!convert || false;
	var keyBreaker = /[^\[\]]+/g; // used to parse bracket notation
	var data = {};
	var seen = {}; // used to uniquely track seen values
	var nestData = function( field, data, parts, seenName )
	{
		var name = parts.shift();
		// Keep track of the dot separated fullname
		seenName = seenName ? seenName + '.' + name : name;
		if( parts.length ) {
			if( !data[ name ] ) {
				data[ name ] = {};
			}
			// Recursive call
			nestData( field, data[ name ], parts, seenName );
		}
		else {
			// Convert the value
			var value = convert ? GLSR.convertValue( field.value ) : field.value;
			// Handle same name case, as well as "last checkbox checked" case
			if( seenName in seen && field.type !== 'radio' && !data[ name ].isArray()) {
				if( name in data ) {
					data[ name ] = [ data[name] ];
				}
				else {
					data[ name ] = [];
				}
			}
			else {
				seen[ seenName ] = true;
			}
			// Finally, assign data
			if( GLSR.inArray( field.type, ['radio','checkbox'] ) && !field.checked )return;

			if( !data[ name ] ) {
				data[ name ] = value;
			}
			else {
				data[ name ].push( value );
			}
		}
	};

	for( var i = 0; i < form.length; i++ ) {
		var field = form[i];
		if( !field.name || field.disabled || GLSR.inArray( field.type, [
			'file','reset','submit','button',
		]))continue;
		var parts = field.name.match( keyBreaker );
		if( !parts.length ) {
			parts = [ field.name ];
		}
		nestData( field, data, parts );
	}
	return data;
};

GLSR.postAjax = function( url, data, success ) {
	var params = typeof data !== 'string' ? GLSR.serialize( data ) : data;
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( 'Microsoft.XMLHTTP' );
	xhr.open( 'POST', url ); // asynchronously
	xhr.onreadystatechange = function() {
		if( xhr.readyState > 3 && xhr.status === 200 ) {
			success( JSON.parse( xhr.responseText ));
		}
	};
	xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
	xhr.send( params );
	return xhr;
};

GLSR.ready = function( fn ) {
	if( typeof fn !== 'function' )return;
	// in case the document is already rendered
	if( document.readyState !== 'loading' ) {
		fn();
	}
	// modern browsers
	else if( document.addEventListener ) {
		document.addEventListener( 'DOMContentLoaded', fn );
	}
	// IE <= 8
	else {
		document.attachEvent( 'onreadystatechange', function() {
			if( document.readyState === 'complete' ) {
				fn();
			}
		});
	}
};

GLSR.removeClass = function( el, className ) {
	if( el.classList ) {
		el.classList.remove( className );
	}
	else {
		el.className = el.className.replace( new RegExp( '\\b' + className + '\\b', 'g' ), '' );
	}
};

GLSR.serialize = function( obj, prefix ) {
	var str = [];

	for( var property in obj ) {
		if( !obj.hasOwnProperty( property ))continue;
		var key = prefix ? prefix + '[' + property + ']' : property;
		var value = obj[ property ];
		str.push( typeof value === 'object' ?
			GLSR.serialize( value, key ) :
			encodeURIComponent( key ) + '=' + encodeURIComponent( value )
		);
	}
	return str.join( '&' );
};

GLSR.toggleClass = function( el, className ) {
	if( !GLSR.hasClass( el, className )) GLSR.addClass( el, className );
	else GLSR.removeClass( el, className );
};

GLSR.insertAfter = function( el, tag, attributes ) {
	var newEl = GLSR.createEl( tag, attributes );
	el.parentNode.insertBefore( newEl, el.nextSibling );
	return newEl;
};

GLSR.appendTo = function( el, tag, attributes ) {
	var newEl = GLSR.createEl( tag, attributes );
	el.appendChild( newEl );
	return newEl;
};

GLSR.createEl = function( tag, attributes ) {
	var el = ( typeof tag === 'string' ) ? document.createElement( tag ) : tag;
	attributes = attributes || {};
	for( var key in attributes ) {
		if( !attributes.hasOwnProperty( key ) )continue;
		el.setAttribute( key, attributes[ key ] );
	}
	return el;
};

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
	var ratings = document.querySelectorAll( 'select.glsr-star-rating' );
	for( var i = 0; i < ratings.length; i++ ) {
		new StarRating( ratings[i], {
			clearable: false,
			showText : false,
			onClick  : GLSR.clearFieldError,
		});
	}
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

GLSR.now = function()
{
	return ( window.performance && window.performance.now ) ? window.performance.now() : Date.now();
};

GLSR.onClickPagination = function( ev )
{
	ev.preventDefault();
	var parentEl = this.closest( '.glsr-reviews' );
	var parentSelector = GLSR.getSelectorOfElement( parentEl );
	GLSR.addClass( parentEl, 'glsr-hide' );
	GLSR.getAjax( this.href, function( response ) {
		var html = document.implementation.createHTMLDocument( 'new' );
		html.documentElement.innerHTML = response;
		var newParentEl = parentSelector ? html.querySelectorAll( parentSelector ) : '';
		if( newParentEl.length === 1 ) {
			parentEl.innerHTML = newParentEl[0].innerHTML;
			GLSR.scrollToTop( parentEl );
			GLSR.removeClass( parentEl, 'glsr-hide' );
			GLSR.on( 'click', '.glsr-ajax-navigation a', GLSR.onClickPagination );
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
	GLSR.toggleClass( hiddenNode, 'glsr-hidden' );
	GLSR.toggleClass( hiddenNode, 'glsr-visible' );
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
		startTime: GLSR.now(),
		startY: el.scrollTop,
	});
};

GLSR.scrollToTopStep = function( context )
{
	var elapsed = ( GLSR.now() - context.startTime ) / GLSR.SCROLL_TIME;
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
		else {
			GLSR.submitForm( token.value );
		}
	};
});

GLSR.on( 'click', '.glsr-ajax-navigation a', GLSR.onClickPagination );

GLSR.ready( function()
{
	GLSR.createExceprts();
	GLSR.createStarRatings();
});
