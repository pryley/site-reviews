/** global: GLSR, jQuery */

const Tabs = function (options) {
    this.options = jQuery.extend({}, this.defaults, options);
    this.active = document.querySelector('input[name=_active_tab]');
    this.referrerEl = document.querySelector('input[name=_wp_http_referer]');
    this.sections = document.querySelectorAll(this.options.viewSectionSelector);
    this.subsubsub = document.querySelectorAll(this.options.viewSubsubsub);
    this.tabs = document.querySelectorAll(this.options.tabSelector);
    this.views = document.querySelectorAll(this.options.viewSelector);
    if (!this.active || !this.referrerEl || !this.tabs || !this.views) return;
    this.init_();
};

Tabs.prototype = {
    defaults: {
        tabSelector: '.glsr-nav-tab',
        viewSelector: '.glsr-nav-view',
        viewSectionSelector: '.glsr-nav-view-section',
        viewSubsubsub: '.glsr-subsubsub a',
    },

    /** @return void */
    init_: function () {
        var self = this;
        jQuery(window).on('hashchange', self.onHashchange_.bind(self));
        jQuery(self.options.tabSelector).on('click', self.onClick_.bind(self));
        jQuery(self.options.viewSubsubsub).on('click', self.onClick_.bind(self));
        jQuery(self.options.tabSelector).each(function (index) {
            var active = location.hash 
                ? this.getAttribute('href').slice(1) === location.hash.slice(5).split('|')[0]
                : index === 0;
            if (active) {
                self.setTab_(this);
            }
        });
    },

    /** @return string */
    getAction_: function (bool) {
        return bool ? 'add' : 'remove';
    },

    /** @return void */
    onClick_: function (ev) {
        var el = ev.currentTarget;
        var href = el.getAttribute('href');
        if (href.startsWith('#')) {
            location.hash = this.prefixedHash_(href.slice(1)); // trigger hashchange
            el.blur();
            ev.preventDefault();
        }
    },

    /** @return void */
    onHashchange_: function () {
        var id = this.unprefixedHash_().split('|')[0];
        for(var i = 0; i < this.views.length; i++) {
            if (id !== this.views[i].id) continue;
            this.setTab_(this.tabs[i]);
            break;
        }
    },

    /** @return void */
    prefixedHash_: function (id) {
        return 'tab-' + id;
    },

    /** @return void */
    unprefixedHash_: function () {
        return location.hash.split('#tab-')[1]
    },

    /** @return void */
    setReferrer_: function (id) {
        var url = this.referrerEl.value.split('#')[0];
        var hash = this.prefixedHash_(id);
        this.referrerEl.value =  url + '#' + hash;
    },

    /** @return void */
    setTab_: function (el) {
        [].forEach.call(this.tabs, function (tab, index) {
            var action = this.getAction_(tab === el);
            if (action === 'add') {
                this.active.value = this.views[index].id;
                this.setReferrer_(this.active.value);
                this.setView_(index);
            }
            tab.classList[action]('nav-tab-active');
        }.bind(this));
    },

    /** @return void */
    setView_: function (idx) {
        [].forEach.call(this.views, function (view, index) {
            var action = this.getAction_(index !== idx);
            view.classList[action]('ui-tabs-hide');
            this.setViewSection_();
        }.bind(this));
    },

    /** @return void */
    setViewSection_: function () {
        let activeIndex = 0;
        [].forEach.call(this.subsubsub, (el, index) => {
            el.classList.remove('current');
            if (el.getAttribute('href').slice(1) === this.unprefixedHash_()) {
                activeIndex = index;
            }
        });
        if (this.subsubsub[activeIndex]) {
            this.subsubsub[activeIndex].classList.add('current');
        }
        [].forEach.call(this.sections, (el, index) => {
            var action = this.getAction_(index !== activeIndex);
            el.classList[action]('ui-tabs-hide');
        });
    },
};

export default Tabs;
