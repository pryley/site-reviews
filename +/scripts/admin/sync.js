/** global: GLSR, jQuery */
/* jshint -W014 */
;(function( $ ) {

	'use strict';

	GLSR.Sync = function() {
		this.button = $( 'button#sync-reviews' );
		this.progressbar = $( '.glsr-progress' );
		this.service = null;
		this.syncing = false;
		$( 'form' ).on( 'click', '#sync-reviews', this.onSync_.bind( this ));
		$( document ).on( 'wp-window-resized', this.onWindowResize_ );
		$( window ).on( 'hashchange', this.onWindowResize_ );
		this.onWindowResize_();
	};

	GLSR.Sync.prototype = {

		onSync_: function( ev ) {
			ev.preventDefault();
			this.service = $( '[name="'+GLSR.nameprefix+'[service]"]' ).val();
			if( this.service ) {
				this.watchSyncStatus_( true );
				this.syncFetch_();
			}
		},

		onWindowResize_: function() {
			var width = $( '.glsr-progress' ).width();
			if( !width )return;
			$( '.glsr-progress span' ).width( width );
		},

		syncFetch_: function() {
			var data = {
				_action: 'sync-reviews',
				service: this.service,
				stage: 'fetch',
			};
			(new GLSR.Ajax( data )).post_( this.syncProgress_.bind( this ));
		},

		syncProgress_: function( response ) {
			var data = {
				_action: 'sync-reviews',
				job_id: response.job_id,
				service: this.service,
				stage: 'progress',
			};
			var callback = !response.finished
				? this.syncProgress_.bind( this )
				: this.syncReviews_.bind( this, response );
			this.updateMessage_( response.message );
			this.updateProgress_( response.percent );
			// console.log( response );
			setTimeout( function() {
				(new GLSR.Ajax( data )).post_( callback );
			}, 2000 );
		},

		syncReviews_: function( response ) {
			var page = 0;
			try {
				page = response.meta.pagination.current_page;
			} catch(e) {}
			var data = {
				_action: 'sync-reviews',
				page: page + 1,
				service: this.service,
				stage: 'reviews',
			};
			this.updateMessage_( response.message );
			if( !response.percent_synced || response.percent_synced < 100 ) {
				(new GLSR.Ajax( data )).post_( this.syncReviews_.bind( this ));
				return;
			}
			this.watchSyncStatus_( false );
		},

		updateMessage_: function( text ) {
			$( '.glsr-progress-status', this.progressbar ).text( text );
		},

		updateProgress_: function( percent ) {
			percent = (percent || 0) + '%';
			$( '.glsr-progress-bar-1', this.progressbar ).outerWidth( percent );
		},

		watchSyncStatus_: function( run ) {
			if( run === true ) {
				this.syncing = true;
				this.button.prop( 'disabled', true );
				this.updateMessage_( this.progressbar.data( 'active-text' ));
				this.updateProgress_();
				window.requestAnimationFrame(function() {
					this.progressbar.addClass( 'active' );
				}.bind( this ));
			}
			if( run === false ) {
				this.syncing = false;
				this.service = null;
				this.button.prop( 'disabled', false );
				this.progressbar.removeClass( 'active' );
				return;
			}
			window.requestAnimationFrame( this.watchSyncStatus_.bind( this ));
		},
	};
})( jQuery );
