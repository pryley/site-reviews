/** global: editor, GLSR, jQuery, tinymce */
;(function( $ ) {

	'use strict';

	GLSR.Shortcode = function( selector ) {
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
			(new GLSR.Ajax( request )).post_( this.handleResponse_.bind( this ));
		};
		this.init_();
	};

	GLSR.Shortcode.prototype = {
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
			if( $( '#scTemp' ).length ) {
				this.initTinymceEditor_();
				return;
			}
			$( 'body' ).append( '<textarea id="scTemp" style="display:none!important;"/>' );
			tinymce.init({
				elements: 'scTemp',
				mode: 'exact',
				plugins: ['glsr_shortcode', 'wplink'],
			});
			setTimeout( function() {
				this.initTinymceEditor_();
			}.bind( this ), 200 );
		},

		/** @return void */
		close_: function() {
			$( this.button ).removeClass( 'active' );
			$( this.el ).find( '.glsr-mce-menu' ).hide();
		},

		/** @return void */
		destroy_: function() {
			var tmp = $( '#scTemp' );
			if( tmp.length ) {
				tinymce.get( 'scTemp' ).remove();
				tmp.remove();
			}
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
				this.normalizeCount_( key );
				this.normalizeHide_( key );
				this.normalizeId_( key );
			}
			this.attributes_.hide = this.hiddenKeys_.join( ',' );
		},

		/** @return void */
		normalizeCount_: function( key ) {
			if( key !== 'count' || $.isNumeric( this.attributes_[key] ))return;
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
			if( $( ev.currentTarget ).closest( $( this.el )).length )return;
			this.close_();
		},

		/** @return void */
		onToggle_: function( ev ) {
			ev.preventDefault();
			if( ev.currentTarget.classList.contains( 'active' )) {
				this.close_();
				return;
			}
			this.open_();
		},

		/** @return void */
		onTrigger_: function( ev ) {
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
				this.close_();
			}.bind( this ), 100 );
		},

		/** @return void */
		open_: function() {
			$( this.button ).addClass( 'active' );
			$( this.el ).find( '.glsr-mce-menu' ).show();
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
})( jQuery );
