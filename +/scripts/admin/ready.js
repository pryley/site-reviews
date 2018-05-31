/** global: GLSR, site_reviews_pointers, wp, x */

x( function()
{
	x('.glsr-button-reset').on( 'click', function() {
		return confirm( site_reviews.are_you_sure );
	});

	var GLSR_fix = GLSR.getURLParameter( 'fix' );
	if( GLSR_fix ) {
		x( 'td [data-key="' + GLSR_fix + '"]').focus();
	}

	var GLSR_textarea = x( '#contentdiv > textarea' );
	if( GLSR_textarea.length ) {
		GLSR.textareaResize( GLSR_textarea );
		x( document ).on( 'wp-window-resized.editor-expand', function() {
			GLSR.textareaResize( GLSR_textarea );
		});
	}

	x( 'form' ).on( 'click', '#clear-log', GLSR.onClearLog );

	GLSR.colorControls();

	x.each( site_reviews_pointers.pointers, function( i, pointer ) {
		GLSR.pointers( pointer );
	});

	x( document ).on( 'click', function( ev ) {
		if( !x( ev.target ).closest( '.glsr-mce' ).length ) {
			GLSR.shortcode.close();
		}
	});

	x( document ).on( 'click', '.glsr-mce-button', GLSR.shortcode.toggle );
	x( document ).on( 'click', '.glsr-mce-menu-item', GLSR.shortcode.trigger );
	x( document ).on( 'click', 'a.change-site-review-status', GLSR.onChangeStatus );

	new GLSR.forms( 'form.glsr-form' );
	new GLSR.pinned();
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
	new GLSR.tabs();
});
