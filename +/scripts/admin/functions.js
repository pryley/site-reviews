GLSR.colorControls = function()
{
	if( typeof x.wp !== 'object' || typeof x.wp.wpColorPicker !== 'function' )return;
	x( document ).find( 'input[type="text"].color-picker-hex' ).each( function() {
		x( this ).wpColorPicker( x( this ).data( 'colorpicker' ) || {} );
	});
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

GLSR.onClearLog = function( ev )
{
	var request = {
		action: 'clear-log',
	};
	GLSR.postAjax( ev, request, function( response ) {
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
	var textarea = el[0];
	textarea.style.height = 'auto';
	textarea.style.height = textarea.scrollHeight > minHeight ? textarea.scrollHeight + 'px' : minHeight + 'px';
};
