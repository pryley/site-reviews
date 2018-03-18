/* globals GLSR, jQuery, site_reviews */
/* jshint strict:false */

GLSR.pinned.events = function()
{
	var pinnedSelect = jQuery( '#pinned-status-select' );

	jQuery( 'a.cancel-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		pinnedSelect.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		pinnedSelect.find( 'select' ).val( jQuery( '#hidden-pinned-status' ).val() === '0' ? 1 : 0 );
	});

	jQuery( 'a.edit-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		if( pinnedSelect.is( ':hidden' ) ) {
			pinnedSelect.slideDown( 'fast', function() {
				pinnedSelect.find( 'select' ).focus();
			});
			jQuery( this ).hide();
		}
	});

	jQuery( 'a.save-pinned-status' ).on( 'click', function( e ) {
		e.preventDefault();
		pinnedSelect.slideUp( 'fast' ).siblings( 'a.edit-pinned-status' ).show().focus();
		GLSR.pinned.save( jQuery( this ) );
	});

	jQuery( 'table' ).on( 'click', 'td.sticky i', GLSR.pinned.onToggle );
};

GLSR.pinned.onToggle = function()
{
	var el = jQuery( this );

	var data = {
		action: site_reviews.action,
		request: {
			action: 'toggle-pinned',
			id: el[0].getAttribute( 'data-id' ),
		},
	};

	jQuery.post( site_reviews.ajaxurl, data, function( response ) {
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
			id:     jQuery( '#post_ID' ).val(),
			pinned: jQuery( '#pinned-status' ).val(),
		},
	};

	jQuery.post( site_reviews.ajaxurl, data, function( response ) {
		jQuery( '#pinned-status' ).val( !response.pinned|0 );
		jQuery( '#hidden-pinned-status' ).val( response.pinned|0 );
		jQuery( '#pinned-status-text' ).text( response.pinned ? el.data( 'yes' ) : el.data( 'no' ) );

		GLSR.insertNotices( response.notices );
	});
};
