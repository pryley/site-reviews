/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Ajax = function( request, ev ) { // object
		this.event = ev || null;
		this.post = this.post_;
		this.request = request;
	};

	Ajax.prototype = {
		/** @return void */
		buildData_: function( el ) { // HTMLElement|null
			this.buildNonce_( el );
			return {
				action: GLSR.action,
				ajax_request: true,
				request: this.request,
			};
		},

		/** @return void */
		buildNonce_: function( el ) { // HTMLElement|null
			if( this.request.nonce )return;
			if( GLSR.nonce[this.request.action] ) {
				this.request.nonce = GLSR.nonce[this.request.action];
				return;
			}
			if( !el )return;
			this.request.nonce = el.closest( 'form' ).find( '#_wpnonce' ).val();
		},

		/** @return void */
		post_: function( callback ) { // function|void
			if( this.event ) {
				this.postFromEvent_( callback );
				return;
			}
			x.post( GLSR.ajaxurl, this.buildData_(), function( response ) {
				if( typeof callback !== 'function' )return;
				callback( response );
			});
		},

		/** @return void */
		postFromEvent_: function( callback ) { // Event, function|void
			this.event.preventDefault();
			var el = x( this.event.target );
			if( el.is( ':disabled' ))return;
			el.prop( 'disabled', true );
			x.post( GLSR.ajaxurl, this.buildData_( el ), function( response ) {
				if( typeof callback === 'function' ) {
					callback( response );
				}
				el.prop( 'disabled', false );
			});
		},
	};

	GLSR.Ajax = Ajax;
})( jQuery );

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	GLSR.ColorPicker = function() {
		if( typeof x.wp !== 'object' || typeof x.wp.wpColorPicker !== 'function' )return;
		x( document ).find( 'input[type=text].color-picker-hex' ).each( function() {
			x( this ).wpColorPicker( x( this ).data( 'colorpicker' ) || {} );
		});
	};
})( jQuery );

/** global: GLSR */
;(function() {

	'use strict';

	var Forms = function( selector ) {
		this.el = document.querySelector( selector );
		if( !this.el )return;
		this.depends = this.el.querySelectorAll( '[data-depends]' );
		if( !this.depends.length )return;
		this.init_();
	};

	Forms.prototype = {
		/** @return void */
		init_: function() {
			var formControls = this.el.elements;
			for( var i = 0; i < formControls.length; i++ ) {
				if( ['INPUT', 'SELECT'].indexOf( formControls[i].nodeName ) === -1 )continue;
				formControls[i].addEventListener( 'change', this.onChange_.bind( this ));
			}
		},

		/** @return bool */
		isSelected_: function( el, dependency ) {
			if( 'checkbox' === el.type ) {
				return !!el.checked;
			}
			else if( Array.isArray( dependency.value )) {
				return this.normalizeValues_( dependency.value ).indexOf( this.normalizeValue_( el.value )) !== -1;
			}
			return this.normalizeValue_( dependency.value ) === this.normalizeValue_( el.value );
		},

		/** @return bool|string */
		normalizeValue_: function( value ) {
			if(['true','on','yes','1'].indexOf( value ) !== -1 ) {
				return true;
			}
			if(['false','off','no','0'].indexOf( value ) !== -1 ) {
				return false;
			}
			return value;
		},

		/** @return array */
		normalizeValues_: function( values ) {
			return values.map( this.normalizeValue_ );
		},

		/** @return void */
		onChange_: function( ev ) {
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
				this.toggleHiddenField_( el, this.isSelected_( ev.target, dependency ));
			}.bind( this ));
		},

		/** @return void */
		toggleHiddenField_: function( el, bool ) {
			var row = el.closest( '.glsr-field' );
			if( !row )return;
			row.classList[bool ? 'remove' : 'add']( 'hidden' );
		},
	};

	GLSR.Forms = Forms;
})();

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Logger = function() {
		x( 'form' ).on( 'click', '#clear-log', this.onClick_ );
	};

	Logger.prototype = {
		onClick_: function( ev ) {
		 	var request = {
				action: 'clear-log',
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				GLSR.Notices( response.notices );
				x( '#log-file' ).val( response.logger );
			});
		},
	};

	GLSR.Logger = Logger;
})( jQuery );

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	GLSR.Notices = function( notices ) { // string
		if( !notices )return;
		if( !x( '#glsr-notices' ).length ) {
			x( '#message.notice' ).remove();
			x( 'form#post' ).before( '<div id="glsr-notices" />' );
		}
		x( '#glsr-notices' ).html( notices );
		x( document ).trigger( 'wp-updates-notice-added' );
	};
})( jQuery );

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

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Pointers = function() {
		x.each( GLSR.pointers, function( i, pointer ) {
			this.init_( pointer );
		}.bind( this ));
	};

	Pointers.prototype = {
		/** @return void */
		close_: function( pointerId ) { // string
			x.post( GLSR.ajaxurl, {
				action: 'dismiss-wp-pointer',
				pointer: pointerId,
			});
		},

		/** @return void */
		init_: function( pointer ) { // object
			x( pointer.target ).pointer({
				content: pointer.options.content,
				position: pointer.options.position,
				close: this.close_.bind( pointer.id ),
			})
			.pointer( 'open' )
			.pointer( 'sendToTop' );
			x( document ).on( 'wp-window-resized', function() {
				x( pointer.target ).pointer( 'reposition' );
			});
		},
	};

	GLSR.Pointers = Pointers;
})( jQuery );

