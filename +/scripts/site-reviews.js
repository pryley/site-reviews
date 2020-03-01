/** global: GLSR */
document.addEventListener( 'DOMContentLoaded', function() {
	// set text direction class
	var widgets = document.querySelectorAll( '.glsr' );
	for( var i = 0; i < widgets.length; i++ ) {
		var direction = window.getComputedStyle( widgets[i], null ).getPropertyValue( 'direction' );
		widgets[i].classList.add( 'glsr-' + direction );
	}
	// Check for unsupported browser versions (<=IE9)
	if( !( document.all && !window.atob )) {
		new GLSR.Forms( true );
		new GLSR.Pagination();
		new GLSR.Excerpts();
	}
});
