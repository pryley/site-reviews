/** global: GLSR */
;(function() {

	'use strict';

	var GLSR_Pagination = function( el ) { // HTMLElement
		this.el = el;
		this.initEvents_();
	};

	GLSR_Pagination.prototype = {
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
			(new GLSR.Ajax()).get_( ev.currentTarget.href, this.handleResponse_.bind( this, ev.currentTarget.href, parentSelector ));
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
		var pagination = document.querySelectorAll( '.glsr-ajax-pagination' );
		if( !pagination.length )return;
		pagination.forEach( function( nodeItem ) {
			this.navs.push( new GLSR_Pagination( nodeItem ));
		}.bind( this ));
	};
})();
