/** global: GLSR, jQuery, _, wp */

const Search = function (selector, options) {
    this.el = jQuery(selector);
    this.options = options;
    this.searchTerm = null;
    this.init_();
};

Search.prototype = {
    defaults: {
        action: null,
        exclude: [],
        onInit: null,
        onResultClick: null,
        results: {},
        selected: -1,
        selectedClass: 'glsr-selected-result',
        selectorEntries: '.glsr-strings-table tbody',
        selectorResults: '.glsr-search-results',
        selectorSearch: '.glsr-search-input',
        // entriesEl
        // resultsEl
        // searchEl
    },

    /** @return void */
    init_: function () {
        this.options = jQuery.extend({}, this.defaults, this.options);
        if (!this.el.length) return;
        this.options.entriesEl = this.el.parent().find(this.options.selectorEntries);
        this.options.resultsEl = this.el.find(this.options.selectorResults);
        this.options.searchEl = this.el.find(this.options.selectorSearch);
        this.options.searchEl.attr('aria-describedby', 'live-search-desc');
        if (typeof this.options.onInit === 'function') {
            this.options.onInit.call(this);
        }
        this.initEvents_();
    },

    /** @return void */
    initEvents_: function () {
        this.options.searchEl.on('input', _.debounce(this.onSearchInput_.bind(this), 500));
        this.options.searchEl.on('keyup', this.onSearchKeyup_.bind(this));
        this.options.searchEl.on('keydown keypress', function (ev) {
            if (GLSR.keys.ENTER !== ev.which) return;
            ev.preventDefault();
        });
        jQuery(document).on('click', this.onDocumentClick_.bind(this));
        jQuery(document).on('keydown', this.onDocumentKeydown_.bind(this));
    },

    /** @return void */
    abort_: function () {
        if ('undefined' === typeof this.searchRequest) return;
        this.searchRequest.abort();
    },

    /** @return void */
    clearResults_: function () {
        this.abort_();
        this.options.resultsEl.empty();
        this.options.resultsEl.removeClass('is-active');
        this.el.removeClass('is-active');
        jQuery('body').removeClass('glsr-focus');
    },

    /** @return void */// Manage entries
    deleteEntry_: function (index) {
        var row = this.options.entriesEl.children('tr').eq(index);
        var search = this;
        row.find('td').css({ backgroundColor:'#faafaa' });
        row.fadeOut(350, function () {
            jQuery(this).remove();
            search.options.results = {};
            search.reindexRows_();
            search.setVisibility_();
        });
    },

    /** @return void */
    displayResults_: function (items) {
        jQuery('body').addClass('glsr-focus');
        this.options.resultsEl.append(items);
        this.options.resultsEl.children('span').on('click', this.onResultClick_.bind(this));
    },

    /** @return void */// Manage entries
    makeSortable_: function () {
        this.options.entriesEl.on('click', 'a.delete', this.onEntryDelete_.bind(this));
        this.options.entriesEl.sortable({
            items: 'tr',
            tolerance: 'pointer',
            start: function (ev, ui) {
                ui.placeholder.height(ui.helper[0].scrollHeight);
            },
            sort: function (ev, ui) {
                var top = ev.pageY - jQuery(this).offsetParent().offset().top - (ui.helper.outerHeight(true) / 2);
                ui.helper.css({
                    top: top + 'px',
                });
            },
        });
    },

    /** @return void */
    navigateResults_: function (diff) {
        this.options.selected += diff;
        this.options.results.removeClass(this.options.selectedClass);
        if (this.options.selected < 0) {
            // reached the start (should now allow keydown scroll)
            this.options.selected = -1;
            this.options.searchEl.focus();
        }
        if (this.options.selected >= this.options.results.length) {
            // reached the end (should now allow keydown scroll)
            this.options.selected = this.options.results.length - 1;
        }
        if (this.options.selected >= 0) {
            this.options.results.eq(this.options.selected)
                .addClass(this.options.selectedClass)
                .focus();
        }
    },

    /** @return void */
    onDocumentClick_: function (ev) {
        if (jQuery(ev.target).find(this.el).length && jQuery('body').hasClass('glsr-focus')) {
            this.clearResults_();
        }
    },

    /** @return void */
    onDocumentKeydown_: function (ev) {
        if (jQuery.isEmptyObject(this.options.results)) return;
        if (GLSR.keys.ESC === ev.which) {
            this.clearResults_();
        }
        if (GLSR.keys.ENTER === ev.which || GLSR.keys.SPACE === ev.which) {
            var selected = this.options.resultsEl.find('.' + this.options.selectedClass);
            if (selected) {
                selected.trigger('click');
            }
        }
        if (GLSR.keys.UP === ev.which) {
            ev.preventDefault();
            this.navigateResults_(-1);
        }
        if (GLSR.keys.DOWN === ev.which) {
            ev.preventDefault();
            this.navigateResults_(1);
        }
    },

    /** @return void */// Manage entries
    onEntryDelete_: function (ev) {
        ev.preventDefault();
        this.deleteEntry_(jQuery(ev.currentTarget).closest('tr').index());
    },

    /** @return void */
    onResultClick_: function (ev) {
        ev.preventDefault();
        if (typeof this.options.onResultClick === 'function') {
            this.options.onResultClick.call(this, ev);
        }
        this.clearResults_();
    },

    /** @return void */
    onSearchInput_: function (ev) {
        this.abort_();
        if (this.searchTerm === ev.currentTarget.value && this.options.results.length) {
            return this.displayResults_(this.options.results);
        }
        this.options.resultsEl.empty();
        this.options.selected = -1;
        this.searchTerm = ev.currentTarget.value;
        if (this.searchTerm === '') {
            return this.reset_();
        }
        this.el.addClass('is-active');
        var data = {};
        data[GLSR.nameprefix] = {
            _action: this.options.action,
            _nonce: this.el.find('#_search_nonce').val(),
            exclude: this.options.exclude,
            search: this.searchTerm,
        };
        this.searchRequest = wp.ajax.post(GLSR.action, data).done(function (response) {
            this.el.removeClass('is-active');
            this.displayResults_(response.items ? response.items : response.empty);
            this.options.results = this.options.resultsEl.children();
            this.options.resultsEl.addClass('is-active');
            delete this.searchRequest;
        }.bind(this));
    },

    /** @return void */
    onSearchKeyup_: function (ev) {
        if (GLSR.keys.ESC === ev.which) {
            this.reset_();
        }
        if (GLSR.keys.ENTER === ev.which) {
            this.onSearchInput_(ev);
            ev.preventDefault();
        }
    },

    /** @return void */// Manage entries
    onUnassign_: function (ev) {
        ev.preventDefault();
        var assignedEl = jQuery(ev.currentTarget).closest('.glsr-assigned-entry');
        assignedEl.find('a').css({color:'#c00'});
        assignedEl.fadeOut('fast', function () {
            assignedEl.remove();
        });
    },

    /** @return void */// Manage entries
    reindexRows_: function () {
        var search = this;
        this.options.exclude = [];
        this.options.entriesEl.children('tr').each(function (index) {
            jQuery(this).find('.glsr-string-td2').children().filter(':input').each(function () {
                var input = jQuery(this);
                var name = input.attr('name').replace(/\[\d+\]/i, '[' + index + ']');
                input.attr('name', name);
                if (input.is('[data-id]')) {
                    search.options.exclude.push({ id: input.val() });
                }
            });
        });
    },

    /** @return void */
    reset_: function () {
        this.clearResults_();
        this.options.results = {};
        this.options.searchEl.val('');
    },

    /** @return void */// Manage entries
    setVisibility_: function () {
        var action = this.options.entriesEl.children().length > 0 ? 'remove' : 'add';
        this.options.entriesEl.parent()[action + 'Class']('glsr-hidden');
    },
};

export default Search;
