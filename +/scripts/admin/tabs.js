/** global: GLSR, jQuery */

const Tabs = function (options) {
    this.options = jQuery.extend({}, this.defaults, options);
    this.active = document.querySelector('input[name=_active_tab]');
    this.refererInputs = jQuery('input[name=_wp_http_referer]');
    this.subsubsub = document.querySelectorAll(this.options.viewSubsubsub);
    this.tabs = document.querySelectorAll(this.options.tabSelector);
    this.views = document.querySelectorAll(this.options.viewSelector);
    if (!this.active || !this.refererInputs.length || !this.tabs || !this.views) return;
    this.init_();
};

Tabs.prototype = {
    defaults: {
        smartLinks: '.glsr-card a, #glsr-notices a',
        tabSelector: '.glsr-nav-tab',
        viewSelector: '.glsr-nav-view',
        viewSectionSelector: '.glsr-nav-view-section',
        viewSubsubsub: '.glsr-subsubsub a',
    },

    init_: function () {
        jQuery(this.options.tabSelector).each((index, el) => {
            const href = el.getAttribute('href')
            const tab = this.queryHref_(el, 'tab')
            let currentTab = this.queryLocation_('tab')
            if (null === currentTab && 0 === index) {
                currentTab = tab;
                history.replaceState({ href, tab }, '', href)
                this.refererInputs.val(href);
            }
            if (currentTab === tab) {
                this.setActiveTab_(tab)
            }
        });
        jQuery(window).on('popstate', this.onPopstate_.bind(this));
        jQuery(this.options.smartLinks).on('click', this.onClickLink_.bind(this));
        jQuery(this.options.tabSelector + ',' + this.options.viewSubsubsub).on('click', this.onClick_.bind(this));
    },

    onClick_: function (ev) {
        const el = ev.currentTarget;
        const href = el.getAttribute('href')
        const tab = this.queryHref_(el, 'tab')
        if (tab) {
            history.pushState({ href, tab }, '', href)
            this.refererInputs.val(href);
            this.setActiveTab_(tab)
            ev.preventDefault();
            el.blur();
        }
    },

    onClickLink_: function (ev) {
        const el = ev.currentTarget;
        const currentPage = this.queryLocation_('page')
        const currentPostType = this.queryLocation_('postType')
        const currentTab = this.queryLocation_('tab')
        const page = this.queryHref_(el, 'page')
        const postType = this.queryHref_(el, 'postType')
        const tab = this.queryHref_(el, 'tab')
        if (page === currentPage && postType === currentPostType && tab) {
            const href = el.getAttribute('href')
            history.pushState({ href, tab }, '', href)
            this.refererInputs.val(href);
            this.setActiveTab_(tab)
            if (tab !== currentTab) {
                // scroll to the top of the page if visiting a different tab from a link
                window.scrollTo(0, 0);
            }
            ev.preventDefault();
        }
    },

    onPopstate_: function (ev) {
        const state = ev.originalEvent.state;
        if (state && state.tab && state.href) {
            this.refererInputs.val(state.href);
            this.setActiveTab_(state.tab)
        }
    },

    getAction_: function (bool) {
        return bool ? 'add' : 'remove';
    },

    queryHref_: function (el, param) {
        let url = new URL(el.getAttribute('href'));
        return url.searchParams.get(param)
    },

    queryLocation_: function (param) {
        let url = new URL(location.href);
        return url.searchParams.get(param)
    },

    setActiveTab_: function (id) {
        for (var i = 0; i < this.views.length; i++) {
            if (id !== this.views[i].id) continue;
            this.setTab_(this.tabs[i]);
            break;
        }
    },

    setSubsubsub_: function () {
        let activeIndex = 0;
        let view;
        [].forEach.call(this.subsubsub, (el, index) => {
            el.classList.remove('current');
            const currentSub = this.queryHref_(el, 'sub')
            const sub = this.queryLocation_('sub')
            if (currentSub === sub) {
                activeIndex = index;
            }
        });
        if (this.subsubsub[activeIndex]) {
            this.subsubsub[activeIndex].classList.add('current');
            view = this.subsubsub[activeIndex].closest(this.options.viewSelector)
        }
        if (!view) {
            view = document;
        }
        const sections = view.querySelectorAll(this.options.viewSectionSelector);
        [].forEach.call(sections, (el, index) => {
            var action = this.getAction_(index !== activeIndex);
            el.classList[action]('ui-tabs-hide');
        });
    },

    setTab_: function (el) {
        [].forEach.call(this.tabs, (tab, index) => {
            var action = this.getAction_(tab === el);
            if (action === 'add') {
                this.active.value = this.views[index].id;
                this.setView_(index);
            }
            tab.classList[action]('nav-tab-active');
        });
    },

    setView_: function (tabIndex) {
        [].forEach.call(this.views, (view, viewIndex) => {
            let action = this.getAction_(viewIndex !== tabIndex);
            view.classList[action]('ui-tabs-hide');
            this.setSubsubsub_();
        });
    },
};

export default Tabs;
