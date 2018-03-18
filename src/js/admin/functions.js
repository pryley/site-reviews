/* globals ajaxurl, GLSR, jQuery, site_reviews */
/* jshint strict:false */

GLSR.colorControls = function()
{
	if( typeof jQuery.wp !== 'object' || typeof jQuery.wp.wpColorPicker !== 'function' )return;

	jQuery( document ).find( 'input[type="text"].color-picker-hex' ).each( function() {
		var t = jQuery( this );
		var options = t.data( 'colorpicker' ) || {};
		t.wpColorPicker( options );
	});
};

GLSR.dismissNotices = function()
{
	jQuery( '.notice.is-dismissible' ).each( function() {
		var notice = jQuery( this );
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

	if( !jQuery( '#glsr-notices' ).length ) {
		jQuery( '#message.notice' ).remove();
		jQuery( 'form#post' ).before( '<div id="glsr-notices" />' );
	}

	jQuery( '#glsr-notices' ).html( notices );

	jQuery( document ).trigger( 'wp-updates-notice-added' );
};

GLSR.isUndefined = function( value )
{
	return value === void(0);
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
		var el = jQuery( ev.target );

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

		jQuery( '#log-file' ).val( response.log );
	});
};

GLSR.onFieldChange = function()
{
	var depends = jQuery( this ).closest( 'form' ).find( '[data-depends]' );

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
			else if( jQuery.isArray( data.value ) ) {
				bool = jQuery.inArray( GLSR.normalizeValue( this.value ), GLSR.normalizeValues( data.value ) ) !== -1;
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
	jQuery( pointer.target ).pointer({
		content: pointer.options.content,
		position: pointer.options.position,
		close: function() {
			jQuery.post( ajaxurl, {
				pointer: pointer.id,
				action: 'dismiss-wp-pointer',
			});
		},
	})
	.pointer( 'open' )
	.pointer( 'sendToTop' );

	jQuery( document ).on( 'wp-window-resized', function() {
		jQuery( pointer.target ).pointer( 'reposition' );
	});
};

GLSR.postAjax = function( event, request, callback )
{
	event.preventDefault();

	var el = jQuery( event.target );

	if( el.is( ':disabled' ))return;

	var data = {
		action: site_reviews.action,
		request: request,
	};

	el.prop( 'disabled', true );

	jQuery.post( site_reviews.ajaxurl, data, function( response ) {

		if( typeof callback === 'function' ) {
			callback( response );
		}

		el.prop( 'disabled', false );

	}, 'json' );
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
	var row = jQuery( el ).closest( '.glsr-field' );

	if( !row.length )return;

	if( bool ) {
		row.removeClass( 'hidden' );
	}
	else {
		row.addClass( 'hidden' );
	}
};
