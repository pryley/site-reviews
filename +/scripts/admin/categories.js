/** global: GLSR, jQuery */
;(function( $ ) {

	'use strict';

	GLSR.Categories = function() {
		this.taxonomy = 'site-review-category';
		this.selector = '#taxonomy-' + this.taxonomy + ' input:checkbox';
		if( $('#bulk-edit').length ) {
			this.selector = '#bulk-edit input:checkbox[id^=in-' + this.taxonomy + '-]';
		}
		this.categories = $(this.selector);
		this.checklist = $('#' + this.taxonomy + 'checklist');
		this.init_();
	};

	GLSR.Categories.prototype = {
		/** @return void */
		addAfter_: function(el, response) {
			this.categories = $(this.selector);
			var id = response.parsed.responses[0].id;
			this.handleEvents_('off');
			this.manageCategories_(null, id);
			this.handleEvents_('on');
		},

		handleEvents_: function(action) {
			if( this.categories.length ) {
				this.categories[action]( 'change', this.manageCategories_.bind( this ));
			}
		},

		/** @return void */
		init_: function() {
			this.handleEvents_('on');
			if( this.checklist.length ) {
				this.checklist.wpList({ addAfter: this.addAfter_.bind( this ) });
			}
		},

		/** @return void */
		manageCategories_: function( ev, value ) {
			value = value || ev.target.value;
			$(this.selector).each( function() {
				this.checked = this.value === value && ev.target.checked;
			});
		},
	};
})( jQuery );
