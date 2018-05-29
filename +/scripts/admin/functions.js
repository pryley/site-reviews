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
