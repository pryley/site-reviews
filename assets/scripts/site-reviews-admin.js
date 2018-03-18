// jshint unused:false
var x = jQuery.noConflict();

var GLSR = {
	addons: {},
	keys: {
		ENTER: 13,
		ESC: 27,
		SPACE: 32,
		UP: 38,
		DOWN: 40,
	},
	pinned: {},
	shortcode: {},
	translation: {},
};

GLSR.colorControls = function()
{
	if( typeof x.wp !== 'object' || typeof x.wp.wpColorPicker !== 'function' )return;

	x( document ).find( 'input[type="text"].color-picker-hex' ).each( function() {
		var t = x( this );
		var options = t.data( 'colorpicker' ) || {};
		t.wpColorPicker( options );
	});
};

GLSR.dismissNotices = function()
{
	x( '.notice.is-dismissible' ).each( function() {
		var notice = x( this );
		notice.fadeTo( 100, 0, function() {
			notice.slideUp( 100, function() {
				notice.remove();
			});
		});
	});
};

GLSR.getURLParameter = function( name )
{
	return decodeURIComponent(
		(new RegExp( '[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)' ).exec( location.search ) || [null, ''])[1].replace( /\+/g, '%20' )
	) || null;
};

GLSR.insertNotices = function( notices )
{
	notices = notices || false;

	if( !notices )return;

	if( !x( '#glsr-notices' ).length ) {
		x( '#message.notice' ).remove();
		x( 'form#post' ).before( '<div id="glsr-notices" />' );
	}

	x( '#glsr-notices' ).html( notices );

	x( document ).trigger( 'wp-updates-notice-added' );
};

GLSR.isUndefined = function( value )
{
	var is_undefined = void(0);
	return value === is_undefined;
};

GLSR.normalizeValue = function( value )
{
	if(['true','on','1'].indexOf( value ) > -1 ) {
		return true;
	}

	if(['false','off','0'].indexOf( value ) > -1 ) {
		return false;
	}

	return value;
};

GLSR.normalizeValues = function( array )
{
	return array.map( GLSR.normalizeValue );
};

GLSR.onChangeStatus = function( ev )
{
	var post_id = this.href.match(/post=([0-9]+)/)[1];
	var status  = this.href.match(/action=([a-z]+)/)[1];

	if( GLSR.isUndefined( status ) || GLSR.isUndefined( post_id ))return;

	var request = {
		action: 'change-review-status',
		status : status,
		post_id: post_id,
	};

	GLSR.postAjax( ev, request, function( response )
	{
		var el = x( ev.target );

		el.closest( 'tr' ).removeClass( 'status-pending status-publish' ).addClass( response.class );
		el.closest( 'td.column-title' ).find( 'strong' ).html( response.link );
	});
};

GLSR.onClearLog = function( ev )
{
	var request = {
		action: 'clear-log',
	};
	GLSR.postAjax( ev, request, function( response )
	{
		GLSR.insertNotices( response.notices );
		x( '#log-file' ).val( response.logger );
	});
};

GLSR.onFieldChange = function()
{
	var depends = x( this ).closest( 'form' ).find( '[data-depends]' );

	if( !depends.length )return;

	var name  = this.getAttribute( 'name' );
	var type  = this.getAttribute( 'type' );

	for( var i = 0; i < depends.length; i++ ) {

		try {
			var data = JSON.parse( depends[i].getAttribute( 'data-depends' ) );
			var bool;

			if( data.name !== name )continue;

			if( 'checkbox' === type ) {
				bool = !!this.checked;
			}
			else if( x.isArray( data.value ) ) {
				bool = x.inArray( GLSR.normalizeValue( this.value ), GLSR.normalizeValues( data.value ) ) !== -1;
			}
			else {
				bool = GLSR.normalizeValue( data.value ) === GLSR.normalizeValue( this.value );
			}

			GLSR.toggleHiddenField( depends[i], bool );
		}
		catch( e ) {
			console.error( 'JSON Error: ' + depends[i] );
		}
	}
};

GLSR.pointers = function( pointer )
{
	x( pointer.target ).pointer({
		content: pointer.options.content,
		position: pointer.options.position,
		close: function() {
			x.post( ajaxurl, {
				pointer: pointer.id,
				action: 'dismiss-wp-pointer',
			});
		},
	})
	.pointer( 'open' )
	.pointer( 'sendToTop' );

	x( document ).on( 'wp-window-resized', function() {
		x( pointer.target ).pointer( 'reposition' );
	});
};

