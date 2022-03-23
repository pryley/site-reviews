/** global: GLSR, jQuery */

const aria = (el, prop, bool) => el.attr(`aria-${prop}`, bool ? 'true' : 'false');

const defaults = {
    classes: {
        active: 'is-active',
        selected: 'is-selected',
    },
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

    defaults() {
        return _.sortBy(_.map(GLSR.filters[this.valueEl.attr('name')], (name, id) => ({ id, name })), 'name');
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
            this.resultsNavigate(1)
        } else if (GLSR.keys.UP === ev.which) {
            this.resultsNavigate(-1)
        } else if (GLSR.keys.TAB === ev.which) {
            ev.preventDefault()
        }
    }

    onSelect(ev) {
        if ('function' === typeof this.options.onSelect) {
            this.options.onSelect.call(this, ev)
        }
        const selectedEl = jQuery(ev.currentTarget);
        const value = selectedEl.data('id');
        this.selectedEl.attr('title', !~["","0",0].indexOf(value) ? 'ID: ' + value : selectedEl.data('name'));
        this.selectedEl.text(selectedEl.data('name'));
        this.valueEl.val(value)
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
        let results = jQuery.merge(this.defaults(), this.data);
        let id = this.valueEl.val();
        let name = this.selectedEl.text();
        if (-1 === _.findIndex(results, { id }) && -1 === _.findIndex(results, { name })) {
            return jQuery.merge(results, [{ id, name }])
        }
        return results;
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
            this.resultsScrollIntoView()
        }
    }

    resultsScrollIntoView() {
        const selectedEl = this.resultsEl.children().eq(this.selected);
        const child = selectedEl[0].getBoundingClientRect();
        const parent = this.resultsEl[0].getBoundingClientRect();
        const isAbove = child.top < parent.top;
        const isBelow = child.bottom > (parent.top + parent.height);
        const top = this.resultsEl.scrollTop();
        if (isAbove) {
            const amount = parent.top - child.top;
            this.resultsEl.scrollTop(top - amount);
            return;
        }
        if (isBelow) {
            const amount = child.bottom - (parent.top + parent.height);
            this.resultsEl.scrollTop(top + amount);
            return;
        }
    }

    resultsShow() {
        this.resultsEl.empty();
        this.selected = -1;
        _.each(this.results(), data => this.resultsEl.append(this.templateResult(data)))
        this.resultsEl.children().on('mousedown', this.onSelect.bind(this))
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
        const template = _.template('<span aria-selected="false" data-id="<%= id %>" data-name="<%= name %>" title="<% if (!~["","0",0].indexOf(id)) { %>ID: <%= id %><% } else { %><%= name %><% } %>"><span><%= name %></span></span>');
        return jQuery(template(data));
    }

    templateSearching() {
        const template = _.template('<span data-searching><span><%= text %></span><span class="spinner"></span></span>');
        return jQuery(template({ text: GLSR.text.searching }));
    }
};

export default Filter;
