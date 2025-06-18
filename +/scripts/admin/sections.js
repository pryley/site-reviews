
const defaults = {
    expandSelector: '.glsr-nav-view, .glsr-notice, .glsr-page-header',
    tabSelector: '.glsr-nav-tab',
};

class Sections {
    constructor (options) {
        options = jQuery.extend({}, defaults, options);
        jQuery(options.expandSelector).on('click', 'a[data-expand]', this.onExpand.bind(this))
        jQuery(options.tabSelector).each((index, el) => {
            jQuery(el).on('click', this.onClick.bind(this))
        })
        jQuery(document).on('wp-updates-notice-added', () => {
            jQuery('.glsr-notice a[data-expand]').on('click', this.onExpand.bind(this))
        })
        this.scrollCardIntoView(jQuery(localStorage.getItem('glsr-expand')))
    }

    collapseCardsInView ($view) {
        $view.addClass('collapsed')
            .find('.glsr-card').addClass('closed')
            .find('.glsr-accordion-trigger').attr('aria-expanded', 'false')
    }

    expandCardsInView ($view) {
        $view.removeClass('collapsed')
            .find('.glsr-card').removeClass('closed')
            .find('.glsr-accordion-trigger').attr('aria-expanded', 'true')
    }

    onExpand (ev) {
        const id = jQuery(ev.currentTarget).data('expand');
        localStorage.setItem('glsr-expand', id)
        this.scrollCardIntoView(jQuery(id))
    }

    onClick (ev) {
        ev.preventDefault()
        this.toggleCardsInView(jQuery(ev.currentTarget))
    }

    scrollCardIntoView ($el) {
        if (!$el.length) return;
        const $view = $el.closest('.glsr-nav-view');
        this.collapseCardsInView($view)
        window.setTimeout(() => {
            const $card = $el.closest('.glsr-card');
            $card.removeClass('closed').find('.glsr-accordion-trigger').attr('aria-expanded', 'true')
            localStorage.removeItem('glsr-expand')
            // if notices are visible, do not scroll section into view
            if (jQuery('#glsr-notices .notice').length) return;
            // if height of card is greater than window height, scroll to start, otherwise scroll to center
            $card[0].scrollIntoView({
                behavior: 'smooth',
                block: ($card.outerHeight() >= window.innerHeight ? 'start' : 'center'),
            })
        }, 10)
    }

    toggleCardsInView ($tab) {
        if (!$tab.hasClass('nav-tab-active')) return;
        const $view = jQuery('#'+$tab.data('id'));
        if (!$view.length) return;
        if ($view.hasClass('collapsed')) {
            this.expandCardsInView($view)
        } else {
            this.collapseCardsInView($view)
        }
    }
}

export default Sections;
