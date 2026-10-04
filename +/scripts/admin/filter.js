/** global: GLSR, jQuery */

import config from '@/public/config.js';
import Request from '@/public/request.js';

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

/**
 * The list table dropdown (views/partials/listtable/filter.php).
 */
export class Filter {
    constructor(selector, options) {
        this.el = jQuery(selector);
        this.options = jQuery.extend(true, {}, defaults, options || {});
        this.resultsEl = this.el.find(this.options.selectors.results);
        this.selectedEl = this.el.find(this.options.selectors.selected);
        this.searchEl = this.el.find(this.options.selectors.search);
        this.valueEl = this.el.find(this.options.selectors.value);
        if (!this.el.length || !this.selectedEl.length || !this.valueEl.length || !this.resultsEl.length) return;
        this.choices = this.parseChoices();
        this.route = this.el.attr('data-search') || '';
        this.search = 0; // counts the searches: only the answer to the latest one is shown
        this.keysEl = this.searchEl.length ? this.searchEl : this.resultsEl.attr('tabindex', -1);
        this.events = {
            document: {
                mousedown: this.onDocumentClick.bind(this),
            },
            keys: {
                blur: _.debounce(this.onBlur.bind(this), 10),
                keydown: this.onKeydown.bind(this),
            },
            search: {
                input: _.debounce(this.onSearchInput.bind(this), 200),
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
        this.eventListener(this.keysEl, action, this.events.keys)
        this.eventListener(this.searchEl, action, this.events.search)
        this.eventListener(this.selectedEl, action, this.events.selected)
    }

    eventListener(el, action, events) {
        _.each(events, (func, event) => jQuery(el)[action](event, func))
    }

    onDocumentClick(ev) {
        if (jQuery(ev.target).find(this.el).length) {
            this.search++
            if (this.el.hasClass(this.options.classes.active)) {
                this.resultsHide()
                _.debounce(() => this.selectedEl.focus(), 10)()
            }
        }
    }

    onBlur() {
        if (!this.el.find(document.activeElement).length) {
            this.resultsHide()
        }
    }

    onSearchInput() {
        const search = ++this.search;
        if ('' === this.searchEl.val()) {
            this.resultsShow();
            return;
        }
        this.resultsEl.html(this.templateSearching());
        Request.send({ params: { search: this.searchEl.val() }, path: this.route }).then(({ data, success }) => {
            if (search !== this.search) return;
            this.data = success && Array.isArray(data) ? data : [];
            this.resultsShow()
        })
    }

    onKeydown(ev) {
        if ('Enter' === ev.key) {
            ev.preventDefault()
            const selectedEl = this.resultsEl.find(`.${this.options.classes.selected}`);
            if (selectedEl) {
                selectedEl.trigger('mousedown')
            }
        } else if ('Escape' === ev.key) {
            this.resultsHide()
            _.debounce(() => this.selectedEl.focus(), 10)()
        } else if ('ArrowDown' === ev.key) {
            ev.preventDefault()
            this.resultsNavigate(1)
        } else if ('ArrowUp' === ev.key) {
            ev.preventDefault()
            this.resultsNavigate(-1)
        } else if ('Tab' === ev.key) {
            ev.preventDefault()
        }
    }

    onSelect(ev) {
        if ('function' === typeof this.options.onSelect) {
            this.options.onSelect.call(this, ev)
        }
        const chosenEl = jQuery(ev.currentTarget);
        this.selectedEl.attr('title', chosenEl.attr('title')).text(chosenEl.text());
        this.valueEl.val(chosenEl.attr('data-id'))
        this.resultsHide()
        _.debounce(() => this.selectedEl.focus(), 10)()
    }

    onSelectedClick() {
        this.resultsShow()
    }

    onSelectedKeydown(ev) {
        if (['ArrowDown', ' ', 'ArrowUp'].includes(ev.key)) {
            ev.preventDefault()
            this.resultsShow()
        } else if ('Escape' === ev.key) {
            this.selectedEl.blur()
        }
    }

    parseChoices() {
        try {
            return JSON.parse(this.el.attr('data-options') || '[]');
        } catch (error) {
            return [];
        }
    }

    results() {
        const results = [...this.choices, ...this.data].map(({ id, title }) => ({ id: String(id), title }));
        const id = String(this.valueEl.val());
        const title = this.selectedEl.text();
        if (!results.some(result => id === result.id || title === result.title)) {
            results.push({ id, title })
        }
        return results;
    }

    resultsHide() {
        this.search++
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
            this.keysEl.focus()
        }, 10)()
    }

    templateResult({ id, title }) {
        return jQuery('<span aria-selected="false"/>')
            .attr({ 'data-id': id, title: ['', '0'].includes(id) ? title : `ID: ${id}` })
            .append(jQuery('<span/>').text(title));
    }

    templateSearching() {
        return jQuery('<span data-searching/>')
            .append(jQuery('<span/>').text(config.text.searching))
            .append('<span class="spinner"/>');
    }
};

export default Filter;
