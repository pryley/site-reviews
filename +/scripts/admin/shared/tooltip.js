const ANCHORED = !!globalThis.CSS?.supports?.('anchor-name', '--a');
const DELAY = 300; // as wp-tooltip.js of WordPress
const EDGE = 4; // the space between a tooltip and the window's edge
const GRACE = 200; // time for the pointer to reach the tooltip
const OFFSET = 8; // the space between an element and its tooltip
const SUPPORTED = 'function' === typeof globalThis.HTMLElement?.prototype.showPopover;

let count = 0;

/**
 * The tooltips and popovers that Modules\Html\Tooltip prints: a tooltip shows on hover and focus of the
 * element that names it in data-tooltip, and a click on the button of a setting's help keeps it open.
 */
export default class Tooltip {
    static init (root = document) {
        if (!SUPPORTED) return;
        const bubbles = new Map([...root.querySelectorAll('.glsr-tooltip[popover][id]')].map(el => [el.id, el]));
        root.querySelectorAll('[data-tooltip], button[popovertarget]').forEach(trigger => {
            const bubble = bubbles.get(trigger.dataset.tooltip || trigger.getAttribute('popovertarget'));
            if (bubble) {
                new Tooltip(trigger, bubble)
            }
        })
    }

    constructor (trigger, bubble) {
        this.bubble = bubble;
        this.onKeydown = ev => 'Escape' === ev.key && this.hide();
        this.open = false;
        this.pinned = false; // if a click opened the help
        this.pressed = false; // if the focus comes from a click
        this.timer = 0;
        this.trigger = trigger;
        if (ANCHORED) {
            const name = `--glsr-tooltip-${++count}`;
            trigger.style.setProperty('anchor-name', name)
            bubble.style.setProperty('position-anchor', name)
        }
        bubble.addEventListener('toggle', ev => this.onToggle(ev))
        if (trigger.dataset.tooltip) {
            this.initTooltip()
        } else if (trigger.classList.contains('glsr-help')) {
            this.initHelp()
        }
    }

    hide () {
        clearTimeout(this.timer)
        document.removeEventListener('keydown', this.onKeydown)
        if (!this.open || !this.bubble.isConnected) return;
        this.bubble.hidePopover()
        this.open = false;
        this.pinned = false;
    }

    initHelp () {
        const { bubble, trigger } = this;
        [bubble, trigger].forEach(el => el.addEventListener('focusout', ev => this.onFocusout(ev)))
        trigger.addEventListener('click', ev => this.onClick(ev))
        trigger.addEventListener('focus', () => this.pressed || this.show())
        trigger.addEventListener('mousedown', () => (this.pressed = true))
        this.initHover()
    }

    // the pointer may move from the element into its tooltip
    initHover () {
        [this.bubble, this.trigger].forEach(el => {
            el.addEventListener('mouseenter', () => this.open ? clearTimeout(this.timer) : this.show(DELAY))
            el.addEventListener('mouseleave', () => this.onLeave())
        })
    }

    initTooltip () {
        const { trigger } = this;
        trigger.addEventListener('blur', () => {
            this.pressed = false;
            this.hide()
        })
        trigger.addEventListener('focus', () => this.pressed || this.show())
        trigger.addEventListener('mousedown', () => {
            this.pressed = true;
            this.hide()
        })
        this.initHover()
    }

    onClick (ev) {
        ev.preventDefault() // the browser would close what hover opened
        this.pressed = false;
        if (this.pinned) {
            this.hide()
            return;
        }
        this.show()
        this.pinned = true;
    }

    onFocusout (ev) {
        this.pressed = false;
        if (this.pinned || this.trigger === ev.relatedTarget || this.bubble.contains(ev.relatedTarget)) return;
        this.hide()
    }

    onLeave () {
        clearTimeout(this.timer)
        if (!this.pinned) {
            this.timer = setTimeout(() => this.hide(), GRACE);
        }
    }

    // Escape and a click outside close a popover
    onToggle (ev) {
        this.open = 'open' === ev.newState;
        if (this.open) {
            this.place()
            return;
        }
        this.pinned = false;
    }

    // only on a browser without CSS anchor positioning
    place () {
        if (ANCHORED) return;
        const { bubble, trigger } = this;
        const box = trigger.getBoundingClientRect();
        const { offsetHeight: height, offsetWidth: width } = bubble;
        const below = bubble.classList.contains('is-below') || box.top < height + OFFSET;
        const left = bubble.classList.contains('is-start') ? box.left - OFFSET : box.left + (box.width - width) / 2;
        bubble.style.left = `${Math.max(EDGE, Math.min(left, document.documentElement.clientWidth - width - EDGE))}px`;
        bubble.style.top = `${below ? box.bottom + OFFSET : box.top - height - OFFSET}px`;
    }

    show (delay = 0) {
        clearTimeout(this.timer)
        if (delay) {
            this.timer = setTimeout(() => this.show(), delay);
            return;
        }
        if (this.open || !this.bubble.isConnected) return;
        this.bubble.showPopover()
        this.open = true;
        if (this.trigger.dataset.tooltip) {
            document.addEventListener('keydown', this.onKeydown) // the browser closes a popover on Escape
        }
        this.place()
    }
}
