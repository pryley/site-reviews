/** global: GLSR, site_reviews, x */
GLSR.pinned = function( options ) {
	this.options = x.extend( {}, this.defaults, options );
	this.el = x( this.options.selector );
	this.target = null;
	if( !this.el )return;
	this.init();
};

GLSR.pinned.prototype = {

	defaults: {
		selector: '#pinned-status-select',
	},

	/** @return void */
	init: function() {
		x( 'a.cancel-pinned-status' ).on( 'click', this.onClickCancel.bind( this ));
		x( 'a.edit-pinned-status' ).on( 'click', this.onClickEdit.bind( this ));
		x( 'a.save-pinned-status' ).on( 'click', this.onClickSave.bind( this ));
		x( 'table td.sticky i' ).on( 'click', this.onClickToggle.bind( this ));
	},

	/** @return void */
	onClickCancel: function( ev ) {
		ev.preventDefault();
		this.el.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		this.el.find( 'select' ).val( x( '#hidden-pinned-status' ).val() === '0' ? 1 : 0 );
	},

	/** @return void */
	onClickEdit: function( ev ) {
		ev.preventDefault();
		if( !this.el.is( ':hidden' ))return;
		this.el.slideDown( 'fast', function() {
			this.el.find( 'select' ).focus();
		}.bind( this ));
		x( this ).hide();
	},

	/** @return void */
	onClickSave: function( ev ) {
		ev.preventDefault();
		this.el.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		this.target = ev.target;
		var request = {
			action: 'toggle-pinned',
			id: x( '#post_ID' ).val(),
			pinned: x( '#pinned-status' ).val(),
		};
		this.request( request, this.save.bind( this ));
	},

	/** @return void */
	onClickToggle: function( ev ) {
		ev.preventDefault();
		this.target = ev.target;
		var request = {
			action: 'toggle-pinned',
			id: ev.target.getAttribute( 'data-id' ),
		};
		this.request( request, this.toggle.bind( this ));
	},

	/** @return void */
	request: function( request, callback ) {
		var data = {
			action: site_reviews.action,
			request: request,
		};
		x.post( site_reviews.ajaxurl, data, callback.bind( this ));
	},

	/** @return void */
	save: function( response ) {
		x( '#pinned-status' ).val( !response.pinned|0 );
		x( '#hidden-pinned-status' ).val( response.pinned|0 );
		x( '#pinned-status-text' ).text( response.pinned ? this.target.data( 'yes' ) : this.target.data( 'no' ));
		// GLSR.insertNotices( response.notices );
	},

	/** @return void */
	toggle: function( response ) {
		var action = response.pinned ? 'add' : 'remove';
		this.target[action + 'Class']( 'pinned' );
	},
};
