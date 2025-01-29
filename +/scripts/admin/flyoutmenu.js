const delay = 30;

class Flyoutmenu {
    constructor () {
        this.menu = jQuery('#glsr-flyout');
        this.items = this.menu.find('.glsr-flyout-item');
        this.mascot = this.menu.find('.glsr-flyout-mascot');
        if (this.menu.length) {
            this.mascot.on('click', this.openMenu.bind(this))
            this.menu.on('focusout', this.toggleMenu.bind(this))
        }
    }

    openMenu (ev) {
        ev.preventDefault()
        this.items.each(index => {
            let time = index * delay;
            if (!this.menu.hasClass('is-open')) {
                time = ((this.items.length - 1) * delay) - (index * delay);
            }
            this.items[index].setAttribute('style', `transition-delay: ${time}ms;`)
        })
        this.menu.toggleClass('is-open')
    }

    toggleMenu (ev) {
        if (!this.menu.hasClass('is-open')) return
        setTimeout(() => {
            if (!jQuery.contains(this.menu.get(0), document.activeElement)) {
                this.openMenu(ev)
            }
        }, 0); // Delay to let the event queue process
    }
}

export default Flyoutmenu;
