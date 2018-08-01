/** global: GLSR */
;(function() {

	'use strict';

	GLSR.Forms = function( selector ) {
		this.el = document.querySelector( selector );
		if( !this.el )return;
		this.depends = this.el.querySelectorAll( '[data-depends]' );
		if( !this.depends.length )return;
		this.init_();
	};

	GLSR.Forms.prototype = {
		/** @return void */
		init_: function() {
			var formControls = this.el.elements;
			for( var i = 0; i < formControls.length; i++ ) {
				if( ['INPUT', 'SELECT'].indexOf( formControls[i].nodeName ) === -1 )continue;
				formControls[i].addEventListener( 'change', this.onChange_.bind( this ));
			}
		},

		/** @return bool */
		isSelected_: function( el, dependency ) {
			if( Array.isArray( dependency.value )) {
				if( 'checkbox' === el.type ) {
					var isSelected = false;
					[].map.call( el.closest( 'form' ).querySelectorAll( 'input[name="' +  el.name + '"]:checked' ), function( input ) {
						if( !~dependency.value.indexOf( input.value ))return;
						isSelected = true;
					});
					return isSelected;
				}
				return this.normalizeValues_( dependency.value ).indexOf( this.normalizeValue_( el.value )) !== -1;
			}
			else if( 'checkbox' === el.type ) {
				return !!el.checked;
			}
			return this.normalizeValue_( dependency.value ) === this.normalizeValue_( el.value );
		},

		/** @return bool|string */
		normalizeValue_: function( value ) {
			if(['true','on','yes','1'].indexOf( value ) !== -1 ) {
				return true;
			}
			if(['false','off','no','0'].indexOf( value ) !== -1 ) {
				return false;
			}
			return value;
		},

		/** @return array */
		normalizeValues_: function( values ) {
			return values.map( this.normalizeValue_ );
		},

		/** @return void */
		onChange_: function( ev ) {
			this.depends.forEach( function( el ) {
				var data = el.getAttribute( 'data-depends' );
				var dependency;
				if( !data )return;
				try {
					dependency = JSON.parse( data );
				}
				catch( error ) {
					console.log( data );
					return console.error( error );
				}
				if( dependency.name !== ev.currentTarget.name )return;
				this.toggleHiddenField_( el, this.isSelected_( ev.currentTarget, dependency ));
			}.bind( this ));
		},

		/** @return void */
		toggleHiddenField_: function( el, bool ) {
			var row = el.closest( '.glsr-field' );
			if( !row )return;
			row.classList[bool ? 'remove' : 'add']( 'hidden' );
		},
	};
})();
