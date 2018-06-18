/*!
 * Star Rating
 * @version: 2.1.1
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
			});
		};
		this.rebuild = function() {
			this.widgets.forEach( function( widget ) {
				widget.rebuild();
			});
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
	var Widget = function( el, options ) { // HTMLElement, object|null
		this.el = el;
		this.options = this.extend( {}, this.defaults, options || {}, JSON.parse( el.getAttribute( 'data-options' )));
		this.setStarCount();
		if( this.stars < 1 || this.stars > this.options.maxStars )return;
		this.init();
	};

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
		extend: function() { // ...object
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
			this.eventListener( this.el, action, ['change', 'keydown'] );
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
			if( this.current !== 0 && parseFloat( this.selected ) === index && this.options.clearable ) {
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

/** global: GLSR, XMLHttpRequest */
;(function() {

	'use strict';

	var Ajax = function() {
		this.get = this.get_;
		this.isFileAPISupported = this.isFileAPISupported_;
		this.isFormDataSupported = this.isFormDataSupported_;
		this.isUploadSupported = this.isUploadSupported_;
		this.post = this.post_;
	};

	Ajax.prototype = {
		/** @return void */
		get_: function( url, successCallback, headers ) {
			this.xhr = new XMLHttpRequest();
			this.xhr.open( 'GET', url );
			this.xhr.onreadystatechange = function() {
				if( this.xhr.readyState !== 4 || this.xhr.status !== 200 )return;
				successCallback( this.xhr.responseText );
			}.bind( this );
			this.setHeaders_( headers );
			this.xhr.send();
		},

		/** @return bool */
		isFileAPISupported_: function() {
			var input = document.createElement( 'INPUT' );
			input.type = 'file';
			return 'files' in input;
		},

		/** @return bool */
		isFormDataSupported_: function() {
			return !!window.FormData;
		},

		/** @return bool */
		isUploadSupported_: function() {
			var xhr = new XMLHttpRequest();
			return !!( xhr && ( 'upload' in xhr ) && ( 'onprogress' in xhr.upload ));
		},

		/** @return FormData */
		buildFormData_: function( formData, data, parentKey ) {
			if( typeof data !== 'object' || data instanceof Date || data instanceof File ) {
				formData.append( parentKey, data || '' );
			}
			else {
				Object.keys( data ).forEach( function( key ) {
					if( !data.hasOwnProperty( key ))return;
					formData = this.buildFormData_( formData, data[key], parentKey ? parentKey[key] : key );
				}.bind( this ));
			}
			return formData;
		},

		/** @return FormData */
		normalizeData_: function( data ) { // object
			var formData = new FormData( data );
			if( Object.prototype.toString.call( data ) !== '[object HTMLFormElement]' ) {
				formData = this.buildFormData_( formData, data );
			}
			formData.append( 'action', GLSR.action );
			formData.append( 'ajax_request', true );
			return formData;
		},

		/** @return void */
		post_: function( formOrData, successCallback, headers ) {
			this.xhr = new XMLHttpRequest();
			this.xhr.open( 'POST', GLSR.ajaxurl );
			this.setHeaders_( headers );
			this.xhr.send( this.normalizeData_( formOrData ));
			this.xhr.onreadystatechange = function() {
				if( this.xhr.readyState !== XMLHttpRequest.DONE || this.xhr.status !== 200 )return;
				successCallback( JSON.parse( this.xhr.responseText ));
			}.bind( this );
		},

		/** @return void */
		setHeaders_: function( headers ) {
			headers = headers || {};
			headers['X-Requested-With'] = 'XMLHttpRequest';
			for( var key in headers ) {
				if( !headers.hasOwnProperty( key ))continue;
				this.xhr.setRequestHeader( key, headers[key] );
			}
		},
	};

	GLSR.Ajax = Ajax;
})();

