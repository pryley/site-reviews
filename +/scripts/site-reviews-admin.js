/** global: GLSR, wp, x */

x( function()
{
	var GLSR_textarea = x( '#contentdiv > textarea' );
	if( GLSR_textarea.length ) {
		GLSR.textareaResize( GLSR_textarea );
		x( document ).on( 'wp-window-resized.editor-expand', function() {
			GLSR.textareaResize( GLSR_textarea );
		});
	}

	x( 'form' ).on( 'click', '#clear-log', GLSR.onClearLog );

	x.each( GLSR.pointers, function( i, pointer ) {
		GLSR.initPointer( pointer );
	});

	GLSR.colorControls();

	new GLSR.pinned();
	new GLSR.tabs();
	new GLSR.forms( 'form.glsr-form' );
	new GLSR.status( 'a.glsr-change-status' );
	new GLSR.search( '#glsr-search-posts', {
		action: 'search-posts',
		onInit: function() {
			this.el.on( 'click', '.glsr-remove-button', this.onUnassign.bind( this ));
		},
		onResultClick: function( ev ) {
			var result = x( ev.target );
			var template = wp.template( 'glsr-assigned-post' );
			var entry = {
				url: result.data( 'url' ),
				title: result.text(),
			};
			if( template ) {
				this.el.find( 'input#assigned_to' ).val( result.data( 'id' ));
				this.el.find( '.description' ).html( template( entry ));
				this.el.on( 'click', '.glsr-remove-button', this.onUnassign.bind( this ));
				this.reset();
			}
		},
	});
	new GLSR.search( '#glsr-search-translations', {
		action: 'search-translations',
		onInit: function() {
			this.makeSortable();
		},
		onResultClick: function( ev ) {
			var result = x( ev.target );
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
			this.setVisibility();
		},
	});

	GLSR.modules = {
		shortcode: new GLSR.shortcode( '.glsr-mce' ),
	};
});
