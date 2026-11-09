import { config, fadeOut } from '@/public/core/index.js';
import Typeahead from '@/admin/shared/typeahead.js';

// the search box => the route it searches and the name of the ids it saves
const BOXES = {
    'glsr-search-posts': { name: 'post_ids[]', route: 'search/posts' },
    'glsr-search-users': { name: 'user_ids[]', route: 'search/users' },
};

/**
 * The Assigned Posts and Assigned Users boxes of the review editor and of quick edit: a search
 * adds an entry from the box's template, and an entry's remove button takes it away.
 */
export default class Assigned {
    static init (root = document) {
        Object.entries(BOXES).forEach(([id, options]) => {
            const box = root.querySelector(`#${id}`);
            if (box) {
                new Assigned(box, options)
            }
        })
    }

    constructor (box, { name, route }) {
        this.box = box;
        this.entries = box.querySelector('.glsr-selected-entries');
        this.input = box.querySelector('.glsr-search-input');
        this.name = name;
        this.results = box.querySelector('.glsr-search-results');
        this.template = box.querySelector('template');
        if (!this.entries || !this.input || !this.results || !this.template) return;
        this.input.setAttribute('aria-describedby', 'live-search-desc')
        this.typeahead = new Typeahead(this.input, this.results, {
            highlight: 'is-highlighted',
            onResults: this.onResults.bind(this),
            onSearching: () => this.box.classList.add('is-active'),
            onSelect: this.onSelect.bind(this),
            route,
        });
        this.input.addEventListener('keydown', ev => {
            if ('Escape' === ev.key) this.reset()
        })
        this.results.addEventListener('keydown', this.typeahead.onKeydown.bind(this.typeahead))
        document.addEventListener('mousedown', ev => {
            if (!box.contains(ev.target)) this.clear()
        })
        document.addEventListener('keydown', ev => {
            if ('Escape' === ev.key && box.contains(document.activeElement)) this.clear()
        })
        box.addEventListener('click', this.onRemove.bind(this))
    }

    add (item) {
        const entry = this.template.content.firstElementChild.cloneNode(true);
        const input = entry.querySelector('input');
        const link = entry.querySelector('a');
        input.name = this.name;
        input.value = item.id;
        link.href = item.url;
        link.textContent = item.title;
        this.entries.append(entry)
    }

    clear () {
        this.typeahead.clear()
        this.results.classList.remove('is-active')
        this.box.classList.remove('is-active')
        document.body.classList.remove('glsr-focus')
    }

    empty () {
        const el = document.createElement('div');
        el.textContent = config.text.nothingFound;
        return el;
    }

    async onRemove (ev) {
        const button = ev.target.closest('.glsr-remove-button');
        if (!button) return;
        ev.preventDefault()
        const entry = button.closest('.glsr-multibox-entry');
        await fadeOut(entry, 150)
        entry.remove()
    }

    onResults (items, term) {
        if ('' === term) {
            this.clear()
            return;
        }
        this.box.classList.remove('is-active')
        this.typeahead.show(items)
        if (!items.length) {
            this.results.replaceChildren(this.empty())
        }
        this.results.classList.add('is-active')
        this.results.querySelectorAll('[data-id]').forEach(el => (el.tabIndex = 0))
        document.body.classList.add('glsr-focus')
    }

    onSelect (item, el) {
        if (item) {
            this.add({ ...item, title: el.textContent })
        }
        this.reset()
        this.input.focus()
    }

    reset () {
        this.clear()
        this.input.value = '';
    }
}
