/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Pointers = function() {
		$.each( GLSR.pointers, function( i, pointer ) {
			this.init_( pointer );
		}.bind( this ));
	};

	GLSR.Pointers.prototype = {
		/** @return void */
		close_: function( pointerId ) { // string
			$.post( GLSR.ajaxurl, {
				action: 'dismiss-wp-pointer',
				pointer: pointerId,
			});
		},

		/** @return void */
		init_: function( pointer ) { // object
			$( pointer.target ).pointer({
				content: pointer.options.content,
				position: pointer.options.position,
				close: this.close_.bind( null, pointer.id ),
			})
			.pointer( 'open' )
			.pointer( 'sendToTop' );
			$( document ).on( 'wp-window-resized', function() {
				$( pointer.target ).pointer( 'reposition' );
			});
		},
	};
})( jQuery );