/** global: GLSR */
;(function() {

	'use strict';

	var Excerpts = function( el ) { // HTMLElement
		this.init_( el || document );
	};

	Excerpts.prototype = {
		config: {
			hiddenClass: 'glsr-hidden',
			hiddenTextSelector: '.glsr-hidden-text',
			readMoreClass: 'glsr-read-more',
			visibleClass: 'glsr-visible',
		},

		/** @return void */
		createLinks_: function( el ) { // HTMLElement
			var readMoreSpan = document.createElement( 'span' );
			var readmoreLink = document.createElement( 'a' );
			readmoreLink.setAttribute( 'href', '#' );
			readmoreLink.setAttribute( 'data-text', el.getAttribute( 'data-show-less' ));
			readmoreLink.innerHTML = el.getAttribute( 'data-show-more' );
			readmoreLink.addEventListener( 'click', this.onClick_.bind( this ));
			readMoreSpan.setAttribute( 'class', this.config.readMoreClass );
			readMoreSpan.appendChild( readmoreLink );
			el.parentNode.insertBefore( readMoreSpan, el.nextSibling );
		},

		/** @return void */
		onClick_: function( ev ) { // MouseEvent
			ev.preventDefault();
			var el = ev.target;
			var hiddenNode = el.parentNode.previousSibling;
			var text = el.getAttribute( 'data-text' );
			hiddenNode.classList.toggle( this.config.hiddenClass );
			hiddenNode.classList.toggle( this.config.visibleClass );
			el.setAttribute( 'data-text', el.innerText );
			el.innerText = text;
		},

		init_: function( el ) { // HTMLElement
			var excerpts = el.querySelectorAll( this.config.hiddenTextSelector );
			for( var i = 0; i < excerpts.length; i++ ) {
				this.createLinks_( excerpts[i] );
			}
		},
	};

	GLSR.Excerpts = Excerpts;
})();

