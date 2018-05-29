GLSR.tabs = function( options ) {
	this.options = x.extend( {}, this.defaults, options );
	this.active = document.querySelector( 'input[name=_active_tab]' );
	this.referrer = document.querySelector( 'input[name=_wp_http_referer]' );
	this.tabs = document.querySelectorAll( this.options.tabSelector );
	this.views = document.querySelectorAll( this.options.viewSelector );
	if( !this.active || !this.referrer || !this.tabs || !this.views )return;
	this.init();
};

GLSR.tabs.prototype = {

	defaults: {
		tabSelector: '.glsr-nav-tab',
		viewSelector: '.glsr-nav-view',
	},

	/** @return void */
	init: function() {
		[].forEach.call( this.tabs, function( tab, index ) {
			var active = location.hash ? tab.getAttribute( 'href' ).slice(1) === location.hash.slice(2) : index === 0;
			if( active ) {
				this.setTab( tab );
			}
			tab.addEventListener( 'click', this.onClick.bind( this ));
			tab.addEventListener( 'touchend', this.onClick.bind( this ));
		}.bind( this ));
	},

	/** @return string */
	getAction: function( bool ) {
		return bool ? 'add' : 'remove';
	},

	/** @return void */
	onClick: function( ev ) {
		ev.preventDefault();
		ev.target.blur();
		this.setTab( ev.target );
		location.hash = '!' + ev.target.getAttribute( 'href' ).slice(1);
	},

	/** @return void */
	setReferrer: function( index ) {
		var referrerUrl = this.referrer.value.split('#')[0] + '#!' + this.views[index].id;
		this.referrer.value = referrerUrl;
	},

	/** @return void */
	setTab: function( el ) {
		[].forEach.call( this.tabs, function( tab, index ) {
			var action = this.getAction( tab === el );
			if( action === 'add' ) {
				this.active.value = this.views[index].id;
				this.setReferrer( index );
				this.setView( index );
			}
			tab.classList[action]( 'nav-tab-active' );
		}.bind( this ));
	},

	/** @return void */
	setView: function( idx ) {
		[].forEach.call( this.views, function( view, index ) {
			var action = this.getAction( index !== idx );
			view.classList[action]( 'ui-tabs-hide' );
		}.bind( this ));
	},
};
