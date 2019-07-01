/** global: GLSR, jQuery, wp */

GLSR.keys = {
	DOWN: 40,
	ENTER: 13,
	ESC: 27,
	SPACE: 32,
	UP: 38,
};

jQuery( function( $ ) {

	GLSR.shortcode = new GLSR.Shortcode( '.glsr-mce' );
	GLSR.ColorPicker();
	new GLSR.Categories();
	new GLSR.Forms( 'form.glsr-form' );
	new GLSR.Pinned();
	new GLSR.Pointers();
	new GLSR.Search( '#glsr-search-posts', {
		action: 'search-posts',
		onInit: function() {
			this.el.on( 'click', '.glsr-remove-button', this.onUnassign_.bind( this ));
		},
		onResultClick: function( ev ) {
			var result = $( ev.currentTarget );
			var template = wp.template( 'glsr-assigned-post' );
			var entry = {
				url: result.data( 'url' ),
				title: result.text(),
			};
			if( template ) {
				this.el.find( 'input#assigned_to' ).val( result.data( 'id' ));
				this.el.find( '.description' ).html( template( entry ));
				this.el.on( 'click', '.glsr-remove-button', this.onUnassign_.bind( this ));
				this.reset_();
			}
		},
	});
	new GLSR.Search( '#glsr-search-translations', {
		action: 'search-translations',
		onInit: function() {
			this.makeSortable_();
		},
		onResultClick: function( ev ) {
			var result = $( ev.currentTarget );
			var entry = result.data( 'entry' );
			var template = wp.template( 'glsr-string-' + ( entry.p1 ? 'plural' : 'single' ));
			entry.index = this.options.entriesEl.children().length;
			entry.prefix = this.options.resultsEl.data( 'prefix' );
			if( template ) {
				this.options.entriesEl.append( template( entry ));
				this.options.exclude.push({ id: entry.id });
				this.options.results = this.options.results.filter( function( i, el ) {
					return el !== result.get(0);
				});
			}
			this.setVisibility_();
		},
	});
	new GLSR.Status( 'a.glsr-change-status' );
	new GLSR.Tabs();
	new GLSR.TextareaResize();
	new GLSR.Tools();
	new GLSR.Sync();

	$( '.glsr-card.postbox' ).addClass( 'closed' )
		.find( '.handlediv' ).attr( 'aria-expanded', false )
		.closest( '.glsr-nav-view' ).addClass( 'collapsed' );

	$( '.glsr-card.postbox .glsr-card-header' ).on( 'click', function() {
		var parent = $( this ).parent();
		var view = parent.closest( '.glsr-nav-view' );
		var action = parent.hasClass( 'closed' ) ? 'remove' : 'add';
		parent[action + 'Class']( 'closed' ).find( '.handlediv' ).attr( 'aria-expanded', action !== 'add' );
		action = view.find( '.glsr-card.postbox' ).not( '.closed' ).length > 0 ? 'remove' : 'add';
		view[action + 'Class']( 'collapsed' );
	});

	if( $('.glsr-support-step').not(':checked').length < 1 ) {
		$( '.glsr-card-result' ).removeClass( 'hidden' );
	}

	$('.glsr-support-step').on( 'change', function() {
		var action = $('.glsr-support-step').not(':checked').length > 0 ? 'add' : 'remove';
		$( '.glsr-card-result' )[action + 'Class']( 'hidden' );
	});
});