/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */
;(function() {

	'use strict';

	var Form = function( formEl, buttonEl ) { // HTMLElement, HTMLElement
		this.button = buttonEl;
		this.enableButton = this.enableButton_.bind( this );
		this.form = formEl;
		this.init = this.init_.bind( this );
		this.recaptcha = new GLSR.Recaptcha( this );
		this.submitForm = this.submitForm_.bind( this );
	};

	Form.prototype = {
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
			var tokenEl = this.form.querySelector( '#recaptcha-token' );
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
		handleResponse_: function( response ) { // object
			console.log( response );
			if( response.recaptcha === true ) {
				console.log( 'executing recaptcha' );
				this.recaptcha.execute();
				return;
			}
			if( response.recaptcha === 'reset' ) {
				console.log( 'resetting failed recaptcha' );
				this.recaptcha.reset();
			}
			if( response.errors === false ) {
				console.log( 'resetting recaptcha' );
				this.recaptcha.reset();
				this.form.reset();
			}
			console.log( 'submission finished' );
			this.showFieldErrors_( response.errors );
			this.showResults_( response );
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
			console.log( 'onChange_' );
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
				var token = this.querySelector( '#g-recaptcha-response' );
				if( null !== token && this.querySelector( form.config.fieldSelector )) {
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
			this.recaptcha.addListeners();
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
		showResults_: function( response ) { // object
			var resultsEl = this.getResultsEl_();
			this.addRemoveClass_( resultsEl, 'gslr-has-errors', !!response.errors );
			resultsEl.innerHTML = response.message;
		},

		/** @return void */
		submitForm_: function( recaptchaToken ) { // string|null
			this.disableButton_();
			this.fallbackSubmit_();
			(new GLSR.Ajax()).post( this.getForm_( recaptchaToken ), this.handleResponse_.bind( this ), {
				// 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				// 'Content-Type': 'multipart/form-data; charset=utf-8; boundary=glsr-form-data-boundary',
			});
		},
	};

	GLSR.Forms = function( shouldInit ) { // bool
		var form, submitButton;
		this.nodeList = document.querySelectorAll( 'form.glsr-form' );
		this.forms = [];
		for( var i = 0; i < this.nodeList.length; i++ ) {
			submitButton = this.nodeList[i].querySelector( '[type=submit]' );
			if( !submitButton )continue;
			form = new Form( this.nodeList[i], submitButton );
			if( shouldInit ) {
				form.init();
			}
			this.forms.push( form );
		}
		this.renderRecaptcha = function() {
			this.forms.forEach( function( form ) {
				form.recaptcha.render();
			});
		};
	};
})();

/** global: GLSR */
;(function() {

	'use strict';

	var Pagination = function( el ) { // HTMLElement
		this.el = el;
		this.initEvents_();
	};

	Pagination.prototype = {
		config: {
			hideClass: 'glsr-hide',
			linkSelector: '.glsr-navigation a',
			scrollTime: 468,
		},

		/** @return int */
		getChildIndexOfElement_: function( el ) { // HTMLElement
			var index = 0;
			while(( el = el.previousSibling )) {
				if( el.nodeType === 1 ) {
					index++;
				}
			}
			return index;
		},

		getSelector_: function( el ) {
			if( !el.nodeName )return;
			return this.getDomPath_( this.getDomPathNode_( el ));
		},

		/** @return string */
		getDomPath_: function( node ) { // object
			if( node.id !== '' ) {
				return '#' + node.id;
			}
			var root = '';
			if( node.parent ) {
				root = this.getDomPath_( node.parent ) + ' > ';
			}
			return root + node.name + ':nth-child(' + ( node.index + 1 ) + ')';
		},

		/** @return object */
		getDomPathNode_: function( el ) { // HTMLElement
			var node = {
				id: el.id,
				index: this.getChildIndexOfElement_( el ),
				name: el.nodeName.toLowerCase(),
				parent: null
			};
			if( el.parentElement && el.parentElement !== document.body ) {
				node.parent = this.getDomPathNode_( el.parentElement );
			}
			return node;
		},

		/** @return string */
		getElementClass_: function( el ) { // HTMLElement
			return el.className ? '.' + el.className.trim().replace( /\s+/g, '.' ) : '';
		},

		/** @return string */
		getElementId_: function( el ) { // HTMLElement
			return el.id ? '#' + el.id.trim() : '';
		},

		/** @return void */
		handleResponse_: function( location, selector, response ) { // string, string, string
			var newHTML = document.implementation.createHTMLDocument( 'x' );
			newHTML.documentElement.innerHTML = response;
			var newParentEl = selector ? newHTML.querySelectorAll( selector ) : '';
			if( newParentEl.length === 1 ) {
				this.el.innerHTML = newParentEl[0].innerHTML;
				this.scrollToTop_( this.el );
				this.el.classList.remove( this.config.hideClass );
				this.initEvents_();
				window.history.pushState( null, '', location );
				new GLSR.Excerpts( this.el );
				return;
			}
			window.location = location;
		},

		/** @return void */
		initEvents_: function() {
			var links = this.el.querySelectorAll( this.config.linkSelector );
			for( var i = 0; i < links.length; i++ ) {
				links[i].addEventListener( 'click', this.onClick_.bind( this ));
			}
		},

		/** @return void */
		onClick_: function( ev ) { // MouseEvent
			ev.preventDefault();
			var parentSelector = this.getSelector_( this.el );
			this.el.classList.add( this.config.hideClass );
			(new GLSR.Ajax()).get( ev.target.href, this.handleResponse_.bind( this, ev.target.href, parentSelector ));
		},

		/** @return void */
		scrollToTop_: function( el, offset ) { // HTMLElement, int
			offset = offset || 16; // 1rem
			var fixedElement;
			for( var i = 0; i < GLSR.ajaxpagination.length; i++ ) {
				fixedElement = document.querySelector( GLSR.ajaxpagination[i] );
				if( !fixedElement || window.getComputedStyle( fixedElement ).getPropertyValue( 'position' ) !== 'fixed' )continue;
				offset = offset + fixedElement.clientHeight;
			}
			var clientBounds = el.getBoundingClientRect();
			var offsetTop = clientBounds.top - offset;
			if( offsetTop > 0 )return; // if top is in view, don't scroll!
			this.scrollToTopStep_({
				endY: offsetTop,
				offset: window.pageYOffset,
				startTime: window.performance.now(),
				startY: el.scrollTop,
			});
		},

		/** @return void */
		scrollToTopStep_: function( context ) { // object
			var elapsed = ( window.performance.now() - context.startTime ) / this.config.scrollTime;
			elapsed = elapsed > 1 ? 1 : elapsed;
			var easedValue = 0.5 * ( 1 - Math.cos( Math.PI * elapsed ));
			var currentY = context.startY + ( context.endY - context.startY ) * easedValue;
			window.scroll( 0, context.offset + currentY ); // @todo what is this for again?
			if( currentY !== context.endY ) {
				window.requestAnimationFrame( this.scrollToTopStep_.bind( this, context ));
			}
		},
	};

	GLSR.Pagination = function() {
		this.navs = [];
		document.querySelectorAll( '.glsr-ajax-pagination' ).forEach( function( nodeItem ) {
			this.navs.push( new Pagination( nodeItem ));
		}.bind( this ));
	};
})();

/** global: GLSR, grecaptcha */
;(function() {

	'use strict';

	var Recaptcha = function( form ) { // Form object
		this.Form = form;
		this.addListeners = this.addListeners_;
		this.execute = this.execute_;
		this.render = this.render_;
		this.reset = this.reset_;
	};

	Recaptcha.prototype = {

		/** @return void */
		addListeners_: function() {
			var overlayEl = this.getOverlay_();
			if( overlayEl === -1 )return;
			overlayEl.addEventListener( 'click', this.Form.enableButton );
			window.addEventListener( 'keyup', this.onKeyup_.bind( this, overlayEl ));
		},

		/** @return void */
		execute_: function() {
			var recaptchaId = this.getId_();
			if( recaptchaId !== -1 ) {
				grecaptcha.execute( recaptchaId );
				return;
			}
			// recaptcha ID not found so pass through an error
			this.Form.submitForm_( false );
		},

		/** @return string|int (-1) */
		getId_: function() {
			return this.search_( function( value, id ) {
				if( Object.prototype.toString.call( value ) !== '[object HTMLDivElement]' )return;
				if( value.closest( 'form' ) === this.Form.form ) {
					return id;
				}
			});
		},

		/** @return HTMLDivElement|int (-1) */
		getOverlay_: function() {
			return this.search_( function( value ) {
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
		onKeyup_: function( ev ) { // KeyboardEvent
			if( ev.keyCode !== 27 )return;
			this.Form.enableButton();
			this.removeListeners_( ev.target );
		},

		/** @return void */
		removeListeners_: function( overlayEl ) { // HTMLDivElement
			overlayEl.removeEventListener( 'click', this.Form.enableButton );
			window.removeEventListener( 'keyup', this.onKeyup_ );
		},

		/** @return void */
		render_: function() {
			this.Form.form.onsubmit = null;
			var recaptchaEl = this.Form.form.querySelector( '.glsr-recaptcha-holder' );
			if( !recaptchaEl )return;
			recaptchaEl.innerHTML = '';
			var id = grecaptcha.render( recaptchaEl, {
				callback: this.submitForm_.bind( this ),
				'expired-callback': function() {
					grecaptcha.reset( id );
				},
			}, true );
		},

		/** @return void */
		reset_: function() {
			var recaptchaId = this.getId_();
			if( recaptchaId !== -1 ) {
				grecaptcha.reset( recaptchaId );
			}
		},

		/** @return mixed|int (-1) */
		search_: function( callback ) { // function
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
		submitForm_: function( token ) { // string
			this.Form.form.submitForm( token );
		},
	};

	GLSR.Recaptcha = Recaptcha;
})();

/** global: GLSR */
document.addEventListener( 'DOMContentLoaded', function() {
	// set text direction class
	var widgets = document.querySelectorAll( '.glsr-widget, .glsr-shortcode' );
	for( var i = 0; i < widgets.length; i++ ) {
		var direction = window.getComputedStyle( widgets[i], null ).getPropertyValue( 'direction' );
		widgets[i].classList.add( 'glsr-' + direction );
	}
	new GLSR.Forms( true );
	new GLSR.Pagination();
	new GLSR.Excerpts();
});
