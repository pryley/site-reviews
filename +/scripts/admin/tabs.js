/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Tabs = function( options ) {
		this.options = $.extend( {}, this.defaults, options );
		this.active = document.querySelector( 'input[name=_active_tab]' );
		this.referrer = document.querySelector( 'input[name=_wp_http_referer]' );
		this.tabs = document.querySelectorAll( this.options.tabSelector );
		this.views = document.querySelectorAll( this.options.viewSelector );
		if( !this.active || !this.referrer || !this.tabs || !this.views )return;
		this.init_();
	};

	GLSR.Tabs.prototype = {
		defaults: {
			tabSelector: '.glsr-nav-tab',
			viewSelector: '.glsr-nav-view',
		},

		/** @return void */
		init_: function() {
			$( window ).on( 'hashchange', this.onHashchange_.bind( this ));
			[].forEach.call( this.tabs, function( tab, index ) {
				var active = location.hash ? tab.getAttribute( 'href' ).slice(1) === location.hash.slice(2) : index === 0;
				if( active ) {
					this.setTab_( tab );
				}
				tab.addEventListener( 'click', this.onClick_.bind( this ));
				tab.addEventListener( 'touchend', this.onClick_.bind( this ));
			}.bind( this ));
		},

		/** @return string */
		getAction_: function( bool ) {
			return bool ? 'add' : 'remove';
		},

		/** @return void */
		onClick_: function( ev ) {
			ev.preventDefault();
			var el = ev.currentTarget;
			el.blur();
			this.toggleCollapsibleViewSections_( el );
			this.setTab_( el );
			location.hash = '!' + el.getAttribute( 'href' ).slice(1);
		},

		/** @return void */
		onHashchange_: function() {
			var id = location.hash.split('#!')[1];
			for( var i = 0; i < this.views.length; i++ ) {
				if( id !== this.views[i].id )continue;
				this.setTab_( this.tabs[i] );
				break;
			}
		},

		/** @return void */
		setReferrer_: function( index ) {
			var referrerUrl = this.referrer.value.split('#')[0] + '#!' + this.views[index].id;
			this.referrer.value = referrerUrl;
		},

		/** @return void */
		setTab_: function( el ) {
			[].forEach.call( this.tabs, function( tab, index ) {
				var action = this.getAction_( tab === el );
				if( action === 'add' ) {
					this.active.value = this.views[index].id;
					this.setReferrer_( index );
					this.setView_( index );
				}
				tab.classList[action]( 'nav-tab-active' );
			}.bind( this ));
		},

		/** @return void */
		setView_: function( idx ) {
			[].forEach.call( this.views, function( view, index ) {
				var action = this.getAction_( index !== idx );
				view.classList[action]( 'ui-tabs-hide' );
			}.bind( this ));
		},

		/** @return void */
		toggleCollapsibleViewSections_: function( el ) {
			if( !el.classList.contains( 'nav-tab-active' ))return;
			var view = $( el.getAttribute( 'href' ));
			var action = view.hasClass( 'collapsed' ) ? 'remove' : 'add';
			view[action + 'Class']( 'collapsed' )
				.find( '.glsr-card.postbox' )[action + 'Class']( 'closed' )
				.find( '.handlediv' ).attr( 'aria-expanded', action !== 'add' );
		},
	};
})( jQuery );
