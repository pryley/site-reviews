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

		/** @return string */
		getElementClass_: function( el ) { // HTMLElement
			return el.className ? '.' + el.className.trim().replace( /\s+/g, '.' ) : '';
		},

		/** @return string */
		getElementId_: function( el ) { // HTMLElement
			return el.id ? '#' + el.id.trim() : '';
		},

		/** @return void|string */
		getSelectorOfElement_: function( el ) { // HTMLElement
			if( !el || el.nodeType !== el.ELEMENT_NODE )return;
			return el.nodeName.toLowerCase() + this.getElementId_( el ) + this.getElementClass_( el );
		},

		/** @return void */
		handleResponse_: function( response, location ) { // string
			var parentSelector = this.getSelectorOfElement_( this.el );
			var html = document.implementation.createHTMLDocument( 'new' );
			html.documentElement.innerHTML = response;
			var newParentEl = parentSelector ? html.querySelectorAll( parentSelector ) : '';
			if( newParentEl.length === 1 ) {
				this.el.innerHTML = newParentEl[0].innerHTML;
				this.scrollToTop_( this.el );
				this.el.classList.remove( this.config.hideClass );
				this.initEvents_();
				window.history.pushState( null, '', location );
				new GLSR.Excerpts( this.el );
				return;
			}
			window.location = location; // @todo test location var
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
			this.el.classList.add( this.config.hideClass );
			GLSR.Ajax.get( this.href, this.handleResponse_.bind( this, ev.target.href ));
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
				window.requestAnimationFrame( this.scrollToTopStep_.bind( window, context ));
			}
		},
	};

	GLSR.Pagination = function() {
		var parentEl;
		var nodeList = document.querySelectorAll( '.glsr-ajax-pagination' );
		this.navs = [];
		for( var i = 0; i < nodeList.length; i++ ) {
			parentEl = nodeList[i].querySelector( '.glsr-reviews' );
			if( !parentEl )continue;
			this.navs.push( new Pagination( parentEl ));
		}
	};
})();
