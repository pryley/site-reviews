/** global: GLSR, jQuery */

const aria = (el, prop, bool) => el.attr(`aria-${prop}`, bool ? 'true' : 'false');

const defaults = {
    classes: {
        active: 'is-active',
        selected: 'is-selected',
    },
    inView: 5, // items to display before scolling, this is dependant of the accompanying CSS
    onInit: null,
    onDestroy: null,
    onSelect: null,
    selectors: {
        results: '.glsr-filter__results',
        search: '.glsr-filter__search',
        selected: '.glsr-filter__selected',
        value: '.glsr-filter__value',
    },
}

export class Filter {
    constructor(selector, options) {
        this.el = jQuery(selector);
        this.options = jQuery.extend(true, defaults, options || {}); // deep extend
        this.resultsEl = this.el.find(this.options.selectors.results);
        this.selectedEl = this.el.find(this.options.selectors.selected);
        this.searchEl = this.el.find(this.options.selectors.search);
        this.valueEl = this.el.find(this.options.selectors.value);
        if (!this.el.length || !this.searchEl.length || !this.selectedEl.length || !this.valueEl.length || !this.resultsEl.length) return;
        this.action = this.el.data('action');
        this.events = {
            document: {
                mousedown: this.onDocumentClick.bind(this),
            },
            search: {
                blur: _.debounce(this.onSearchBlur.bind(this), 10),
                input: _.debounce(this.onSearchInput.bind(this), 200),
                keydown: this.onSearchKeydown.bind(this),
            },
            selected: {
                keydown: this.onSelectedKeydown.bind(this),
                mousedown: this.onSelectedClick.bind(this),
            },
        };
        this.init()
    }

    init() {
        this.eventHandler('on')
        this.data = [];
        if ('function' === typeof this.options.onInit) {
            this.options.onInit.call(this)
        }
    }

    destroy() {
        this.eventHandler('off')
        this.data = [];
        if ('function' === typeof this.options.onDestroy) {
            this.options.onDestroy.call(this)
        }
    }

    eventHandler(action) {
        this.eventListener(document, action, this.events.document)
        this.eventListener(this.searchEl, action, this.events.search)
        this.eventListener(this.selectedEl, action, this.events.selected)
    }

    eventListener(el, action, events) {
        _.each(events, (func, event) => jQuery(el)[action](event, func))
    }

    offsets() {
        let height = this.resultsEl.outerHeight(false);
        let selectedHeight = height / this.options.inView;
        let top = this.selected * selectedHeight; // top Y of selection
        let bottom = top + selectedHeight; // bottom Y of selection
        return { bottom, height, top }
    }

    onDocumentClick(ev) {
        if (jQuery(ev.target).find(this.el).length) {
            this.requestAbort()
            if (this.el.hasClass(this.options.classes.active)) {
                this.resultsHide()
                _.debounce(() => this.selectedEl.focus(), 10)()
            }
        }
    }

    onSearchBlur() {
        if (!this.el.find(document.activeElement).length) {
            this.resultsHide()
        }
    }

    onSearchInput() {
        this.requestAbort()
        if ('' === this.searchEl.val()) {
            this.resultsShow();
            return;
        }
        this.resultsEl.html(this.templateSearching());
        this.xhr = this.request().done(response => {
            this.data = response.items;
            this.resultsShow()
        })
    }

    onSearchKeydown(ev) {
        if (GLSR.keys.ENTER === ev.which) {
            ev.preventDefault()
            const selectedEl = this.resultsEl.find(`.${this.options.classes.selected}`);
            if (selectedEl) {
                selectedEl.trigger('mousedown')
            }
        } else if (GLSR.keys.ESC === ev.which) {
            this.resultsHide()
            _.debounce(() => this.selectedEl.focus(), 10)()
        } else if (GLSR.keys.DOWN === ev.which) {
            this.resultsMoveDown()
        } else if (GLSR.keys.UP === ev.which) {
            this.resultsMoveUp()
        } else if (GLSR.keys.TAB === ev.which) {
            this.resultsHide()
        }
    }

