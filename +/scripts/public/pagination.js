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
			linkSelector: '[data-pagination] a',
			scrollTime: 468,
		},

		/** @return void */
		handleResponse_: function( location, response, success ) { // string, string
			var paginationEl = this.el.querySelector('[data-pagination]');
			var reviewsEl = this.el.querySelector('[data-reviews]');
			if( !success || !reviewsEl || !paginationEl ) {
				window.location = location;
				return;
			}
			paginationEl.outerHTML = response.pagination;
			reviewsEl.outerHTML = response.reviews;
			this.scrollToTop_( this.el );
			this.el.classList.remove( this.config.hideClass );
			this.initEvents_();
			window.history.pushState( null, '', location );
			new GLSR.Excerpts( this.el );
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
			var jsonEl = this.el.querySelector('[data-pagination]');
			if( !jsonEl ) {
				console.log( 'pagination config not found.' );
				return;
			}
			var data = {};
			data[GLSR.nameprefix + '[_action]'] = 'fetch-paged-reviews';
			data[GLSR.nameprefix + '[atts]'] = jsonEl.dataset.atts;
			data[GLSR.nameprefix + '[url]'] = ev.currentTarget.href;
			this.el.classList.add( this.config.hideClass );
			ev.preventDefault();
			(new GLSR.Ajax()).post( data, this.handleResponse_.bind( this, ev.currentTarget.href ));
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
			window.scrollTo( 0, context.offset + currentY ); // @todo what is this for again?
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
