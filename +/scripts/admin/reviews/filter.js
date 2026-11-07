import { config } from '@/public/core/index.js';
import Typeahead from '@/admin/shared/typeahead.js';

const aria = (el, name, value) => el.setAttribute(`aria-${name}`, String(value));

/**
 * The list table's dropdown (views/partials/listtable/filter.php): a combobox of fixed choices
 * and, with a data-search route, of what a search finds.
 */
export default class Filter {
    static init (root = document) {
        root.querySelectorAll('.glsr-filter').forEach(el => new Filter(el))
    }

    constructor (el) {
        this.el = el;
        this.results = el.querySelector('.glsr-filter__results');
        this.search = el.querySelector('.glsr-filter__search');
        this.selected = el.querySelector('.glsr-filter__selected');
        this.value = el.querySelector('.glsr-filter__value');
        if (!this.results || !this.selected || !this.value) return;
        this.choices = this.parseChoices();
        this.found = []; // what the search found
        this.keys = this.search || this.results; // the element that takes the keys: the search box, or the list itself
        if (!this.search) {
            this.results.tabIndex = -1;
        }
        this.typeahead = new Typeahead(this.search || this.results, this.results, {
            onResults: this.onResults.bind(this),
            onSearching: () => this.results.replaceChildren(this.searching()),
            onSelect: this.onSelect.bind(this),
            route: el.dataset.search || '',
        });
        document.addEventListener('mousedown', this.onDocumentMousedown.bind(this))
        this.keys.addEventListener('blur', () => window.setTimeout(this.onBlur.bind(this), 10))
        this.keys.addEventListener('keydown', this.onKeydown.bind(this))
        this.selected.addEventListener('keydown', this.onSelectedKeydown.bind(this))
        this.selected.addEventListener('mousedown', () => this.show())
    }

    hide () {
        this.typeahead.abort()
        this.el.classList.remove('is-active')
        if (this.search) {
            this.search.value = '';
        }
        aria(this.el, 'expanded', false)
        aria(this.results, 'expanded', false)
        aria(this.results, 'hidden', true)
    }

    // the choices, what was found, and the current value when neither has it
    items () {
        const items = [...this.choices, ...this.found].map(({ id, title }) => ({ id: String(id), title }));
        const id = String(this.value.value);
        const title = this.selected.textContent;
        if (!items.some(item => id === item.id || title === item.title)) {
            items.push({ id, title })
        }
        return items;
    }

    onBlur () {
        if (!this.el.contains(document.activeElement)) {
            this.hide()
        }
    }

    onDocumentMousedown (ev) {
        if (!this.el.contains(ev.target) && this.el.classList.contains('is-active')) {
            this.hide()
            window.setTimeout(() => this.selected.focus(), 10)
        }
    }

    onKeydown (ev) {
        if ('Escape' === ev.key) {
            this.hide()
            window.setTimeout(() => this.selected.focus(), 10)
        } else if ('Tab' === ev.key) {
            ev.preventDefault()
        }
    }

    onResults (found, term) {
        this.found = found;
        if ('' === term) {
            this.found = [];
        }
        this.show(false)
    }

    onSelect (item, el) {
        this.selected.title = el.title;
        this.selected.textContent = item.title;
        this.value.value = item.id;
        this.hide()
        window.setTimeout(() => this.selected.focus(), 10)
    }

    onSelectedKeydown (ev) {
        if ([' ', 'ArrowDown', 'ArrowUp'].includes(ev.key)) {
            ev.preventDefault()
            this.show()
        } else if ('Escape' === ev.key) {
            this.selected.blur()
        }
    }

    parseChoices () {
        try {
            return JSON.parse(this.el.dataset.options || '[]');
        } catch (error) {
            return [];
        }
    }

    searching () {
        const el = document.createElement('span');
        el.dataset.searching = '';
        const text = document.createElement('span');
        text.textContent = config.text.searching;
        const spinner = document.createElement('span');
        spinner.className = 'spinner';
        el.append(text, spinner);
        return el;
    }

    show (focus = true) {
        this.typeahead.show(this.items())
        this.el.classList.add('is-active')
        aria(this.el, 'expanded', true)
        aria(this.results, 'expanded', true)
        aria(this.results, 'hidden', false)
        if (focus) {
            window.setTimeout(() => {
                this.results.scrollTop = 0;
                this.keys.focus()
            }, 10)
        }
    }
}
