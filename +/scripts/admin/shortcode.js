/** global: editor, GLSR, site_reviews, tinymce, x */
GLSR.shortcode = function( selector ) {
	this.el = document.querySelector( selector );
	if( !this.el )return;
	this.current = null; // GLSR.shortcode.current is used by scForm to trigger the correct popup
	this.editor = null;
	this.button = this.el.querySelector( 'button' );
	this.menuItems = this.el.querySelectorAll( '.mce-menu-item' );
	if( !this.button || !this.menuItems.length )return;
	this.create = function( editor_id ) {
		this.editor = tinymce.get( editor_id );
		if( !this.editor )return;
		this.request({
			action: 'mce-shortcode',
			nonce: site_reviews.mce_nonce,
			shortcode: this.current,
		});
	};
	this.init();
};

GLSR.shortcode.prototype = {

	attributes: {},

	hiddenKeys: [],

	hiddenFields: {
		site_reviews: ['author','avatar','date','excerpt','rating','response','title'],
		site_reviews_form: ['email','name','terms','title'],
		site_reviews_summary: ['bars','if_empty','rating','stars','summary'],
	},

	/** @return void */
	init: function() {
		document.addEventListener( 'click', this.onClose.bind( this ));
		this.button.addEventListener( 'click', this.onToggle.bind( this ));
		this.menuItems.forEach( function( item ) {
			item.addEventListener( 'click', this.onTrigger.bind( this ));
		}.bind( this ));
	},

	/** @return void */
	initTinymceEditor: function() {
		tinymce.execCommand( 'GLSR_Shortcode' );
	},

	/** @return void */
	initQuicktagsEditor: function() {
		if( x( '#scTemp' ).length ) {
			this.initTinymceEditor();
			return;
		}
		x( 'body' ).append( '<textarea id="scTemp" style="display:none;"/>' );
		tinymce.init({
			elements: 'scTemp',
			mode: 'exact',
			plugins: ['glsr_shortcode', 'wplink'],
		});
		setTimeout( function() {
			this.initTinymceEditor();
		}, 200 );
	},

	/** @return void */
	close: function() {
		x( this.button ).removeClass( 'active' );
		x( this.el ).find( '.glsr-mce-menu' ).hide();
	},

	/** @return void */
	destroy: function() {
		var tmp = x( '#scTemp' );
		if( tmp.length ) {
			tinymce.get( 'scTemp' ).remove();
			tmp.remove();
		}
	},

	/** @return void */
	normalize: function( attributes ) {
		this.attributes = attributes;
		this.hiddenKeys = [];
		for( var key in attributes ) {
			if( !attributes.hasOwnProperty( key ))continue;
			this.normalizeCount( key );
			this.normalizeHide( key );
			this.normalizeId( key );
		}
		this.attributes.hide = this.hiddenKeys.join( ',' );
	},

	/** @return void */
	normalizeCount: function( key ) {
		if( key === 'count' && !x.isNumeric( this.attributes[key] )) {
			this.attributes[key] = '';
		}
	},

	/** @return void */
	normalizeHide: function( key ) {
		if( !this.hiddenFields.hasOwnProperty( this.current ))return;
		var value = key.substring('hide_'.length);
		if( this.hiddenFields[this.current].indexOf( value ) === -1 )return;
		if( this.attributes[key] ) {
			this.hiddenKeys.push( value );
		}
		delete this.attributes[key];
	},

	/** @return void */
	normalizeId: function( key ) {
		if( key === 'id' ) {
			this.attributes[key] = (+new Date()).toString(36);
		}
	},

	/** @return void */
	onClose: function( ev ) {
		if( x( ev.target ).closest( x( this.el )).length )return;
		this.close();
	},

	/** @return void */
	onToggle: function( ev ) {
		ev.preventDefault();
		this[ev.target.classList.contains( 'active' ) ? 'close' : 'open']();
	},

	/** @return void */
	onTrigger: function( ev ) {
		ev.preventDefault();
		this.current = ev.target.dataset.shortcode;
		if( !this.current )return;
		if( tinymce.get( window.wpActiveEditor )) {
			this.initTinymceEditor();
		}
		else {
			this.initQuicktagsEditor();
		}
		setTimeout( function() {
			this.close();
		}.bind( this ), 100 );
	},

	/** @return void */
	open: function() {
		x( this.button ).addClass( 'active' );
		x( this.el ).find( '.glsr-mce-menu' ).show();
	},

	/** @return void */
	request: function( request ) {
		var data = {
			action: site_reviews.action,
			request: request,
		};
		x.post( site_reviews.ajaxurl, data, this.response.bind( this ));
	},

	/** @return void */
	response: function( response ) {
		if( !response.body )return;
		if( response.body.length === 0 ) {
			window.send_to_editor( '[' + response.shortcode + ']' );
			this.destroy();
			return;
		}
		var popup = this.responsePopup( response );
		// Change the buttons if server-side validation failed
		if( response.ok.constructor === Array ) {
			popup.buttons[0].text = response.ok[0];
			popup.buttons[0].onclick = 'close';
			delete popup.buttons[1];
		}
		this.editor.windowManager.open( popup );
	},

	/** @return array */
	responseButtons: function( response ) {
		return [{
			text: response.ok,
			classes: 'btn glsr-btn primary',
			onclick: function() {
				var currentWindow = this.editor.windowManager.getWindows()[0];
				if( !this.validateAttributes( currentWindow ) )return;
				currentWindow.submit();
			}.bind( this ),
		},{
			text: response.close,
			onclick: 'close'
		}];
	},

	/** @return object */
	responsePopup: function( response ) {
		return {
			title: response.title,
			body: response.body,
			classes: 'glsr-mce-popup',
			minWidth: 320,
			buttons: this.responseButtons( response ),
			onsubmit: this.sendToEditor.bind( this, response ),
			onclose: this.destroy.bind( this ),
		};
	},

	/** @return void */
	sendToEditor: function( response, ev ) {
		var attributes = '';
		this.normalize( ev.data );
		for( var key in this.attributes ) {
			if( this.attributes.hasOwnProperty( key ) && this.attributes[key] !== '' ) {
				attributes += ' ' + key + '="' + this.attributes[key] + '"';
			}
		}
		window.send_to_editor( '[' + response.shortcode + attributes + ']' );
	},

	/** @return bool */
	validateAttributes: function( currentWindow ) {
		var field;
		var is_valid = true;
		var requiredAttributes = site_reviews.shortcodes[this.current];
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
