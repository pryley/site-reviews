var GLSR = {};

GLSR.convertValue = function( value ) {
	if( GLSR.isNumeric( value )) {
		return parseFloat( value );
	}
	else if( value === 'true') {
		return true;
	}
	else if( value === 'false' ) {
		return false;
	}
	else if( value === '' || value === null ) {
		return undefined;
	}
	return value;
};

GLSR.getAjax = function( url, success ) {
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( 'Microsoft.XMLHTTP' );
	xhr.open( 'GET', url );
	xhr.onreadystatechange = function() {
		if( xhr.readyState > 3 && xhr.status === 200 ) {
			success( xhr.responseText );
		}
	};
	xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
	xhr.send();
	return xhr;
};

GLSR.isNumeric = function( value ) {
	return !( isNaN( parseFloat( value )) || !isFinite( value ));
};

GLSR.isString = function( str ) {
	return Object.prototype.toString.call( str ) === '[object String]';
};

GLSR.on = function( type, el, handler ) {
	if( GLSR.isString( el )) {
		el = document.querySelectorAll( el );
	}
	[].forEach.call( el, function( node ) {
		node.addEventListener( type, handler );
	});
};

GLSR.off = function( type, el, handler ) {
	if( GLSR.isString( el )) {
		el = document.querySelectorAll( el );
	}
	[].forEach.call( el, function( node ) {
		node.removeEventListener( type, handler );
	});
};

/**
 * Adapted from https://github.com/bitovi/jquerypp/blob/master/dom/form_params/form_params.js
 */
GLSR.parseFormData = function( form, convert ) {
	convert = !!convert || false;
	var keyBreaker = /[^\[\]]+/g; // used to parse bracket notation
	var data = {};
	var seen = {}; // used to uniquely track seen values
	var nestData = function( field, data, parts, seenName )
	{
		var name = parts.shift();
		// Keep track of the dot separated fullname
		seenName = seenName ? seenName + '.' + name : name;
		if( parts.length ) {
			if( !data[ name ] ) {
				data[ name ] = {};
			}
			// Recursive call
			nestData( field, data[ name ], parts, seenName );
		}
		else {
			// Convert the value
			var value = convert ? GLSR.convertValue( field.value ) : field.value;
			// Handle same name case, as well as "last checkbox checked" case
			if( seenName in seen && field.type !== 'radio' && !data[ name ].isArray()) {
				if( name in data ) {
					data[ name ] = [ data[name] ];
				}
				else {
					data[ name ] = [];
				}
			}
			else {
				seen[ seenName ] = true;
			}
			// Finally, assign data
			if( ['radio','checkbox'].indexOf( field.type ) !== -1 && !field.checked )return;

			if( !data[ name ] ) {
				data[ name ] = value;
			}
			else {
				data[ name ].push( value );
			}
		}
	};

	for( var i = 0; i < form.length; i++ ) {
		var field = form[i];
		if( !field.name || field.disabled || ['file','reset','submit','button'].indexOf( field.type ) !== -1 )continue;
		var parts = field.name.match( keyBreaker );
		if( !parts.length ) {
			parts = [ field.name ];
		}
		nestData( field, data, parts );
	}
	return data;
};

GLSR.postAjax = function( url, data, success ) {
	var params = typeof data !== 'string' ? GLSR.serialize( data ) : data;
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( 'Microsoft.XMLHTTP' );
	xhr.open( 'POST', url ); // asynchronously
	xhr.onreadystatechange = function() {
		if( xhr.readyState > 3 && xhr.status === 200 ) {
			success( JSON.parse( xhr.responseText ));
		}
	};
	xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
	xhr.send( params );
	return xhr;
};

GLSR.ready = function( fn ) {
	if( typeof fn !== 'function' )return;
	// in case the document is already rendered
	if( document.readyState !== 'loading' ) {
		fn();
	}
	// modern browsers
	else if( document.addEventListener ) {
		document.addEventListener( 'DOMContentLoaded', fn );
	}
	// IE <= 8
	else {
		document.attachEvent( 'onreadystatechange', function() {
			if( document.readyState === 'complete' ) {
				fn();
			}
		});
	}
};

GLSR.serialize = function( obj, prefix ) {
	var str = [];

	for( var property in obj ) {
		if( !obj.hasOwnProperty( property ))continue;
		var key = prefix ? prefix + '[' + property + ']' : property;
		var value = obj[ property ];
		str.push( typeof value === 'object' ?
			GLSR.serialize( value, key ) :
			encodeURIComponent( key ) + '=' + encodeURIComponent( value )
		);
	}
	return str.join( '&' );
};

GLSR.insertAfter = function( el, tag, attributes ) {
	var newEl = GLSR.createEl( tag, attributes );
	el.parentNode.insertBefore( newEl, el.nextSibling );
	return newEl;
};

GLSR.appendTo = function( el, tag, attributes ) {
	var newEl = GLSR.createEl( tag, attributes );
	el.appendChild( newEl );
	return newEl;
};

GLSR.createEl = function( tag, attributes ) {
	var el = ( typeof tag === 'string' ) ? document.createElement( tag ) : tag;
	attributes = attributes || {};
	for( var key in attributes ) {
		if( !attributes.hasOwnProperty( key ) )continue;
		el.setAttribute( key, attributes[ key ] );
	}
	return el;
};
