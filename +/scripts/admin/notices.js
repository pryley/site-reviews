/** global: GLSR, jQuery */
;(function ($) {

	'use strict';

	GLSR.Notices = function () { // string
		this.init_();
	};

	GLSR.Notices.prototype = {
		/** @return void */
		add: function (notices) { // string
			if (!notices) return;
			if (!$('#glsr-notices').length) {
				$('#message.notice').remove();
				$('form#post').before('<div id="glsr-notices" />');
			}
			$('#glsr-notices').html(notices);
			$(document).trigger('wp-updates-notice-added');
		},

		/** @return void */
		init_: function () {
			$('.glsr-notice[data-dismiss]').on('click.wp-dismiss-notice', this.onClick_.bind(this));
		},

		/** @return void */
		onClick_: function (ev) {
			var data = {};
			data[GLSR.nameprefix] = {
				_action: 'dismiss-notice',
				notice: $(ev.currentTarget).data('dismiss'),
			};
			wp.ajax.post(GLSR.action, data);
		},
	};
})(jQuery);
