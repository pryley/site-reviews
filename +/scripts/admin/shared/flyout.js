import Request from '@/public/modules/request/request.js';

const DELAY = 30; // ms between the items' transitions

/**
 * The flyout menu of the plugin screens, and its Write a Review item, which dismisses the notice.
 */
export default class Flyout {
    static init (root = document) {
        const menu = root.querySelector('#glsr-flyout');
        if (menu) {
            new Flyout(menu)
        }
    }

    constructor (menu) {
        this.menu = menu;
        this.items = [...menu.querySelectorAll('.glsr-flyout-item')];
        menu.querySelector('.glsr-flyout-mascot')?.addEventListener('click', this.toggle.bind(this))
        menu.addEventListener('focusout', this.onFocusout.bind(this))
        menu.querySelectorAll('.glsr-flyout-write-review').forEach(item => item.addEventListener('click', Flyout.dismissWriteReviewNotice))
    }

    static dismissWriteReviewNotice () {
        Request.send({ method: 'DELETE', path: 'plugin/notices/write-review' })
        document.querySelectorAll('.glsr-notice-popup[data-notice="write-review"]').forEach(el => el.remove())
    }

    onFocusout () {
        if (!this.menu.classList.contains('is-open')) return;
        window.setTimeout(() => { // after the focus has moved
            if (!this.menu.contains(document.activeElement)) {
                this.toggle()
            }
        }, 0)
    }

    toggle (ev) {
        ev?.preventDefault()
        const opening = !this.menu.classList.contains('is-open');
        this.items.forEach((item, index) => {
            const time = opening ? index * DELAY : ((this.items.length - 1) * DELAY) - (index * DELAY);
            item.style.transitionDelay = `${time}ms`;
        })
        this.menu.classList.toggle('is-open')
    }
}
