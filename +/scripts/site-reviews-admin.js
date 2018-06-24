/** global: GLSR, jQuery, wp */

GLSR.keys = {
	DOWN: 40,
	ENTER: 13,
	ESC: 27,
	SPACE: 32,
	UP: 38,
};

jQuery( function() {
	GLSR.ColorPicker();
	new GLSR.Console();
	new GLSR.Forms( 'form.glsr-form' );
	new GLSR.Pinned();
	new GLSR.Pointers();
	new GLSR.Search( '#glsr-search-posts', {
		action: 'search-posts',
		onInit: function() {
			this.el.on( 'click', '.glsr-remove-button', this.onUnassign_.bind( this ));
		},
		onResultClick: function( ev ) {
			var result = jQuery( ev.target );
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
			var result = jQuery( ev.target );
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
});
