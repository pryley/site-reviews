GLSR.search = function( el, options ) {
	this.el = Object.prototype.toString.call( el ) === '[object String]' ? x( el ) : el;
	this.options = options;
	this.searchTerm = null;
	this.init();
};

GLSR.search.prototype = {

	defaults: {
		action: null,
		exclude: [],
		onInit: null,
		onResultClick: null,
		results: {},
		selected: -1,
		selectedClass: 'glsr-selected-result',
		selectorEntries: '.glsr-strings-table tbody',
		selectorResults: '.glsr-search-results',
		selectorSearch: '.glsr-search-input',
		// entriesEl
		// resultsEl
		// searchEl
	},

	/** @return void */
	init: function() {
		this.options = x.extend( {}, this.defaults, this.options );
		if( !this.el.length )return;
		this.options.entriesEl = this.el.parent().find( this.options.selectorEntries );
		this.options.resultsEl = this.el.find( this.options.selectorResults );
		this.options.searchEl = this.el.find( this.options.selectorSearch );
		this.options.searchEl.attr( 'aria-describedby', 'live-search-desc' );
		if( typeof this.options.onInit === 'function' ) {
			this.options.onInit.call( this );
		}
		this.initEvents();
	},

	/** @return void */
	initEvents: function() {
		this.options.searchEl.on( 'input', _.debounce( this.onSearchInput.bind( this ), 500 ));
		this.options.searchEl.on( 'keyup', this.onSearchKeyup.bind( this ));
		x( document ).on( 'click', this.onDocumentClick.bind( this ));
		x( document ).on( 'keydown', this.onDocumentKeydown.bind( this ));
	},

	/** @return void */
	abort: function() {
		if( 'undefined' === typeof this.searchRequest )return;
		this.searchRequest.abort();
	},

	/** @return void */
	clearResults: function() {
		this.abort();
		this.options.resultsEl.empty();
		this.el.removeClass( 'is-active' );
		x( 'body' ).removeClass( 'glsr-focus' );
	},

	/** @return void */// Manage entries
	deleteEntry: function( index ) {
		var row = this.options.entriesEl.children( 'tr' ).eq( index );
		var search = this;
		row.find( 'td' ).css({ backgroundColor:'#faafaa' });
		row.fadeOut( 350, function() {
			x( this ).remove();
			search.options.results = {};
			search.reindexRows();
			search.setVisibility();
		});
	},

	/** @return void */
	displayResults: function( items ) {
		x( 'body' ).addClass( 'glsr-focus' );
		this.options.resultsEl.append( items );
		this.options.resultsEl.children( 'span' ).on( 'click', this.onResultClick.bind( this ));
	},

	/** @return void */// Manage entries
	makeSortable: function() {
		this.options.entriesEl.on( 'click', 'a.delete', this.onEntryDelete.bind( this ));
		this.options.entriesEl.sortable({
			items: 'tr',
			tolerance: 'pointer',
			start: function( ev, ui ) {
				ui.placeholder.height( ui.helper[0].scrollHeight );
			},
			sort: function( ev, ui ) {
				var top = ev.pageY - x( this ).offsetParent().offset().top - ( ui.helper.outerHeight( true ) / 2 );
				ui.helper.css({
					top: top + 'px',
				});
			},
		});
	},

	/** @return void */
	navigateResults: function( diff ) {
		this.options.selected += diff;
		this.options.results.removeClass( this.options.selectedClass );
		if( this.options.selected < 0 ) {
			// reached the start (should now allow keydown scroll)
			this.options.selected = -1;
			this.options.searchEl.focus();
		}
		if( this.options.selected >= this.options.results.length ) {
			// reached the end (should now allow keydown scroll)
			this.options.selected = this.options.results.length - 1;
		}
		if( this.options.selected >= 0 ) {
			this.options.results.eq( this.options.selected )
				.addClass( this.options.selectedClass )
				.focus();
		}
	},

	/** @return void */
	onDocumentClick: function( ev ) {
		if( x( ev.target ).find( this.el ).length && x( 'body' ).hasClass( 'glsr-focus' )) {
			this.clearResults();
		}
	},

	/** @return void */
	onDocumentKeydown: function( ev ) {
		if( !this.options.results )return;
		if( GLSR.keys.ESC === ev.which ) {
			this.clearResults();
		}
		if( GLSR.keys.ENTER === ev.which || GLSR.keys.SPACE === ev.which ) {
			var selected = this.options.resultsEl.find( '.' + this.options.selectedClass );
			if( selected ) {
				selected.trigger( 'click' );
			}
		}
		if( GLSR.keys.UP === ev.which ) {
			ev.preventDefault();
			this.navigateResults(-1);
		}
		if( GLSR.keys.DOWN === ev.which ) {
			ev.preventDefault();
			this.navigateResults(1);
		}
	},

	/** @return void */// Manage entries
	onEntryDelete: function( ev ) {
		ev.preventDefault();
		this.deleteEntry( x( ev.target ).closest( 'tr' ).index() );
	},

	/** @return void */
	onResultClick: function( ev ) {
		ev.preventDefault();
		if( typeof this.options.onResultClick === 'function' ) {
			this.options.onResultClick.call( this, ev );
		}
		this.clearResults();
	},

	/** @return void */
	onSearchInput: function( ev ) {
		this.abort();
		if( this.searchTerm === ev.target.value && this.options.results.length ) {
			return this.displayResults( this.options.results );
		}
		this.options.resultsEl.empty();
		this.options.selected = -1;
		this.searchTerm = ev.target.value;
		if( this.searchTerm === '' ) {
			return this.reset();
		}
		this.el.addClass( 'is-active' );
		this.searchRequest = wp.ajax.post( site_reviews.action, {
			request: {
				action: this.options.action,
				exclude: this.options.exclude,
				nonce: this.el.find( '#_wpnonce' ).val(),
				search: this.searchTerm,
			},
		}).done( function( response ) {
			this.el.removeClass( 'is-active' );
			this.displayResults( response.items ? response.items : response.empty );
			this.options.results = this.options.resultsEl.children();
			delete this.searchRequest;
		}.bind( this ));
	},

	/** @return void */
	onSearchKeyup: function( ev ) {
		if( GLSR.keys.ESC === ev.which ) {
			this.reset();
		}
		if( GLSR.keys.ENTER === ev.which ) {
			this.onSearchInput( ev );
		}
	},

	/** @return void */// Manage entries
	onUnassign: function( ev ) {
		ev.preventDefault();
		var assigned = this.el.find( '.description' );
		this.el.find( 'input#assigned_to' ).val( '' );
		assigned.find( 'a' ).css({ color:'#c00' });
		assigned.fadeOut( 'fast', function() {
			x( this ).html( '' ).show();
		});
	},

	/** @return void */// Manage entries
	reindexRows: function() {
		var search = this;
		this.options.exclude = [];
		this.options.entriesEl.children( 'tr' ).each( function( index ) {
			x( this ).find( '.glsr-string-td2' ).children().filter( ':input' ).each( function() {
				var input = x( this );
				var name = input.attr( 'name' ).replace( /\[\d+\]/i, '[' + index + ']' );
				input.attr( 'name', name );
				if( input.is( '[data-id]' )) {
					search.options.exclude.push({ id: input.val() });
				}
			});
		});
	},

	/** @return void */
	reset: function() {
		this.clearResults();
		this.options.results = {};
		this.options.searchEl.val( '' );
	},

	/** @return void */// Manage entries
	setVisibility: function() {
		var action = this.options.entriesEl.children().length > 0 ? 'remove' : 'add';
		this.options.entriesEl.parent()[action + 'Class']( 'glsr-hidden' );
	},
};
