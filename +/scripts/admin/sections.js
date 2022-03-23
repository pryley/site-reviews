/** global: GLSR, jQuery */

const Sections = function (options) {
    this.options = jQuery.extend({}, this.defaults, options);
    this.tabs = document.querySelectorAll(this.options.tabSelector);
    if (!this.tabs) return;
    this.init_();
    jQuery(() => this.scrollIntoView_(jQuery(localStorage.getItem('glsr-expand'))));
};

Sections.prototype = {
    defaults: {
        expandSelectors: '.glsr-nav-view, .glsr-notice',
        tabSelector: '.glsr-nav-tab',
    },

    /** @return void */
    init_: function () {
        var self = this;
        [].forEach.call(self.tabs, function (tab, index) {
            tab.addEventListener('click', self.onClick_.bind(self));
            tab.addEventListener('touchend', self.onClick_.bind(self));
        }.bind(self));
        jQuery(self.options.expandSelectors).on('click', 'a', function () {
            var elId = this.dataset.expand;
            localStorage.setItem('glsr-expand', elId);
            self.scrollIntoView_(jQuery(elId));
        });
    },

    /** @return void */
    onClick_: function (ev) {
        ev.preventDefault();
        this.toggleCollapsibleViewSections_(ev.currentTarget);
    },

    /** @return void */
    scrollIntoView_: function (el) {
        if (el.length) {
            var navEl = el.closest('.glsr-nav-view')
            var cardEl = el.closest('.glsr-card');
            navEl.removeClass('collapsed');
            this.toggleCollapsibleSections_(navEl);
            navEl.removeClass('collapsed'); // just in case
            cardEl.removeClass('closed').find('.glsr-accordion-trigger').attr('aria-expanded', true);
            window.setTimeout(() => {
                localStorage.removeItem('glsr-expand');
                // if notices are visible, do not scroll section into view
                if (jQuery('#glsr-notices .notice').length) {
                    return;
                }
                // if height of card is greater than window height, scroll to start, otherwise scroll to center
                cardEl[0].scrollIntoView({
                    behavior: 'smooth',
                    block: (cardEl.outerHeight() >= window.innerHeight ? 'start' : 'center'),
                });
            }, 10);
        }
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
        var viewEl = jQuery('#' + el.dataset.id);
        this.toggleCollapsibleSections_(viewEl);
    },
};

export default Sections;
