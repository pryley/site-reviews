const delay = 30;

class Flyoutmenu {
    constructor () {
        this.menu = jQuery('#glsr-flyout');
        this.items = this.menu.find('.glsr-flyout-item');
        this.mascot = this.menu.find('.glsr-flyout-mascot');
        if (this.menu.length) {
            this.mascot.on('click', this.openMenu.bind(this))
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
}

export default Flyoutmenu;
