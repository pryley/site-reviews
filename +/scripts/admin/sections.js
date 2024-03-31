
const defaults = {
    expandSelector: '.glsr-nav-view, .glsr-notice',
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
        this.scrollIntoView(jQuery(localStorage.getItem('glsr-expand')))
    }

    onExpand (ev) {
        const id = jQuery(ev.currentTarget).data('expand');
        localStorage.setItem('glsr-expand', id)
        this.scrollIntoView(jQuery(id))
    }

    onClick (ev) {
        ev.preventDefault()
        this.toggleTabView(jQuery(ev.currentTarget))
    }

    scrollIntoView ($el) {
        if (!$el.length) return;
        const $nav = $el.closest('.glsr-nav-view');
        this.toggleSection($nav)
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

    toggleSection ($el) {
        const action = $el.hasClass('collapsed') ? 'removeClass' : 'addClass';
        $el[action]('collapsed')
            .find('.glsr-card')[action]('closed')
            .find('.glsr-accordion-trigger').attr('aria-expanded', action === 'addClass' ? 'false' : 'true')
    }

    toggleTabView ($el) {
        if (!$el.hasClass('nav-tab-active')) return;
        const id = $el.data('id');
        if (id) {
            this.toggleSection(jQuery(`#${id}`))
        }
    }
}

export default Sections;
