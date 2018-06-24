/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	GLSR.Pointers = function() {
		x.each( GLSR.pointers, function( i, pointer ) {
			this.init_( pointer );
		}.bind( this ));
	};

	GLSR.Pointers.prototype = {
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
})( jQuery );
