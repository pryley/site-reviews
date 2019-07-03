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
		getDependsData_: function( el ) {
			var data = el.getAttribute( 'data-depends' );
			if( !data )return;
			try {
				return JSON.parse( data );
			}
			catch( error ) {
				console.log( data );
				return console.error( error );
			}
		},

		/** @return void */
		init_: function() {
			var formControls = this.el.elements;
			for( var i = 0; i < formControls.length; i++ ) {
				if( !~['INPUT', 'SELECT'].indexOf( formControls[i].nodeName ))continue;
				formControls[i].addEventListener( 'change', this.onChange_.bind( this ));
			}
		},

		/** @return bool */
		isFieldSelected_: function( name, values ) {
			var isSelected = false;
			values = [].concat( values ); // cast to array
			var els = this.el.querySelectorAll( '[name="' + name + '"]' );
			[].map.call( els, function( el ) {
				var hasValue = ~this.normalizeValues_( values ).indexOf( this.normalizeValue_( el.value ));
				if( ~['checkbox', 'radio'].indexOf( el.type )) {
					if( !!el.checked && hasValue ) {
						isSelected = true;
					}
				}
				else if( hasValue ) {
					isSelected = true;
				}
			}.bind( this ));
			return isSelected;
		},

		/** @return bool|string */
		normalizeValue_: function( value ) {
			if( ~['true','on','yes','1'].indexOf( value )) {
				return true;
			}
			if( ~['false','off','no','0'].indexOf( value )) {
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
				var dependencies = this.getDependsData_( el );
				if( !dependencies )return;
				var names = dependencies.map(function( dependency ) {
					return dependency.name;
				});
				if( !~names.indexOf( ev.currentTarget.name ))return;
				var isFieldSelected = true;
				dependencies.forEach( function( dependency ) {
					// check dependency.name has dependency.value
					if( !this.isFieldSelected_( dependency.name, dependency.value )) {
						isFieldSelected = false;
					}
				}.bind( this ));
				this.toggleHiddenField_( el, isFieldSelected );
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
