/** global: GLSR, jQuery, StarRating, wp */

GLSR.keys = {
	ALT: 18,
	DOWN: 40,
	ENTER: 13,
	ESC: 27,
	SPACE: 32,
	UP: 38,
};

jQuery( function( $ ) {

	GLSR.notices = new GLSR.Notices();
	GLSR.shortcode = new GLSR.Shortcode( '.glsr-mce' );
	GLSR.stars = new StarRating('.glsr-metabox-field select.glsr-star-rating', {
		showText: false,
	});
	GLSR.ColorPicker();
	new GLSR.Forms();
	new GLSR.Pinned();
	new GLSR.Pointers();
	new GLSR.Search('#glsr-search-posts', {
		action: 'search-posts',
		onInit: function () {
			this.el.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
		},
		onResultClick: function (ev) {
			var result = $(ev.currentTarget);
			var template = wp.template('glsr-assigned-posts');
			var entry = {
				id: result.data('id'),
				name: 'post_ids[]',
				url: result.data('url'),
				title: result.text(),
			};
			if (template) {
				var entryEl = $(template(entry));
				entryEl.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
				this.el.find('.glsr-selected-entries').append(entryEl);
				this.reset_();
			}
			this.options.searchEl.focus();
		},
	});
	new GLSR.Search('#glsr-search-users', {
		action: 'search-users',
		onInit: function () {
			this.el.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
		},
		onResultClick: function (ev) {
			var result = $(ev.currentTarget);
			var template = wp.template('glsr-assigned-users');
			var entry = {
				id: result.data('id'),
				name: 'user_ids[]',
				url: result.data('url'),
				title: result.text(),
			};
			if (template) {
				var entryEl = $(template(entry));
				entryEl.find('.glsr-remove-button').on('click', this.onUnassign_.bind(this));
				this.el.find('.glsr-selected-entries').append(entryEl);
				this.reset_();
			}
			this.options.searchEl.focus();
		},
	});
	new GLSR.Search( '#glsr-search-translations', {
		action: 'search-translations',
		onInit: function() {
			this.makeSortable_();
		},
		onResultClick: function( ev ) {
			var result = $( ev.currentTarget );
			var entry = result.data( 'entry' );
			var template = wp.template( 'glsr-string-' + ( entry.p1 ? 'plural' : 'single' ));
			entry.index = this.options.entriesEl.children().length;
			entry.prefix = this.options.resultsEl.data( 'prefix' );
			if( template ) {
				this.options.entriesEl.append( template( entry ));
				this.options.exclude.push({ id: entry.id });
				this.options.results = this.options.results.filter( function( i, el ) {
					return el !== result.get(0);
				});
			}
			this.setVisibility_();
		},
	});
	new GLSR.Status( 'a.glsr-toggle-status' );
	new GLSR.Tabs();
	new GLSR.TextareaResize();
	new GLSR.Tools();
	new GLSR.Sync();

	$('.glsr-metabox-field .glsr-toggle__input').change(function () {
		var isChecked = this.checked;
		$('.glsr-input-value').each(function(i, el) {
			if (isChecked) {
				$(el).data('value', el.value);
			} else {
				el.value = $(el).data('value');
				if ('url' !== el.type) return;
				switchImage($(el).parent().find('img'), el.value);
			}
		});
		$('.glsr-input-value').prop('disabled', !isChecked);
		GLSR.stars.rebuild();
	});

	$('.glsr-metabox-field input[type=url]').change(function () {
		switchImage($(this).parent().find('img'), this.value);
	});

	var switchImage = function (imgEl, imgSrc) {
		if (!imgEl) return;
		var image = new Image();
		image.src = imgSrc;
		image.onerror = function () {
			imgEl.attr('src', imgEl.data('fallback'));
		};
		image.onload = function () {
			imgEl.attr('src', image.src);
		};
	};

	$('a#revert').on('click', function () {
		$(this).parent().find('.spinner').addClass('is-active');
	});

	$( '.glsr-card.postbox' ).addClass( 'closed' )
		.find( '.handlediv' ).attr( 'aria-expanded', false )
		.closest( '.glsr-nav-view' ).addClass( 'collapsed' );

	$( '.glsr-card.postbox .glsr-card-header' ).on( 'click', function() {
		var parent = $( this ).parent();
		var view = parent.closest( '.glsr-nav-view' );
		var action = parent.hasClass( 'closed' ) ? 'remove' : 'add';
		parent[action + 'Class']( 'closed' ).find( '.handlediv' ).attr( 'aria-expanded', action !== 'add' );
		action = view.find( '.glsr-card.postbox' ).not( '.closed' ).length > 0 ? 'remove' : 'add';
		view[action + 'Class']( 'collapsed' );
	});

	if( $('.glsr-support-step').not(':checked').length < 1 ) {
		$( '.glsr-card-result' ).removeClass( 'hidden' );
	}

	$('.glsr-support-step').on( 'change', function() {
		var action = $('.glsr-support-step').not(':checked').length > 0 ? 'add' : 'remove';
		$( '.glsr-card-result' )[action + 'Class']( 'hidden' );
	});

	var trackValue = function () {
		this.dataset.glsrTrack = this.value;
	};

	$('select[data-glsr-track]').each(trackValue);
	$('select[data-glsr-track]').on('change', trackValue);
});
