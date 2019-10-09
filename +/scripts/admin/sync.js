/** global: GLSR, jQuery */
/* jshint -W014 */
;(function( $ ) {

	'use strict';

	GLSR.Sync = function() {
		this.button = $( 'button#sync-reviews' );
		this.progressbar = $( '.glsr-progress' );
		this.service = null;
		$( 'form.glsr-form-sync' ).on( 'click', '#sync-reviews', this.onSync_.bind( this ));
		$( document ).on( 'wp-window-resized', this.onWindowResize_ );
		$( window ).on( 'hashchange', this.onWindowResize_ );
		this.onWindowResize_();
	};

	GLSR.Sync.prototype = {

		finishSync_: function( response ) {
			$( '.service-' + this.service + ' td.column-last_sync' ).text( response.last_sync );
			$( '.service-' + this.service + ' td.column-total_fetched a' ).text( response.total );
			this.watchSyncStatus_( false );
		},

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
			(new GLSR.Ajax( data )).post( this.syncProgress_.bind( this ));
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
			setTimeout( function() {
				(new GLSR.Ajax( data )).post( callback );
			}, 1500 );
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
			if( response.percent_synced && response.percent_synced >= 100 ) {
				this.finishSync_( response );
				return;
			}
			(new GLSR.Ajax( data )).post( this.syncReviews_.bind( this ));
		},

		updateMessage_: function( text ) {
			$( '.glsr-progress-status', this.progressbar ).text( text );
		},

		updateProgress_: function( percent ) {
			percent = (percent || 0) + '%';
			$( '.glsr-progress-bar', this.progressbar ).outerWidth( percent );
		},

		watchSyncStatus_: function( run ) {
			if( run === true ) {
				this.updateMessage_( this.progressbar.data( 'active-text' ));
				this.updateProgress_();
				this.button.prop( 'disabled', true );
				window.requestAnimationFrame(function() {
					this.progressbar.addClass( 'active' );
				}.bind( this ));
			}
			if( run === false ) {
				this.service = null;
				this.button.prop( 'disabled', false );
				this.progressbar.removeClass( 'active' );
				return;
			}
			window.requestAnimationFrame( this.watchSyncStatus_.bind( this ));
		},
	};
})( jQuery );
