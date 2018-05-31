GLSR.forms = function( selector ) {
	this.el = document.querySelector( selector );
	if( !this.el )return;
	this.depends = this.el.querySelectorAll( '[data-depends]' );
	if( !this.depends.length )return;
	this.init();
};

GLSR.forms.prototype = {

	/** @return void */
	init: function() {
		var formControls = this.el.elements;
		for( var i = 0; i < formControls.length; i++ ) {
			if( ['INPUT', 'SELECT'].indexOf( formControls[i].nodeName ) === -1 )continue;
			formControls[i].addEventListener( 'change', this.onChange.bind( this ));
		}
	},

	/** @return bool */
	isSelected: function( el, dependency ) {
		if( 'checkbox' === el.type ) {
			return !!el.checked;
		}
		else if( Array.isArray( dependency.value )) {
			return this.normalizeValues( dependency.value ).indexOf( this.normalizeValue( el.value )) !== -1;
		}
		return this.normalizeValue( dependency.value ) === this.normalizeValue( el.value );
	},

	/** @return bool|string */
	normalizeValue: function( value ) {
		if(['true','on','yes','1'].indexOf( value ) !== -1 ) {
			return true;
		}
		if(['false','off','no','0'].indexOf( value ) !== -1 ) {
			return false;
		}
		return value;
	},

	/** @return array */
	normalizeValues: function( values ) {
		return values.map( this.normalizeValue );
	},

	/** @return void */
	onChange: function( ev ) {
		this.depends.forEach( function( el ) {
			var data = el.getAttribute( 'data-depends' );
			if( !data )return;
			var dependency;
			try {
				dependency = JSON.parse( data );
			}
			catch( error ) {
				console.log( data );
				return console.error( error );
			}
			if( dependency.name !== ev.target.name.replace( '[]', '' ))return;
			this.toggleHiddenField( el, this.isSelected( ev.target, dependency ));
		}.bind( this ));
	},

	/** @return void */
	toggleHiddenField: function( el, bool ) {
		var row = el.closest( '.glsr-field' );
		if( !row )return;
		row.classList[bool ? 'remove' : 'add']( 'hidden' );
	},
};
