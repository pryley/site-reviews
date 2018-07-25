/** global: CustomEvent, FormData, GLSR, HTMLFormElement, StarRating */
/* jshint -W014 */
/* jshint -W030 */
/* jshint -W093 */
;(function() {

	'use strict';

	function countGroupedElements( inputEl ) {
		var selector = 'input[name="' + inputEl.getAttribute( 'name' ) + '"]:checked';
		return inputEl.validation.form.querySelectorAll( selector ).length;
	}

	var validators = {
		email: {
			fn: function fn( val ) {
				return !val || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( val );
			},
		},
		max: {
			fn: function fn( val, limit ) {
				return !val || ( this.type === 'checkbox'
					? countGroupedElements( this ) <= parseInt( limit )
					: parseFloat( val ) <= parseFloat( limit )
				);
			},
		},
		maxlength: {
			fn: function fn( val, length ) {
				return !val || val.length <= parseInt( length );
			},
		},
		min: {
			fn: function fn( val, limit ) {
				return !val || ( this.type === 'checkbox'
					? countGroupedElements( this ) >= parseInt( limit )
					: parseFloat( val ) >= parseFloat( limit )
				);
			},
		},
		minlength: {
			fn: function fn( val, length ) {
				return !val || val.length >= parseInt( length );
			},
		},
		number: {
			fn: function fn( val ) {
				return !val || !isNaN( parseFloat( val ));
			},
			priority: 2,
		},
		pattern: {
			fn: function fn( val, pattern ) {
				var m = pattern.match( new RegExp( '^/(.*?)/([gimy]*)$' ));
				return !val || new RegExp( m[1], m[2] ).test( val );
			},
		},
		required: {
			fn: function fn( val ) {
				return this.type === 'radio' || this.type === 'checkbox'
					? countGroupedElements( this )
					: val !== undefined && val !== '';
			},
			priority: 99,
			halt: true,
		},
	};

	GLSR.Validation = function( formEl ) { // HTMLElement
		this.config = GLSR.validationconfig;
		this.form = formEl;
		this.form.setAttribute( 'novalidate', '' );
		this.strings = GLSR.validationstrings;
		this.init_();
	};

	GLSR.Validation.prototype = {

		ALLOWED_ATTRIBUTES_: ['required', 'max', 'maxlength', 'min', 'minlength', 'pattern'],
		SELECTOR_: 'input:not([type^=hidden]):not([type^=submit]), select, textarea',

		/** @return void */
		addEvent_: function( input ) {
			var eventName = ~['radio', 'checkbox'].indexOf( input.getAttribute( 'type' )) || input.nodeName === 'SELECT'
				? 'change'
				: 'input';
			input.addEventListener( eventName, function( ev ) {
				this.validate_( ev.target );
			}.bind( this ));
		},

		/** @return void */
		addValidators_: function( attributes, fns, params ) {
			[].forEach.call( attributes, function( attr ) {
				if( ~this.ALLOWED_ATTRIBUTES_.indexOf( attr.name )) {
					this.addValidatorToField_( fns, params, attr.name, attr.value );
				}
				else if( attr.name === 'type' ) {
					this.addValidatorToField_( fns, params, attr.value );
				}
			}.bind( this ));
		},

		/** @return void */
		addValidatorToField_: function( fns, params, name, value ) {
			if( !validators[name] )return;
			validators[name].name = name;
			fns.push( validators[name] );
			if( value ) {
				var valueParams = value.split( ',' );
				valueParams.unshift( null ); // placeholder for input value
				params[name] = valueParams;
			}
		},

		/** @return object */
		extend_: function() { // ...object
			var args = [].slice.call( arguments );
			var result = args[0];
			var extenders = args.slice(1);
			Object.keys( extenders ).forEach( function( i ) {
				for( var key in extenders[i] ) {
					if( !extenders[i].hasOwnProperty( key ))continue;
					result[key] = extenders[i][key];
				}
			});
			return result;
		},

		/** @return array */
		getErrorElements_: function( field ) {
			if( field.errorElements ) {
				return field.errorElements;
			}
			var errorTextParent = field.input.closest( '.' + this.config.field_class );
			var errorTextElement = null;
			if( errorTextParent ) {
				errorTextElement = errorTextParent.querySelector( '.' + this.config.error_tag_class );
				if( !errorTextElement ) {
					errorTextElement = document.createElement( this.config.error_tag );
					errorTextElement.className = this.config.error_tag_class;
					errorTextParent.appendChild( errorTextElement );
					errorTextElement.validation_display = errorTextElement.style.display;
				}
			}
			return field.errorElements = [
				errorTextParent,
				errorTextElement,
			];
		},

		/** @return void */
		init_: function() {
			this.fields = [].map.call( this.form.querySelectorAll( this.SELECTOR_ ), function( input ) {
				var params = {};
				var rules = [];
				this.addValidators_( input.attributes, rules, params );
				this.sortValidators_( rules );
				this.addEvent_( input );
				return input.validation = {
					form: this.form,
					input: input,
					params: params,
					validators: rules,
				};
			}.bind( this ));
		},

		/** @return void */
		removeError_: function( field ) {
			var errorElements = this.getErrorElements_( field );
			var errorTextParent = errorElements[0];
			var errorTextElement = errorElements[1];
			field.input.classList.remove( this.config.input_error_class );
			field.input.classList.remove( this.config.input_success_class );
			if( errorTextParent ) {
				errorTextParent.classList.remove( this.config.field_error_class );
			}
			if( errorTextElement ) {
				errorTextElement.innerHTML = '';
				errorTextElement.style.display = 'none';
			}
			return errorElements;
		},

		/** @return void */
		showError_: function( field ) {
			var errorElements = this.getErrorElements_( field );
			var errorTextParent = errorElements[0];
			var errorTextElement = errorElements[1];
			field.input.classList.remove( this.config.input_success_class );
			field.input.classList.add( this.config.input_error_class );
			if( errorTextParent ) {
				console.log( this.config );
				errorTextParent.classList.add( this.config.field_error_class );
			}
			if( errorTextElement ) {
				errorTextElement.innerHTML = field.errors.join( '<br>' );
				errorTextElement.style.display = errorTextElement.validation_display || '';
			}
		},

		/** @return void */
		showSuccess_: function( field ) {
			this.removeError_( field )[0];
			field.input.classList.add( this.config.input_success_class );
		},

		/** @return void */
		sortValidators_: function( fns ) {
			fns.sort( function( a, b ) {
				a.priority || (a.priority = 1);
				b.priority || (b.priority = 1);
				return b.priority - a.priority;
			});
		},

		/** @return bool */
		validate_: function( input ) {
			var isValid = true;
			var fields = this.fields;
			if( input instanceof HTMLElement ) {
				fields = [input.validation];
			}
			for( var i in fields ) {
				var field = fields[i];
				if( this.validateField_( field )) {
					this.showSuccess_( field );
				}
				else {
					isValid = false;
					this.showError_( field );
				}
			}
			return isValid;
		},

		/** @return bool */
		validateField_: function( field ) {
			var errors = [];
			var isValid = true;
			for( var i in field.validators ) {
				var validator = field.validators[i];
				var params = field.params[validator.name]
					? field.params[validator.name]
					: [];
				params[0] = field.input.value;
				if( !validator.fn.apply( field.input, params )) {
					isValid = false;
					var error = this.strings[validator.name];
					errors.push( error.replace( /(\%s)/g, params[1] ));
					if( validator.halt === true )break;
				}
			}
			field.errors = errors;
			return isValid;
		},
	};
})();
