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

GLSR.forms = function( selector ) {
	this.el = document.querySelector( selector );
	if( !this.el )return;
	this.depends = this.el.querySelectorAll( '[data-depends]' );
	if( !this.depends.length )return;
	this.init();
};

GLSR.forms.prototype = {

	/** @return void */
	init: function() {
		var formControls = this.el.elements;
		for( var i = 0; i < formControls.length; i++ ) {
			if( ['INPUT', 'SELECT'].indexOf( formControls[i].nodeName ) === -1 )continue;
			formControls[i].addEventListener( 'change', this.onChange.bind( this ));
		}
	},

	/** @return bool */
	isSelected: function( el, dependency ) {
		if( 'checkbox' === el.type ) {
			return !!el.checked;
		}
		else if( Array.isArray( dependency.value )) {
			return this.normalizeValues( dependency.value ).indexOf( this.normalizeValue( el.value )) !== -1;
		}
		return this.normalizeValue( dependency.value ) === this.normalizeValue( el.value );
	},

	/** @return bool|string */
	normalizeValue: function( value ) {
		if(['true','on','yes','1'].indexOf( value ) !== -1 ) {
			return true;
		}
		if(['false','off','no','0'].indexOf( value ) !== -1 ) {
			return false;
		}
		return value;
	},

	/** @return array */
	normalizeValues: function( values ) {
		return values.map( this.normalizeValue );
	},

	/** @return void */
	onChange: function( ev ) {
		this.depends.forEach( function( el ) {
			var data = el.getAttribute( 'data-depends' );
			if( !data )return;
			var dependency;
			try {
				dependency = JSON.parse( data );
			}
			catch( error ) {
				console.log( data );
				return console.error( error );
			}
			if( dependency.name !== ev.target.name.replace( '[]', '' ))return;
			this.toggleHiddenField( el, this.isSelected( ev.target, dependency ));
		}.bind( this ));
	},

	/** @return void */
	toggleHiddenField: function( el, bool ) {
		var row = el.closest( '.glsr-field' );
		if( !row )return;
		row.classList[bool ? 'remove' : 'add']( 'hidden' );
	},
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
	return value === void(0);
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
		if( !response.class )return;
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

GLSR.tabs = function( options ) {
	this.options = x.extend( {}, this.defaults, options );
	this.active = document.querySelector( 'input[name=_active_tab]' );
	this.referrer = document.querySelector( 'input[name=_wp_http_referer]' );
	this.tabs = document.querySelectorAll( this.options.tabSelector );
	this.views = document.querySelectorAll( this.options.viewSelector );
	if( !this.active || !this.referrer || !this.tabs || !this.views )return;
	this.init();
};

GLSR.tabs.prototype = {

	defaults: {
		tabSelector: '.glsr-nav-tab',
		viewSelector: '.glsr-nav-view',
	},

	/** @return void */
	init: function() {
		[].forEach.call( this.tabs, function( tab, index ) {
			var active = location.hash ? tab.getAttribute( 'href' ).slice(1) === location.hash.slice(2) : index === 0;
			if( active ) {
				this.setTab( tab );
			}
			tab.addEventListener( 'click', this.onClick.bind( this ));
			tab.addEventListener( 'touchend', this.onClick.bind( this ));
		}.bind( this ));
	},

	/** @return string */
	getAction: function( bool ) {
		return bool ? 'add' : 'remove';
	},

	/** @return void */
	onClick: function( ev ) {
		ev.preventDefault();
		ev.target.blur();
		this.setTab( ev.target );
		location.hash = '!' + ev.target.getAttribute( 'href' ).slice(1);
	},

	/** @return void */
	setReferrer: function( index ) {
		var referrerUrl = this.referrer.value.split('#')[0] + '#!' + this.views[index].id;
		this.referrer.value = referrerUrl;
	},

	/** @return void */
	setTab: function( el ) {
		[].forEach.call( this.tabs, function( tab, index ) {
			var action = this.getAction( tab === el );
			if( action === 'add' ) {
				this.active.value = this.views[index].id;
				this.setReferrer( index );
				this.setView( index );
			}
			tab.classList[action]( 'nav-tab-active' );
		}.bind( this ));
	},

	/** @return void */
	setView: function( idx ) {
		[].forEach.call( this.views, function( view, index ) {
			var action = this.getAction( index !== idx );
			view.classList[action]( 'ui-tabs-hide' );
		}.bind( this ));
	},
};

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
