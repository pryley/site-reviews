/** global: GLSR, jQuery */
;(function ($) {
    'use strict';

    GLSR.Tabs = function (options) {
        this.options = $.extend({}, this.defaults, options);
        this.active = document.querySelector('input[name=_active_tab]');
        this.referrer = document.querySelector('input[name=_wp_http_referer]');
        this.tabs = document.querySelectorAll(this.options.tabSelector);
        this.views = document.querySelectorAll(this.options.viewSelector);
        if (!this.active || !this.referrer || !this.tabs || !this.views) return;
        this.init_();
    };

    GLSR.Tabs.prototype = {
        defaults: {
            tabSelector: '.glsr-nav-tab',
            viewSelector: '.glsr-nav-view',
        },

        /** @return void */
        init_: function () {
            var self = this;
            $(window).on('hashchange', self.onHashchange_.bind(self));
            [].forEach.call(self.tabs, function (tab, index) {
                var active = location.hash ? tab.getAttribute('href').slice(1) === location.hash.slice(5) : index === 0;
                if (active) {
                    self.setTab_(tab);
                }
                tab.addEventListener('click', self.onClick_.bind(self));
                tab.addEventListener('touchend', self.onClick_.bind(self));
            }.bind(self));
            $(self.options.viewSelector).on('click', 'a', function () {
                var elId = $(this).data('expand');
                localStorage.setItem('glsr-expand', elId);
                self.scrollSectionIntoView_($(elId));
            });
            $(window).on('load', function () {
                self.scrollSectionIntoView_($(localStorage.getItem('glsr-expand')));
            });
        },

        /** @return string */
        getAction_: function (bool) {
            return bool ? 'add' : 'remove';
        },

        /** @return void */
        onClick_: function (ev) {
            ev.preventDefault();
            var el = ev.currentTarget;
            el.blur();
            this.toggleCollapsibleViewSections_(el);
            this.setTab_(el);
            location.hash = 'tab-' + el.getAttribute('href').slice(1);
        },

        /** @return void */
        onHashchange_: function () {
            var id = location.hash.split('#tab-')[1];
            for(var i = 0; i < this.views.length; i++) {
                if (id !== this.views[i].id) continue;
                this.setTab_(this.tabs[i]);
                break;
            }
        },

        /** @return void */
        scrollSectionIntoView_: function (el) {
            if (el.length) {
                var parentEl = el.parent().parent();
                parentEl.removeClass('collapsed');
                this.toggleCollapsibleSections_(parentEl);
                parentEl.removeClass('collapsed');
                el.parent().removeClass('closed').find('.glsr-accordion-trigger').attr('aria-expanded', true);
                window.setTimeout(function () {
                    el.parent()[0].scrollIntoView({behavior: 'smooth', block: 'center'});
                    localStorage.removeItem('glsr-expand');
                }, 10);
            }
        },

        /** @return void */
        setReferrer_: function (index) {
            var referrerUrl = this.referrer.value.split('#')[0] + '#tab-' + this.views[index].id;
            this.referrer.value = referrerUrl;
        },

        /** @return void */
        setTab_: function (el) {
            [].forEach.call(this.tabs, function (tab, index) {
                var action = this.getAction_(tab === el);
                if (action === 'add') {
                    this.active.value = this.views[index].id;
                    this.setReferrer_(index);
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
            }.bind(this));
        },

        /** @return void */
        toggleCollapsibleSections_: function (viewEl) {
            var action = viewEl.hasClass('collapsed') ? 'remove' : 'add';
            viewEl[action + 'Class']('collapsed')
                .find('.glsr-card.postbox')[action + 'Class']('closed')
                .find('.glsr-accordion-trigger').attr('aria-expanded', action !== 'add');
        },

        /** @return void */
        toggleCollapsibleViewSections_: function (el) {
            if (!el.classList.contains('nav-tab-active')) return;
            var view = $(el.getAttribute('href'));
            this.toggleCollapsibleSections_(view);
        },
    };
})(jQuery);
