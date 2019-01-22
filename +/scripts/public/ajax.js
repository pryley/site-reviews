/** global: File, GLSR, XMLHttpRequest */
;(function() {

	'use strict';

	GLSR.Ajax = function() {};

	GLSR.Ajax.prototype = {
		/** @return void */
		get: function( url, successCallback, headers ) {
			this.xhr = new XMLHttpRequest();
			this.xhr.open( 'GET', url, true );
			this.xhr.onreadystatechange = function() {
				if( this.xhr.readyState !== 4 || this.xhr.status !== 200 )return;
				successCallback( this.xhr.responseText );
			}.bind( this );
			this.setHeaders_( headers );
			this.xhr.send();
		},

		/** @return bool */
		isFileSupported: function() {
			var input = document.createElement( 'INPUT' );
			input.type = 'file';
			return 'files' in input;
		},

		/** @return bool */
		isFormDataSupported: function() {
			return !!window.FormData;
		},

		/** @return bool */
		isUploadSupported: function() {
			var xhr = new XMLHttpRequest();
			return !!( xhr && ( 'upload' in xhr ) && ( 'onprogress' in xhr.upload ));
		},

		/** @return void */
		post: function( formOrData, successCallback, headers ) {
			this.xhr = new XMLHttpRequest();
			this.xhr.open( 'POST', GLSR.ajaxurl, true );
			this.setHeaders_( headers );
			this.xhr.send( this.normalizeData_( formOrData ));
			this.xhr.onreadystatechange = function() {
				if( this.xhr.readyState !== XMLHttpRequest.DONE )return;
				var result = JSON.parse( this.xhr.responseText );
				successCallback( result.data, result.success );
			}.bind( this );
		},

		/** @return FormData */
		buildFormData_: function( formData, data, parentKey ) {
			if( typeof data !== 'object' || data instanceof Date || data instanceof File ) {
				formData.append( parentKey, data || '' );
			}
			else {
				Object.keys( data ).forEach( function( key ) {
					if( !data.hasOwnProperty( key ))return;
					formData = this.buildFormData_( formData, data[key], parentKey ? parentKey[key] : key );
				}.bind( this ));
			}
			return formData;
		},

		/** @return FormData */
		normalizeData_: function( data ) { // object
			var formData = data;
			if( Object.prototype.toString.call( data ) === '[object HTMLFormElement]' ) {
				formData = new FormData( data );
			}
			if( Object.prototype.toString.call( formData ) !== '[object FormData]' ) {
				formData = new FormData();
			}
			formData.append( 'action', GLSR.action );
			formData.append( '_ajax_request', true );
			return formData;
		},

		/** @return void */
		setHeaders_: function( headers ) {
			headers = headers || {};
			headers['X-Requested-With'] = 'XMLHttpRequest';
			for( var key in headers ) {
				if( !headers.hasOwnProperty( key ))continue;
				this.xhr.setRequestHeader( key, headers[key] );
			}
		},
	};
})();
