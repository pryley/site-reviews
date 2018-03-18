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
