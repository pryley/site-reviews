/** global: GLSR, XMLHttpRequest */
;(function() {

	'use strict';

	GLSR.Ajax = function() {
		/** @return void */
		this.get = function( url, successCallback ) {
			var xhr = new XMLHttpRequest();
			xhr.open( 'GET', url );
			xhr.onreadystatechange = function() {
				if( xhr.readyState !== 4 )return;
				successCallback( xhr.responseText );
			};
			xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
			xhr.send();
		};

		/** @return bool */
		this.isFileAPISupported = function() {
			var input = document.createElement( 'INPUT' );
			input.type = 'file';
			return 'files' in input;
		};

		/** @return bool */
		this.isFormDataSupported = function() {
			return !!window.FormData;
		};

		/** @return bool */
		this.isUploadSupported = function() {
			var xhr = new XMLHttpRequest();
			return !!( xhr && ( 'upload' in xhr ) && ( 'onprogress' in xhr.upload ));
		};

		/** @return void */
		this.post = function( data, successCallback, headers ) {
			var xhr = new XMLHttpRequest();
			this.setHeaders_( xhr, headers );
			xhr.open( 'POST', GLSR.ajaxurl );
			xhr.onreadystatechange = function() {
				if( xhr.readyState !== 4 )return;
				successCallback( JSON.parse( xhr.responseText ));
			};
			xhr.send({
				action: GLSR.action,
				request: data,
			});
		};

		/** @return void */
		this.setHeaders_ = function( xhr, headers ) {
			headers = headers || {};
			headers['X-Requested-With'] = 'XMLHttpRequest';
			for( var key in headers ) {
				if( !headers.hasOwnProperty( key ))continue;
				xhr.setRequestHeader( key, headers[key] );
			}
		};
	};
})();