    onSelect(ev) {
        if ('function' === typeof this.options.onSelect) {
            this.options.onSelect.call(this, ev)
        }
        const selectedEl = jQuery(ev.currentTarget);
        this.selectedEl.attr('title', selectedEl.attr('title'));
        this.selectedEl.text(selectedEl.attr('title'));
        this.valueEl.val(selectedEl.data('id'))
        this.resultsHide()
        _.debounce(() => this.selectedEl.focus(), 10)()
    }

    onSelectedClick() {
        this.resultsShow()
    }

    onSelectedKeydown(ev) {
        if (~[GLSR.keys.DOWN, GLSR.keys.SPACE, GLSR.keys.UP].indexOf(ev.which)) {
            ev.preventDefault()
            this.resultsShow()
        } else if (GLSR.keys.ESC === ev.which) {
            this.selectedEl.blur()
        }
    }

    request() {
        const data = {};
        data[GLSR.nameprefix] = {
            _action: this.action,
            _nonce: GLSR.nonce[this.action],
            exclude: this.options.exclude,
            search: this.searchEl.val(),
        };
        return wp.ajax.post(GLSR.action, data).always(() => (delete this.xhr))
    }

    requestAbort() {
        if ('undefined' === typeof this.xhr) return;
        this.xhr.abort()
    }

    results() {
        let results = _.map(GLSR.filters[this.valueEl.attr('name')], (name, id) => ({ id, name }));
        let mergedResults = jQuery.merge(_.sortBy(results, 'name'), this.data);
        let id = this.valueEl.val();
        let name = this.selectedEl.text();
        if (~['','0'].indexOf(id)) {
            return mergedResults;
        }
        return jQuery.merge(mergedResults, [{ id, name }])
    }

    resultsHide() {
        this.requestAbort()
        this.el.removeClass(this.options.classes.active)
        this.searchEl.val('')
        this.selected = -1;
        aria(this.el, 'expanded', 0)
        aria(this.resultsEl, 'expanded', 0)
        aria(this.resultsEl, 'hidden', 1)
    }

    resultsMoveDown() {
        this.resultsNavigate(1) // run this first
        const offset = this.offsets();
        if (offset.bottom > offset.height) {
            this.resultsEl.scrollTop(offset.bottom - offset.height)
        }
    }

    resultsMoveUp() {
        this.resultsNavigate(-1) // run this first
        const offset = this.offsets();
        if (offset.bottom < offset.height) {
            this.resultsEl.scrollTop(offset.top)
        }
    }

    resultsNavigate(diff) {
        this.selected += diff;
        const children = this.resultsEl.children()
        children.attr('aria-selected', 'false').removeClass(this.options.classes.selected)
        if (this.selected < 0) { // reached the beginning
            this.selected = -1;
        }
        if (this.selected >= children.length) { // reached the end
            this.selected = children.length - 1;
        }
        if (this.selected >= 0) {
            const el = children.eq(this.selected);
            el.addClass(this.options.classes.selected)
            aria(el, 'selected', 1);
        }
    }

    resultsShow() {
        this.resultsEl.empty();
        _.each(this.results(), data => this.resultsEl.append(this.templateResult(data)))
        this.resultsEl.children().on('mousedown', this.onSelect.bind(this))
        this.selected = -1;
        this.el.addClass(this.options.classes.active)
        aria(this.el, 'expanded', 1)
        aria(this.resultsEl, 'expanded', 1)
        aria(this.resultsEl, 'hidden', 0)
        _.debounce(() => {
            this.resultsEl.scrollTop(0)
            this.searchEl.focus()
        }, 10)()
    }

    templateResult(data) {
        const template = _.template('<span aria-selected="false" data-id="<%= id %>" title="<%= name %>"><span><%= name %></span><% if (!~["","0",0].indexOf(id)) { %><span>ID:<%= id %></span><% } %></span>');
        return jQuery(template(data));
    }

    templateSearching() {
        const template = _.template('<span><span><%= text %></span><span class="spinner"></span></span>');
        return jQuery(template({ text: GLSR.text.searching }));
    }
};

export default Filter;
