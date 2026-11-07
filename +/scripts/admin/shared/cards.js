/**
 * The cards of a page: a heading toggles its card, an active tab toggles every card of its view,
 * and a [data-expand] link opens the card it names, on this page or on the page it links to.
 */
const STORAGE_KEY = 'glsr-expand';

const cardsOf = (view) => [...view.querySelectorAll('.glsr-card.postbox')];

const isClosed = (card) => card.classList.contains('closed');

const setOpen = (card, open) => {
    card.classList.toggle('closed', !open)
    card.querySelectorAll('.glsr-accordion-trigger').forEach(trigger => trigger.setAttribute('aria-expanded', String(open)))
}

const updateView = (view) => {
    if (view) {
        view.classList.toggle('collapsed', !cardsOf(view).some(card => !isClosed(card)))
    }
}

export default class Cards {
    static init (root = document) {
        const cards = new Cards(root);
        document.addEventListener('click', cards.onExpand.bind(cards))
        cards.collapseClosed()
        cards.open(localStorage.getItem(STORAGE_KEY))
    }

    constructor (root) {
        this.root = root;
        root.querySelectorAll('.glsr-card.postbox .glsr-card-heading').forEach(heading => heading.addEventListener('click', this.onHeading.bind(this)))
        root.querySelectorAll('.glsr-nav-tab').forEach(tab => tab.addEventListener('click', this.onTab.bind(this)))
    }

    collapseClosed () {
        this.root.querySelectorAll('.glsr-card.postbox:not(.open)').forEach(card => {
            setOpen(card, false)
            card.closest('.glsr-nav-view')?.classList.add('collapsed')
        })
    }

    onExpand (ev) {
        const link = ev.target.closest('[data-expand]');
        if (!link) return;
        localStorage.setItem(STORAGE_KEY, link.dataset.expand)
        this.open(link.dataset.expand)
    }

    onHeading (ev) {
        const card = ev.currentTarget.parentElement;
        setOpen(card, isClosed(card))
        updateView(card.closest('.glsr-nav-view'))
    }

    onTab (ev) {
        const tab = ev.currentTarget;
        if (!tab.classList.contains('nav-tab-active')) return;
        const view = document.getElementById(tab.dataset.id);
        if (!view) return;
        const open = view.classList.contains('collapsed');
        cardsOf(view).forEach(card => setOpen(card, open))
        view.classList.toggle('collapsed', !open)
    }

    open (selector) {
        let el = null;
        try {
            el = selector ? this.root.querySelector(selector) : null;
        } catch (ignored) {
            // not a selector
        }
        if (!el) return;
        const view = el.closest('.glsr-nav-view');
        if (view) {
            cardsOf(view).forEach(card => setOpen(card, false))
            view.classList.add('collapsed')
        }
        window.setTimeout(() => {
            const card = el.closest('.glsr-card');
            if (!card) return;
            setOpen(card, true)
            updateView(view)
            localStorage.removeItem(STORAGE_KEY)
            if (document.querySelector('#glsr-notices .notice')) return;
            card.scrollIntoView({
                behavior: 'smooth',
                block: card.offsetHeight >= window.innerHeight ? 'start' : 'center',
            })
        }, 10)
    }
}
