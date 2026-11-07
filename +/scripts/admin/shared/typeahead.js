import { debounce } from '@/public/core/index.js';
import Request from '@/public/modules/request/request.js';

/**
 * A search box that lists what a route answers as the person types, with the arrow keys moving the
 * highlight and Enter choosing. The screen's module renders the items and acts on the choice.
 */
export default class Typeahead {
    /**
     * @param {HTMLInputElement} input
     * @param {HTMLElement} list     where the rendered items go
     * @param {Object} options
     * @param {string} options.highlight       the class of the highlighted item (is-selected)
     * @param {number} options.minLength       the shortest term that is searched (1)
     * @param {Function} options.onResults     called with the items the route answered, or [] for a term too short
     * @param {Function} options.onSearching   called when a search starts
     * @param {Function} options.onSelect      called with the chosen item and its element
     * @param {Function} options.params        the other query parameters of the search
     * @param {Function} options.render        an item's element
     * @param {string} options.route           the path searched
     */
    constructor (input, list, options) {
        this.input = input;
        this.list = list;
        this.options = {
            highlight: 'is-selected',
            minLength: 1,
            onResults: () => {},
            onSearching: () => {},
            onSelect: () => {},
            params: () => ({}),
            render: Typeahead.render,
            ...options,
        };
        this.controller = null;
        this.index = -1;
        this.items = []; // what is rendered
        this.term = null;
        input.addEventListener('input', debounce(() => this.search(input.value), 200))
        input.addEventListener('keydown', this.onKeydown.bind(this))
        if (list !== input && list.hasAttribute('tabindex')) {
            list.addEventListener('keydown', this.onKeydown.bind(this))
        }
        list.addEventListener('mousedown', this.onMousedown.bind(this))
    }

    static render ({ id, title }) {
        const el = document.createElement('span');
        el.setAttribute('aria-selected', 'false');
        el.dataset.id = id;
        el.title = ['', '0'].includes(String(id)) ? title : `ID: ${id}`;
        const text = document.createElement('span');
        text.textContent = title;
        el.append(text);
        return el;
    }

    abort () {
        this.controller?.abort()
        this.controller = null;
    }

    clear () {
        this.abort()
        this.index = -1;
        this.items = [];
        this.list.replaceChildren()
    }

    get highlighted () {
        return this.list.children[this.index] || null;
    }

    highlight (diff) {
        const children = [...this.list.children];
        children.forEach(el => {
            el.classList.remove(this.options.highlight)
            el.setAttribute('aria-selected', 'false')
        })
        this.index = Math.min(children.length - 1, Math.max(-1, this.index + diff));
        const el = children[this.index];
        if (!el) {
            this.input.focus()
            return;
        }
        el.classList.add(this.options.highlight)
        el.setAttribute('aria-selected', 'true')
        this.scrollIntoView(el)
    }

    onKeydown (ev) {
        if ('ArrowDown' === ev.key) {
            ev.preventDefault()
            this.highlight(1)
        } else if ('ArrowUp' === ev.key) {
            ev.preventDefault()
            this.highlight(-1)
        } else if ('Enter' === ev.key || (' ' === ev.key && ev.target !== this.input)) {
            ev.preventDefault()
            if (this.highlighted) {
                this.select(this.highlighted)
            } else if ('Enter' === ev.key && this.input instanceof HTMLInputElement) { // a list-table filter has no search box
                this.search(this.input.value)
            }
        }
    }

    onMousedown (ev) {
        const el = ev.target.closest('[data-id]');
        if (el && this.list.contains(el) && this.list.children.length) {
            ev.preventDefault()
            this.select(el)
        }
    }

    scrollIntoView (el) {
        const child = el.getBoundingClientRect();
        const parent = this.list.getBoundingClientRect();
        if (child.top < parent.top) {
            this.list.scrollTop -= parent.top - child.top;
        } else if (child.bottom > parent.bottom) {
            this.list.scrollTop += child.bottom - parent.bottom;
        }
    }

    // only the latest search's answer is shown
    async search (term) {
        this.abort()
        this.term = term;
        if (term.length < this.options.minLength) {
            this.options.onResults([], term)
            return;
        }
        this.controller = new AbortController();
        const { signal } = this.controller;
        this.options.onSearching(term)
        const { data, success } = await Request.send({
            params: { search: term, ...this.options.params() },
            path: this.options.route,
            signal,
        });
        if (signal.aborted) return;
        this.controller = null;
        this.options.onResults(success && Array.isArray(data) ? data : [], term)
    }

    select (el) {
        const item = this.items[[...this.list.children].indexOf(el)];
        this.options.onSelect(item, el)
    }

    show (items) {
        this.index = -1;
        this.items = items;
        this.list.replaceChildren(...items.map(item => this.options.render(item)))
    }
}