GLSR.postAjax = function( ev, request, callback )
{
	ev.preventDefault();
	var el = x( ev.target );
	if( el.is( ':disabled' ))return;
	request.nonce = request.nonce || el.closest( 'form' ).find( '#_wpnonce' ).val();
	var data = {
		action: site_reviews.action,
		request: request,
	};
	el.prop( 'disabled', true );
	x.post( site_reviews.ajaxurl, data, function( response ) {
		if( typeof callback === 'function' ) {
			callback( response );
		}
		el.prop( 'disabled', false );
	});
};

GLSR.textareaResize = function( el )
{
	var minHeight = 320;
	var textarea  = el[0];

	textarea.style.height = 'auto';

	textarea.style.height = textarea.scrollHeight > minHeight ?
		textarea.scrollHeight + 'px' :
		minHeight + 'px';
};

GLSR.toggleHiddenField = function( el, bool )
{
	var row = x( el ).closest( '.glsr-field' );

	if( !row.length )return;

	if( bool ) {
		row.removeClass( 'hidden' );
	}
	else {
		row.addClass( 'hidden' );
	}
};

/** global: GLSR, site_reviews, x */

GLSR.pinned.events = function()
{
	var pinnedSelect = x( '#pinned-status-select' );

	x( 'a.cancel-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		pinnedSelect.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		pinnedSelect.find( 'select' ).val( x( '#hidden-pinned-status' ).val() === '0' ? 1 : 0 );
	});

	x( 'a.edit-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		if( pinnedSelect.is( ':hidden' ) ) {
			pinnedSelect.slideDown( 'fast', function() {
				pinnedSelect.find( 'select' ).focus();
			});
			x( this ).hide();
		}
	});

	x( 'a.save-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		pinnedSelect.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		GLSR.pinned.save( x( this ) );
	});

	x( 'table' ).on( 'click', 'td.sticky i', GLSR.pinned.onToggle );
};

GLSR.pinned.onToggle = function()
{
	var el = x( this );

	var data = {
		action: site_reviews.action,
		request: {
			action: 'toggle-pinned',
			id: el[0].getAttribute( 'data-id' ),
		},
	};

	x.post( site_reviews.ajaxurl, data, function( response ) {
		if( response.pinned ) {
			el.addClass( 'pinned' );
		}
		else {
			el.removeClass( 'pinned' );
		}
	});
};

GLSR.pinned.save = function( el )
{
	var data = {
		action: site_reviews.action,
		request: {
			action: 'toggle-pinned',
			id:     x( '#post_ID' ).val(),
			pinned: x( '#pinned-status' ).val(),
		},
	};

	x.post( site_reviews.ajaxurl, data, function( response ) {
		x( '#pinned-status' ).val( !response.pinned|0 );
		x( '#hidden-pinned-status' ).val( response.pinned|0 );
		x( '#pinned-status-text' ).text( response.pinned ? el.data( 'yes' ) : el.data( 'no' ) );

		GLSR.insertNotices( response.notices );
	});
};

GLSR.search = function( el, options )
{
	this.el = Object.prototype.toString.call( el ) === '[object String]' ? x( el ) : el;
	this.options = options;
	this.searchTerm = null;
	this.init();
};

