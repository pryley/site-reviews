/** global: editor, GLSR, jQuery, tinymce, tinyMCEPreInit */
;(function( $ ) {

	'use strict';

	GLSR.Shortcode = function( selector ) {
		this.current = null; // this.current is used by scForm to trigger the correct popup
		this.editor = null;
		this.create = function( editor_id ) {
			this.editor = tinymce.get( editor_id );
			if( !this.editor )return;
			var request = {
				_action: 'mce-shortcode',
				shortcode: this.current,
			};
			(new GLSR.Ajax( request )).post( this.handleResponse_.bind( this ));
		};
		var selectors = document.querySelectorAll( selector );
		if( !selectors.length )return;
		selectors.forEach( function( el ) {
			var button = el.querySelector( 'button' );
			var menuItems = el.querySelectorAll( '.mce-menu-item' );
			if( !button || !menuItems.length )return;
			this.init_( el, button, menuItems );
		}.bind( this ));
	};

	GLSR.Shortcode.prototype = {
		attributes_: {},
		hiddenKeys_: [],

		/** @return void */
		init_: function( el, button, menuItems ) {
			document.addEventListener( 'click', this.onClose_.bind( this, el, button ));
			button.addEventListener( 'click', this.onToggle_.bind( this, el, button ));
			menuItems.forEach( function( item ) {
				item.addEventListener( 'click', this.onTrigger_.bind( this, el, button ));
			}.bind( this ));
		},

		/** @return void */
		initTinymceEditor_: function() {
			tinymce.execCommand( 'GLSR_Shortcode' );
		},

		/** @return void */
		initQuicktagsEditor_: function() {
			if( $( '#scTemp' ).length ) {
				this.initTinymceEditor_();
				return;
			}
			$( 'body' ).append( '<textarea id="scTemp" style="display:none!important;"/>' );
			tinymce.init({
				elements: 'scTemp',
				external_plugins: GLSR.tinymce,
				mode: 'exact',
				plugins: ['glsr_shortcode', 'wplink'],
			});
			setTimeout( function() {
				this.initTinymceEditor_();
			}.bind( this ), 200 );
		},

		/** @return void */
		close_: function( el, button ) {
			$( button ).removeClass( 'active' );
			$( el ).find( '.glsr-mce-menu' ).hide();
		},

		/** @return void */
		destroy_: function() {
			var tmp = $( '#scTemp' );
			if( tmp.length ) {
				tinymce.get( 'scTemp' ).remove();
				tmp.remove();
			}
			this.attributes_ = {};
			this.hiddenKeys_ = [];
		},

		/** @return void */
		handleResponse_: function( response ) {
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

		/** @return void */
		normalize_: function( attributes ) {
			this.attributes_ = attributes;
			this.hiddenKeys_ = [];
			for( var key in attributes ) {
				if( !attributes.hasOwnProperty( key ))continue;
				this.normalizeDisplay_( key );
				this.normalizeHide_( key );
				this.normalizeId_( key );
			}
			this.attributes_.hide = this.hiddenKeys_.join( ',' );
		},

		/** @return void */
		normalizeDisplay_: function( key ) {
			if( 'display' !== key || $.isNumeric( this.attributes_[key] ))return;
			this.attributes_[key] = '';
		},

		/** @return void */
		normalizeHide_: function( key ) {
			if( !GLSR.hideoptions.hasOwnProperty( this.current ))return;
			var value = key.substring('hide_'.length);
			if( Object.keys( GLSR.hideoptions[this.current] ).indexOf( value ) === -1 )return;
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
		onClose_: function( el, button, ev ) {
			if( $( ev.target ).closest( $( el )).length )return;
			this.close_( el, button );
		},

		/** @return void */
		onToggle_: function( el, button, ev ) {
			ev.preventDefault();
			if( ev.currentTarget.classList.contains( 'active' )) {
				this.close_( el, button );
				return;
			}
			this.open_( el, button );
		},

		/** @return void */
		onTrigger_: function( el, button, ev ) {
			ev.preventDefault();
			this.current = ev.currentTarget.dataset.shortcode;
			if( !this.current )return;
			if( tinymce.get( window.wpActiveEditor )) {
				this.initTinymceEditor_();
			}
			else {
				this.initQuicktagsEditor_();
			}
			setTimeout( function() {
				this.close_( el, button );
			}.bind( this ), 100 );
		},

		/** @return void */
		open_: function( el, button ) {
			$( button ).addClass( 'active' );
			$( el ).find( '.glsr-mce-menu' ).show();
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
				if( !requiredAttributes.hasOwnProperty( id ))continue;
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
})( jQuery );
