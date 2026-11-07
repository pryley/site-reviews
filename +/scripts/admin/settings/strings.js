/** global: jQuery */

import { config, fadeOut } from '@/public/core/index.js';
import fill from '@/admin/shared/template.js';
import Typeahead from '@/admin/shared/typeahead.js';

/**
 * The Strings tab: a search of the translatable strings adds a row to the table from the matching
 * <template>, a row's delete link removes it, and the rows are sortable.
 */
export default class Strings {
    static init (root = document) {
        const box = root.querySelector('#glsr-search-translations');
        if (box) {
            new Strings(box)
        }
    }

    constructor (box) {
        this.box = box;
        this.input = box.querySelector('.glsr-search-input');
        this.results = box.querySelector('.glsr-search-results');
        this.table = box.parentElement.querySelector('.glsr-strings-table tbody');
        if (!this.input || !this.results || !this.table) return;
        this.excluded = []; // the ids of the rows
        this.input.setAttribute('aria-describedby', 'live-search-desc')
        this.typeahead = new Typeahead(this.input, this.results, {
            highlight: 'is-highlighted',
            onResults: this.onResults.bind(this),
            onSearching: () => this.box.classList.add('is-active'),
            onSelect: this.onSelect.bind(this),
            params: () => ({ exclude: this.excluded }),
            render: this.render.bind(this),
            route: 'search/strings',
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
        this.table.addEventListener('click', this.onDelete.bind(this))
        this.reindex()
        this.makeSortable()
    }

    add (entry) {
        const template = document.getElementById(`tmpl-glsr-string-${entry.p1 ? 'plural' : 'single'}`);
        if (!template) return;
        this.table.append(fill(template, {
            ...entry,
            index: this.table.children.length,
            prefix: this.results.dataset.prefix,
        }))
        this.excluded.push(entry.id)
        this.setVisibility()
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

    // jQuery UI sortable used by WordPress
    makeSortable () {
        if ('function' !== typeof jQuery?.fn?.sortable) return;
        const $table = jQuery(this.table);
        $table.sortable({
            items: 'tr',
            tolerance: 'pointer',
            start: (ev, ui) => {
                ui.placeholder.height(ui.helper[0].scrollHeight)
            },
            sort: (ev, ui) => {
                const top = ev.pageY - $table.offsetParent().offset().top - (ui.helper.outerHeight(true) / 2);
                ui.helper.css({ top: `${top}px` })
            },
        })
    }

    async onDelete (ev) {
        const link = ev.target.closest('a.delete');
        if (!link) return;
        ev.preventDefault()
        const row = link.closest('tr');
        row.querySelectorAll('td').forEach(td => (td.style.backgroundColor = '#faafaa'))
        await fadeOut(row, 350)
        row.remove()
        this.reindex()
        this.setVisibility()
    }

    onResults (items) {
        this.box.classList.remove('is-active')
        this.typeahead.show(items)
        if (!items.length) {
            this.results.replaceChildren(this.empty())
        }
        this.results.classList.add('is-active')
        document.body.classList.add('glsr-focus')
    }

    onSelect (item) {
        if (item) {
            this.add(item)
        }
        this.reset()
    }

    // the rows are numbered in order and ids left out of the next search
    reindex () {
        this.excluded = [];
        [...this.table.children].forEach((row, index) => {
            row.querySelectorAll('.glsr-string-td2 input, .glsr-string-td2 textarea').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                if (input.hasAttribute('data-id')) {
                    this.excluded.push(input.value)
                }
            })
        })
    }

    render (item) {
        const el = document.createElement('span');
        el.tabIndex = 0;
        el.dataset.id = item.id;
        el.dataset.domain = item.domain;
        const text = document.createElement('span');
        text.textContent = item.text;
        el.append(text);
        return el;
    }

    reset () {
        this.clear()
        this.input.value = '';
    }

    setVisibility () {
        this.table.parentElement.classList.toggle('glsr-hidden', 0 === this.table.children.length)
    }
}