GLSR.search.prototype =
{
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
	init: function()
	{
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
	initEvents: function()
	{
		this.options.searchEl.on( 'input', _.debounce( this.onSearchInput.bind( this ), 500 ));
		this.options.searchEl.on( 'keyup', this.onSearchKeyup.bind( this ));
		x( document ).on( 'click', this.onDocumentClick.bind( this ));
		x( document ).on( 'keydown', this.onDocumentKeydown.bind( this ));
	},

	/** @return void */
	abort: function()
	{
		if( 'undefined' === typeof this.searchRequest )return;
		this.searchRequest.abort();
	},

	/** @return void */
	clearResults: function()
	{
		this.abort();
		this.options.resultsEl.empty();
		this.el.removeClass( 'is-active' );
		x( 'body' ).removeClass( 'glsr-focus' );
	},

	/** @return void */
	displayResults: function( items )
	{
		x( 'body' ).addClass( 'glsr-focus' );
		this.options.resultsEl.append( items );
		this.options.resultsEl.children( 'span' ).on( 'click', this.onResultClick.bind( this ));
	},

	/** @return void */
	navigateResults: function( diff )
	{
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
	onDocumentClick: function( ev )
	{
		if( x( ev.target ).find( this.el ).length && x( 'body' ).hasClass( 'glsr-focus' )) {
			this.clearResults();
		}
	},

	/** @return void */
	onDocumentKeydown: function( ev )
	{
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

	/** @return void */
	onResultClick: function( ev )
	{
		ev.preventDefault();
		if( typeof this.options.onResultClick === 'function' ) {
			this.options.onResultClick.call( this, ev );
		}
		this.clearResults();
	},

	/** @return void */
	onSearchInput: function( ev )
	{
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
	onSearchKeyup: function( ev )
	{
		if( GLSR.keys.ESC === ev.which ) {
			this.reset();
		}
		if( GLSR.keys.ENTER === ev.which ) {
			this.onSearchInput( ev );
		}
	},

	/** @return void */
	reset: function()
	{
		this.clearResults();
		this.options.results = {};
		this.options.searchEl.val( '' );
	},
};

// Manage Entries

GLSR.search.prototype.deleteEntry = function( index )
{
	var row = this.options.entriesEl.children( 'tr' ).eq( index );
	var search = this;
	row.find( 'td' ).css({ backgroundColor:'#faafaa' });
	row.fadeOut( 350, function() {
		x( this ).remove();
		search.options.results = {};
		search.reindexRows();
		search.setVisibility();
	});
};

GLSR.search.prototype.makeSortable = function()
{
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
};

GLSR.search.prototype.onEntryDelete = function( ev )
{
	ev.preventDefault();
	this.deleteEntry( x( ev.target ).closest( 'tr' ).index() );
};

GLSR.search.prototype.onUnassign = function( ev )
{
	ev.preventDefault();
	var assigned = this.el.find( '.description' );
	this.el.find( 'input#assigned_to' ).val( '' );
	assigned.find( 'a' ).css({ color:'#c00' });
	assigned.fadeOut( 'fast', function() {
		x( this ).html( '' ).show();
	});
};

GLSR.search.prototype.reindexRows = function()
{
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
};

GLSR.search.prototype.setVisibility = function()
{
	var action = this.options.entriesEl.children().length > 0 ? 'remove' : 'add';
	this.options.entriesEl.parent()[action + 'Class']( 'glsr-hidden' );
};

GLSR.shortcode.close = function( el )
{
	var button = x(( el = el || '.glsr-mce-button' ));
	if( button.length ) {
		button.removeClass( 'active' ).parent().find( '.glsr-mce-menu' ).hide();
	}
};

GLSR.shortcode.open = function( el )
{
	x( el ).addClass( 'active' ).parent().find( '.glsr-mce-menu' ).show();
};

GLSR.shortcode.toggle = function( ev )
{
	ev.preventDefault();
	if( x( this ).hasClass( 'active' ) ) {
		GLSR.shortcode.close( this );
	}
	else {
		GLSR.shortcode.open( this );
	}
};

GLSR.shortcode.trigger = function( ev )
{
	ev.preventDefault();
	// GLSR.shortcode.current is used by scForm to trigger the correct popup
	GLSR.shortcode.current = x( this ).attr( 'data-shortcode' );
	if( !GLSR.shortcode.current )return;
	if( !tinymce.get( window.wpActiveEditor ) ) {
		// Quicktags Editor
		if( !x( '#scTemp' ).length ) {
			x( 'body' ).append( '<textarea id="scTemp" style="display: none;" />' );
			tinymce.init({
				mode     : 'exact',
				elements : 'scTemp',
				plugins  : ['glsr_shortcode', 'wplink']
			});
		}
		setTimeout( function() {
			tinymce.execCommand( 'GLSR_Shortcode' );
		}, 200 );
	}
	else {
		// TinyMCE Editor
		tinymce.execCommand( 'GLSR_Shortcode' );
	}
	setTimeout( function() {
		GLSR.shortcode.close();
	}, 100 );
};

GLSR.shortcode.create = function( editor_id )
{
	var editor = tinymce.get( editor_id );
	if( !editor )return;
	var data = {
		action: site_reviews.action,
		request: {
			action: 'mce-shortcode',
			nonce: x( '#_glsr_nonce' ).val(),
			shortcode: GLSR.shortcode.current,
		},
	};
	x.post( site_reviews.ajaxurl, data, function( response )
	{
		if( !response.body )return;
		if( response.body.length === 0 ) {
			window.send_to_editor( '[' + response.shortcode + ']' );
			GLSR.shortcode.destroy();
			return;
		}
		var buttons = [{
			text    : response.ok,
			classes : 'btn glsr-btn primary',
			onclick : function() {
				var field, required, valid, win;
				// Get the top most window object
				win = editor.windowManager.getWindows()[0];
				// Get the shortcode required attributes
				required = site_reviews.shortcodes[ GLSR.shortcode.current ];
				valid = true;
				// Do some validation voodoo
				for( var id in required ) {
					if( !required.hasOwnProperty( id ) )continue;
					field = win.find( '#' + id )[0];
					if( typeof field !== 'undefined' && field.state.data.value === '' ) {
						valid = false;
						alert( required[ id ] );
						break;
					}
				}
				if( valid ) {
					win.submit();
				}
			}
		},{
			text    : response.close,
			onclick : 'close'
		}];
		var popup = {
			title   : response.title,
			body    : response.body,
			classes: 'glsr-mce-popup',
			minWidth: 320,
			buttons : buttons,
			onsubmit: function( e ) {
				var attributes = '';
				var data = GLSR.shortcode.normalize( e.data );
				for( var key in data ) {
					if( data.hasOwnProperty( key ) && data[ key ] !== '' ) {
						attributes += ' ' + key + '="' + data[ key ] + '"';
					}
				}
				// Insert shortcode into the WP_Editor
				window.send_to_editor( '[' + response.shortcode + attributes + ']' );
			},
			onclose: function() {
				GLSR.shortcode.destroy();
			}
		};
		// Change the buttons if server-side validation failed
		if( response.ok.constructor === Array ) {
			popup.buttons[0].text    = response.ok[0];
			popup.buttons[0].onclick = 'close';
			delete popup.buttons[1];
		}
		editor.windowManager.open( popup );
	});
};

GLSR.shortcode.normalize = function( data )
{
	var shortcodeHiddenFields = {
		'site_reviews' : ['author','date','excerpt','rating','response','title'],
		'site_reviews_form': ['email','name','terms','title'],
		'site_reviews_summary': ['bars','if_empty','rating','stars','summary'],
	};
	var hide = [];
	for( var key in data ) {
		if( !data.hasOwnProperty( key ) )continue;
		if( shortcodeHiddenFields.hasOwnProperty( GLSR.shortcode.current ) ) {
			var value = '';
			if( key.lastIndexOf( 'hide_', 0 ) === 0 ) {
				value = key.substring(5);
			}
			if( shortcodeHiddenFields[ GLSR.shortcode.current ].indexOf( value ) > -1 ) {
				if( data[ key ] ) {
					hide.push( value );
				}
				delete data[ key ];
			}
		}
		if( key === 'count' && !x.isNumeric( data[ key ] ) ) {
			data[ key ] = '';
		}
		if( key === 'id' ) {
			data[ key ] = (+new Date()).toString(36);
		}
	}
	data.hide = hide.join( ',' );
	return data;
};

GLSR.shortcode.destroy = function()
{
	var tmp = x( '#scTemp' );
	if( tmp.length ) {
		tinymce.get( 'scTemp' ).remove();
		tmp.remove();
	}
};

/** global: GLSR, site_reviews_pointers, wp, x */

x( function()
{
	var GLSR_fix = GLSR.getURLParameter( 'fix' );
	var GLSR_textarea = x( '#contentdiv > textarea' );

	if( GLSR_fix ) {
		x( 'td [data-key="' + GLSR_fix + '"]').focus();
	}

	if( GLSR_textarea.length ) {
		GLSR.textareaResize( GLSR_textarea );

		x( document ).on( 'wp-window-resized.editor-expand', function() {
			GLSR.textareaResize( GLSR_textarea );
		});
	}

	x( 'form' ).on( 'change', ':input', GLSR.onFieldChange );
	x( 'form' ).on( 'click', '#clear-log', GLSR.onClearLog );

	GLSR.colorControls();
	GLSR.pinned.events();

	x.each( site_reviews_pointers.pointers, function( i, pointer ) {
		GLSR.pointers( pointer );
	});

	x( document ).on( 'click', function( ev )
	{
		if( !x( ev.target ).closest( '.glsr-mce' ).length ) {
			GLSR.shortcode.close();
		}
	});

	x( document ).on( 'click', '.glsr-mce-button', GLSR.shortcode.toggle );
	x( document ).on( 'click', '.glsr-mce-menu-item', GLSR.shortcode.trigger );
	x( document ).on( 'click', 'a.change-site-review-status', GLSR.onChangeStatus );

	// WP 4.0 - 4.2 support: toggle list table rows on small screens
	x( document ).on( 'click', '.branch-4 .toggle-row, .branch-4-1 .toggle-row, .branch-4-2 .toggle-row', function() {
		x( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
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
});
