/** global: GLSR */
document.addEventListener( 'DOMContentLoaded', function() {
	// set text direction class
	var widgets = document.querySelectorAll( '.glsr-widget, .glsr-shortcode' );
	for( var i = 0; i < widgets.length; i++ ) {
		var direction = window.getComputedStyle( widgets[i], null ).getPropertyValue( 'direction' );
		widgets[i].classList.add( 'glsr-' + direction );
	}
	new GLSR.Forms( true );
	new GLSR.Pagination();
	new GLSR.Excerpts();
});
