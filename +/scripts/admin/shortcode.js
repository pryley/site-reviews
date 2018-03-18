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
