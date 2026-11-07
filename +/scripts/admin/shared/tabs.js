/**
 * The tabs of a plugin page: the address, the history and the referer field follow the active tab,
 * a link to a tab of this page switches to it, and a row of tabs that wraps gets the is-wrapped class.
 *
 * A tab dispatches `site-reviews/tab` with { id, view } when it is shown.
 */
const SELECTORS = {
    link: '.glsr-card a, #glsr-notices a', // a link to a tab of this page switches the tab
    section: '.glsr-nav-view-section',
    subsubsub: '.glsr-subsubsub a',
    tab: 'a.glsr-nav-tab',
    view: '.glsr-nav-view',
};

const param = (href, name) => new URL(href, location.href).searchParams.get(name);

export default class Tabs {
    static init (root = document) {
        root.querySelectorAll('.glsr-nav-tab-wrapper').forEach(Tabs.watchWrapping)
        const active = root.querySelector('input[name=_active_tab]');
        const tabs = [...root.querySelectorAll(SELECTORS.tab)];
        const views = [...root.querySelectorAll(SELECTORS.view)];
        if (active && tabs.length && views.length) {
            new Tabs(root, { active, tabs, views })
        }
    }

    // the wrapped row styles its tabs as separate buttons
    static watchWrapping (nav) {
        let width = null;
        const update = () => {
            if (width === nav.clientWidth) return;
            width = nav.clientWidth;
            nav.classList.remove('is-wrapped') // measured as tabs: the buttons have margins of their own
            const tabs = [...nav.querySelectorAll('.glsr-nav-tab')];
            nav.classList.toggle('is-wrapped', tabs.some(tab => tab.offsetTop !== tabs[0].offsetTop))
        };
        new ResizeObserver(update).observe(nav)
        update()
    }

    constructor (root, { active, tabs, views }) {
        this.active = active;
        this.referers = [...root.querySelectorAll('input[name=_wp_http_referer]')];
        this.tabs = tabs;
        this.views = views;
        let current = param(location.href, 'tab');
        tabs.forEach((tab, index) => {
            const id = param(tab.href, 'tab');
            if (null === current && 0 === index) {
                current = id;
                this.remember(tab.getAttribute('href'), id, 'replaceState')
            }
            if (current === id) {
                this.show(id)
            }
            tab.addEventListener('click', this.onTab.bind(this))
        })
        root.querySelectorAll(SELECTORS.subsubsub).forEach(link => link.addEventListener('click', this.onTab.bind(this)))
        root.querySelectorAll(SELECTORS.link).forEach(link => link.addEventListener('click', this.onLink.bind(this)))
        window.addEventListener('popstate', this.onPopstate.bind(this))
    }

    onLink (ev) {
        const link = ev.currentTarget;
        const href = link.getAttribute('href');
        const tab = param(href, 'tab');
        if (!tab || param(href, 'page') !== param(location.href, 'page') || param(href, 'postType') !== param(location.href, 'postType')) return;
        ev.preventDefault()
        const current = param(location.href, 'tab');
        this.remember(href, tab)
        this.show(tab)
        if (tab !== current) {
            window.scrollTo(0, 0)
        }
    }

    onPopstate (ev) {
        const { href, tab } = ev.state || {};
        if (href && tab) {
            this.referers.forEach(input => (input.value = href))
            this.show(tab)
        }
    }

    onTab (ev) {
        const el = ev.currentTarget;
        const href = el.getAttribute('href');
        const tab = param(href, 'tab');
        if (!tab) return;
        ev.preventDefault()
        this.remember(href, tab)
        this.show(tab)
        el.blur()
    }

    remember (href, tab, method = 'pushState') {
        history[method]({ href, tab }, '', href)
        this.referers.forEach(input => (input.value = href))
    }

    show (id) {
        const index = this.views.findIndex(view => view.id === id);
        if (-1 === index) return;
        this.active.value = id;
        this.tabs.forEach((tab, i) => tab.classList.toggle('nav-tab-active', i === index))
        this.views.forEach((view, i) => view.classList.toggle('ui-tabs-hide', i !== index))
        this.showSection(this.views[index])
        this.tabs[index].dispatchEvent(new CustomEvent('site-reviews/tab', { bubbles: true, detail: { id, view: this.views[index] } }))
    }

    // the subsubsub links of a view pick one of its sections
    showSection (view) {
        const links = [...view.querySelectorAll(SELECTORS.subsubsub)];
        const sub = param(location.href, 'sub');
        let active = Math.max(0, links.findIndex(link => param(link.href, 'sub') === sub));
        links.forEach((link, index) => link.classList.toggle('current', index === active))
        view.querySelectorAll(SELECTORS.section).forEach((section, index) => section.classList.toggle('ui-tabs-hide', index !== active))
    }
}
