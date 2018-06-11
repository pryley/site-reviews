/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Pinned = function() {
		this.el = x( '#pinned-status-select' );
		if( this.el ) {
			this.cancel = x( 'a.cancel-pinned-status' );
			this.cancel.on( 'click', this.onClickCancel_.bind( this ));
			this.edit = x( 'a.edit-pinned-status' );
			this.edit.on( 'click', this.onClickEdit_.bind( this ));
			this.save = x( 'a.save-pinned-status' );
			this.save.on( 'click', this.onClickSave_.bind( this ));
		}
		x( 'table td.pinned i' ).on( 'click', this.onClickToggle_.bind( this ));
	};

	Pinned.prototype = {
		/** @return void */
		restoreEditLink_: function() {
			this.el.slideUp( 'fast' );
			this.edit.show().focus();
		},

		/** @return void */
		onClickCancel_: function( ev ) { // MouseEvent
			ev.preventDefault();
			this.restoreEditLink_();
			this.el.find( 'select' ).val( x( '#hidden-pinned-status' ).val() === '0' ? 1 : 0 );
		},

		/** @return void */
		onClickEdit_: function( ev ) { // MouseEvent
			ev.preventDefault();
			if( !this.el.is( ':hidden' ))return;
			this.el.slideDown( 'fast', function() {
				this.el.find( 'select' ).focus();
			}.bind( this ));
			this.edit.hide();
		},

		/** @return void */
		onClickSave_: function( ev ) { // MouseEvent
			ev.preventDefault();
			this.restoreEditLink_();
			this.target = ev.target;
			var request = {
				action: 'toggle-pinned',
				id: x( '#post_ID' ).val(),
				pinned: x( '#pinned-status' ).val(),
			};
			(new GLSR.Ajax( request )).post( this.save_.bind( this ));
		},

		/** @return void */
		onClickToggle_: function( ev ) { // MouseEvent
			ev.preventDefault();
			this.target = ev.target;
			var request = {
				action: 'toggle-pinned',
				id: ev.target.getAttribute( 'data-id' ),
			};
			(new GLSR.Ajax( request )).post( this.togglePinned_.bind( this ));
		},

		/** @return void */
		save_: function( response ) {
			x( '#pinned-status' ).val( !response.pinned|0 );
			x( '#hidden-pinned-status' ).val( response.pinned|0 );
			x( '#pinned-status-text' ).text( response.pinned ? this.target.dataset.yes : this.target.dataset.no );
			GLSR.Notices( response.notices );
		},

		/** @return void */
		togglePinned_: function( response ) {
			this.target.classList[response.pinned ? 'add' : 'remove']( 'pinned' );
		},
	};

	GLSR.Pinned = Pinned;
})( jQuery );