/** global: GLSR, jQuery */
;(function( _, wp, x ) {

	'use strict';

	var Search = function( selector, options ) {
		this.el = x( selector );
		this.options = options;
		this.searchTerm = null;
		this.init_();
	};

	Search.prototype = {
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
		init_: function() {
			this.options = x.extend( {}, this.defaults, this.options );
			if( !this.el.length )return;
			this.options.entriesEl = this.el.parent().find( this.options.selectorEntries );
			this.options.resultsEl = this.el.find( this.options.selectorResults );
			this.options.searchEl = this.el.find( this.options.selectorSearch );
			this.options.searchEl.attr( 'aria-describedby', 'live-search-desc' );
			if( typeof this.options.onInit === 'function' ) {
				this.options.onInit.call( this );
			}
			this.initEvents_();
		},

		/** @return void */
		initEvents_: function() {
			this.options.searchEl.on( 'input', _.debounce( this.onSearchInput_.bind( this ), 500 ));
			this.options.searchEl.on( 'keyup', this.onSearchKeyup_.bind( this ));
			this.options.searchEl.on( 'keydown keypress', function( ev ) {
				if( GLSR.keys.ENTER !== ev.which )return;
				ev.preventDefault();
			});
			x( document ).on( 'click', this.onDocumentClick_.bind( this ));
			x( document ).on( 'keydown', this.onDocumentKeydown_.bind( this ));
		},

		/** @return void */
		abort_: function() {
			if( 'undefined' === typeof this.searchRequest )return;
			this.searchRequest.abort();
		},

		/** @return void */
		clearResults_: function() {
			this.abort_();
			this.options.resultsEl.empty();
			this.el.removeClass( 'is-active' );
			x( 'body' ).removeClass( 'glsr-focus' );
		},

		/** @return void */// Manage entries
		deleteEntry_: function( index ) {
			var row = this.options.entriesEl.children( 'tr' ).eq( index );
			var search = this;
			row.find( 'td' ).css({ backgroundColor:'#faafaa' });
			row.fadeOut( 350, function() {
				x( this ).remove();
				search.options.results = {};
				search.reindexRows_();
				search.setVisibility_();
			});
		},

		/** @return void */
		displayResults_: function( items ) {
			x( 'body' ).addClass( 'glsr-focus' );
			this.options.resultsEl.append( items );
			this.options.resultsEl.children( 'span' ).on( 'click', this.onResultClick_.bind( this ));
		},

		/** @return void */// Manage entries
		makeSortable_: function() {
			this.options.entriesEl.on( 'click', 'a.delete', this.onEntryDelete_.bind( this ));
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
		navigateResults_: function( diff ) {
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
		onDocumentClick_: function( ev ) {
			if( x( ev.target ).find( this.el ).length && x( 'body' ).hasClass( 'glsr-focus' )) {
				this.clearResults_();
			}
		},

		/** @return void */
		onDocumentKeydown_: function( ev ) {
			if( !this.options.results )return;
			if( GLSR.keys.ESC === ev.which ) {
				this.clearResults_();
			}
			if( GLSR.keys.ENTER === ev.which || GLSR.keys.SPACE === ev.which ) {
				var selected = this.options.resultsEl.find( '.' + this.options.selectedClass );
				if( selected ) {
					selected.trigger( 'click' );
				}
			}
			if( GLSR.keys.UP === ev.which ) {
				ev.preventDefault();
				this.navigateResults_(-1);
			}
			if( GLSR.keys.DOWN === ev.which ) {
				ev.preventDefault();
				this.navigateResults_(1);
			}
		},

		/** @return void */// Manage entries
		onEntryDelete_: function( ev ) {
			ev.preventDefault();
			this.deleteEntry_( x( ev.target ).closest( 'tr' ).index() );
		},

		/** @return void */
		onResultClick_: function( ev ) {
			ev.preventDefault();
			if( typeof this.options.onResultClick === 'function' ) {
				this.options.onResultClick.call( this, ev );
			}
			this.clearResults_();
		},

		/** @return void */
		onSearchInput_: function( ev ) {
			this.abort_();
			if( this.searchTerm === ev.target.value && this.options.results.length ) {
				return this.displayResults_( this.options.results );
			}
			this.options.resultsEl.empty();
			this.options.selected = -1;
			this.searchTerm = ev.target.value;
			if( this.searchTerm === '' ) {
				return this.reset_();
			}
			this.el.addClass( 'is-active' );
			this.searchRequest = wp.ajax.post( GLSR.action, {
				request: {
					action: this.options.action,
					exclude: this.options.exclude,
					nonce: this.el.find( '#_search_nonce' ).val(),
					search: this.searchTerm,
				},
			}).done( function( response ) {
				this.el.removeClass( 'is-active' );
				this.displayResults_( response.items ? response.items : response.empty );
				this.options.results = this.options.resultsEl.children();
				delete this.searchRequest;
			}.bind( this ));
		},

		/** @return void */
		onSearchKeyup_: function( ev ) {
			if( GLSR.keys.ESC === ev.which ) {
				this.reset_();
			}
			if( GLSR.keys.ENTER === ev.which ) {
				this.onSearchInput_( ev );
				ev.preventDefault();
			}
		},

		/** @return void */// Manage entries
		onUnassign_: function( ev ) {
			ev.preventDefault();
			var assigned = this.el.find( '.description' );
			this.el.find( 'input#assigned_to' ).val( '' );
			assigned.find( 'a' ).css({ color:'#c00' });
			assigned.fadeOut( 'fast', function() {
				x( this ).html( '' ).show();
			});
		},

		/** @return void */// Manage entries
		reindexRows_: function() {
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
		reset_: function() {
			this.clearResults_();
			this.options.results = {};
			this.options.searchEl.val( '' );
		},

		/** @return void */// Manage entries
		setVisibility_: function() {
			var action = this.options.entriesEl.children().length > 0 ? 'remove' : 'add';
			this.options.entriesEl.parent()[action + 'Class']( 'glsr-hidden' );
		},
	};

	GLSR.Search = Search;
})( window._, window.wp, jQuery );

/** global: GLSR, jQuery */
;(function( editor, tinymce, x ) {

	'use strict';

	var Shortcode = function( selector ) {
		this.el = document.querySelector( selector );
		if( !this.el )return;
		this.current = null; // this.current is used by scForm to trigger the correct popup
		this.editor = null;
		this.button = this.el.querySelector( 'button' );
		this.menuItems = this.el.querySelectorAll( '.mce-menu-item' );
		if( !this.button || !this.menuItems.length )return;
		this.create = function( editor_id ) {
			this.editor = tinymce.get( editor_id );
			if( !this.editor )return;
			var request = {
				action: 'mce-shortcode',
				shortcode: this.current,
			};
			(new GLSR.Ajax( request )).post( this.response_.bind( this ));
		};
		this.init_();
	};

	Shortcode.prototype = {
		attributes_: {},
		hiddenKeys_: [],

		/** @return void */
		init_: function() {
			document.addEventListener( 'click', this.onClose_.bind( this ));
			this.button.addEventListener( 'click', this.onToggle_.bind( this ));
			this.menuItems.forEach( function( item ) {
				item.addEventListener( 'click', this.onTrigger_.bind( this ));
			}.bind( this ));
		},

		/** @return void */
		initTinymceEditor_: function() {
			tinymce.execCommand( 'GLSR_Shortcode' );
		},

		/** @return void */
		initQuicktagsEditor_: function() {
			if( x( '#scTemp' ).length ) {
				this.initTinymceEditor_();
				return;
			}
			x( 'body' ).append( '<textarea id="scTemp" style="display:none!important;"/>' );
			tinymce.init({
				elements: 'scTemp',
				mode: 'exact',
				plugins: ['glsr_shortcode', 'wplink'],
			});
			setTimeout( function() {
				this.initTinymceEditor_();
			}, 200 );
		},

		/** @return void */
		close_: function() {
			x( this.button ).removeClass( 'active' );
			x( this.el ).find( '.glsr-mce-menu' ).hide();
		},

		/** @return void */
		destroy_: function() {
			var tmp = x( '#scTemp' );
			if( tmp.length ) {
				tinymce.get( 'scTemp' ).remove();
				tmp.remove();
			}
		},

		/** @return void */
		normalize_: function( attributes ) {
			this.attributes_ = attributes;
			this.hiddenKeys_ = [];
			for( var key in attributes ) {
				if( !attributes.hasOwnProperty( key ))continue;
				this.normalizeCount_( key );
				this.normalizeHide_( key );
				this.normalizeId_( key );
			}
			this.attributes_.hide = this.hiddenKeys_.join( ',' );
		},

		/** @return void */
		normalizeCount_: function( key ) {
			if( key !== 'count' || x.isNumeric( this.attributes_[key] ))return;
			this.attributes_[key] = '';
		},

		/** @return void */
		normalizeHide_: function( key ) {
			if( !GLSR.hiddenkeys.hasOwnProperty( this.current ))return;
			var value = key.substring('hide_'.length);
			if( GLSR.hiddenkeys[this.current].indexOf( value ) === -1 )return;
			if( this.attributes_[key] ) {
				this.hiddenKeys_.push( value );
			}
			delete this.attributes_[key];
		},

		/** @return void */
		normalizeId_: function( key ) {
			if( key !== 'id' )return;
			this.attributes_[key] = (+new Date()).toString(36);
		},

		/** @return void */
		onClose_: function( ev ) {
			if( x( ev.target ).closest( x( this.el )).length )return;
			this.close_();
		},

		/** @return void */
		onToggle_: function( ev ) {
			ev.preventDefault();
			if( ev.target.classList.contains( 'active' )) {
				this.close_();
				return;
			}
			this.open_();
		},

		/** @return void */
		onTrigger_: function( ev ) {
			ev.preventDefault();
			this.current = ev.target.dataset.shortcode;
			if( !this.current )return;
			if( tinymce.get( window.wpActiveEditor )) {
				this.initTinymceEditor_();
			}
			else {
				this.initQuicktagsEditor_();
			}
			setTimeout( function() {
				this.close_();
			}.bind( this ), 100 );
		},

		/** @return void */
		open_: function() {
			x( this.button ).addClass( 'active' );
			x( this.el ).find( '.glsr-mce-menu' ).show();
		},

		/** @return void */
		response_: function( response ) {
			if( !response )return;
			if( response.body.length === 0 ) {
				window.send_to_editor( '[' + response.shortcode + ']' );
				this.destroy_();
				return;
			}
			var popup = this.responsePopup_( response );
			// Change the buttons if server-side validation failed
			if( response.ok.constructor === Array ) {
				popup.buttons[0].text = response.ok[0];
				popup.buttons[0].onclick = 'close';
				delete popup.buttons[1];
			}
			this.editor.windowManager.open( popup );
		},

		/** @return array */
		responseButtons_: function( response ) {
			return [{
				classes: 'btn glsr-btn primary',
				onclick: this.submitShortcode_.bind( this ),
				text: response.ok,
			},{
				onclick: 'close',
				text: response.close,
			}];
		},

		/** @return object */
		responsePopup_: function( response ) {
			return {
				title: response.title,
				body: response.body,
				classes: 'glsr-mce-popup',
				minWidth: 320,
				buttons: this.responseButtons_( response ),
				onsubmit: this.sendToEditor_.bind( this, response ),
				onclose: this.destroy_.bind( this ),
			};
		},

		/** @return void */
		sendToEditor_: function( response, ev ) {
			var attributes = '';
			this.normalize_( ev.data );
			for( var key in this.attributes_ ) {
				if( this.attributes_.hasOwnProperty( key ) && this.attributes_[key] !== '' ) {
					attributes += ' ' + key + '="' + this.attributes_[key] + '"';
				}
			}
			window.send_to_editor( '[' + response.shortcode + attributes + ']' );
		},

		/** @return void */
		submitShortcode_: function() {
			var currentWindow = this.editor.windowManager.getWindows()[0];
			if( !this.validateAttributes_( currentWindow ))return;
			currentWindow.submit();
		},

		/** @return bool */
		validateAttributes_: function( currentWindow ) {
			var field;
			var is_valid = true;
			var requiredAttributes = GLSR.shortcodes[this.current];
			for( var id in requiredAttributes ) {
				field = currentWindow.find( '#' + id )[0];
				if( typeof field !== 'undefined' && field.state.data.value === '' ) {
					is_valid = false;
					alert( requiredAttributes[id] );
					break;
				}
			}
			return is_valid;
		},
	};

	GLSR.Shortcode = Shortcode;
})( window.editor, window.tinymce, jQuery );

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Status = function( selector ) {
		var elements = document.querySelectorAll( selector );
		if( !elements.length )return;
		elements.forEach( function( el ) {
			el.addEventListener( 'click', this.onClick_ );
		}.bind( this ));
	};

	Status.prototype = {
		/** @return void */
		onClick_: function( ev ) { // MouseEvent
			var post_id = ev.target.href.match( /post=([0-9]+)/ );
			var status = ev.target.href.match( /action=([a-z]+)/ );
			if( post_id === null || status === null )return;
			var request = {
				action: 'change-review-status',
				nonce: GLSR.nonce['change-review-status'],
				post_id: post_id[1],
				status: status[1],
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				if( !response.class )return;
				var el = x( ev.target );
				el.closest( 'tr' ).removeClass( 'status-pending status-publish' ).addClass( response.class );
				el.closest( 'td.column-title' ).find( 'strong' ).html( response.link );
			});
		},
	};

	GLSR.Status = Status;
})( jQuery );

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Tabs = function( options ) {
		this.options = x.extend( {}, this.defaults, options );
		this.active = document.querySelector( 'input[name=_active_tab]' );
		this.referrer = document.querySelector( 'input[name=_wp_http_referer]' );
		this.tabs = document.querySelectorAll( this.options.tabSelector );
		this.views = document.querySelectorAll( this.options.viewSelector );
		if( !this.active || !this.referrer || !this.tabs || !this.views )return;
		this.init_();
	};

	Tabs.prototype = {
		defaults: {
			tabSelector: '.glsr-nav-tab',
			viewSelector: '.glsr-nav-view',
		},

		/** @return void */
		init_: function() {
			[].forEach.call( this.tabs, function( tab, index ) {
				var active = location.hash ? tab.getAttribute( 'href' ).slice(1) === location.hash.slice(2) : index === 0;
				if( active ) {
					this.setTab_( tab );
				}
				tab.addEventListener( 'click', this.onClick_.bind( this ));
				tab.addEventListener( 'touchend', this.onClick_.bind( this ));
			}.bind( this ));
		},

		/** @return string */
		getAction_: function( bool ) {
			return bool ? 'add' : 'remove';
		},

		/** @return void */
		onClick_: function( ev ) {
			ev.preventDefault();
			ev.target.blur();
			this.setTab_( ev.target );
			location.hash = '!' + ev.target.getAttribute( 'href' ).slice(1);
		},

		/** @return void */
		setReferrer_: function( index ) {
			var referrerUrl = this.referrer.value.split('#')[0] + '#!' + this.views[index].id;
			this.referrer.value = referrerUrl;
		},

		/** @return void */
		setTab_: function( el ) {
			[].forEach.call( this.tabs, function( tab, index ) {
				var action = this.getAction_( tab === el );
				if( action === 'add' ) {
					this.active.value = this.views[index].id;
					this.setReferrer_( index );
					this.setView_( index );
				}
				tab.classList[action]( 'nav-tab-active' );
			}.bind( this ));
		},

		/** @return void */
		setView_: function( idx ) {
			[].forEach.call( this.views, function( view, index ) {
				var action = this.getAction_( index !== idx );
				view.classList[action]( 'ui-tabs-hide' );
			}.bind( this ));
		},
	};

	GLSR.Tabs = Tabs;
})( jQuery );

/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var TextareaResize = function() {
		var textarea = document.querySelector( '#contentdiv > textarea' );
		if( !textarea )return;
		this.resize_( textarea );
		x( document ).on( 'wp-window-resized.editor-expand', function() {
			this.resize_( textarea );
		}.bind( this ));
	};

	TextareaResize.prototype = {
		/** @return void */
		resize_: function( textareaEl ) { // HTMLElement
			var minHeight = 320;
			var height = textareaEl.scrollHeight > minHeight ? textareaEl.scrollHeight : minHeight;
			textareaEl.style.height = 'auto';
			textareaEl.style.height = height + 'px';
		},
	};

	GLSR.TextareaResize = TextareaResize;
})( jQuery );

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
	new GLSR.Forms( 'form.glsr-form' );
	new GLSR.Logger();
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
