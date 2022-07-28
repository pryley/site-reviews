// see: https://github.com/ghosh/Micromodal

import dom from './dom.js'
import { debounce } from './helpers.js'
import { lock, unlock } from 'tua-body-scroll-lock'

const FOCUSABLE_ELEMENTS = [
    '[contenteditable]',
    '[tabindex]:not([tabindex^="-"])',
    'a[href]',
    'button:not([disabled]):not([aria-hidden])',
    'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',
    'select:not([disabled]):not([aria-hidden])',
    'textarea:not([disabled]):not([aria-hidden])',
];

const defaults = {
    onClose: () => {},
    onOpen: () => {},
};

const closeTrigger = 'data-glsr-close';
const modalClass = 'glsr-modal';
const openClass = 'is-open';
const openTrigger = 'data-glsr-trigger';

const attr = (className, attributes = {}) => {
    attributes.class = modalClass + '__' + className;
    return attributes
}

class Modal {
    constructor (id, config = {}) {
        this.config = Object.assign({}, defaults, config);
        this.events = {
            _open: this._openModal.bind(this),
            _opened: this._setFocusToFirstNode.bind(this),
            mouseup: this._onClick.bind(this),
            keydown: this._onKeydown.bind(this),
            touchstart: this._onClick.bind(this),
        };
        this.id = id;
        this.triggers = [];
        this._reset()
    }

    _closeModal (event = null) {
        if (event) {
            event.preventDefault()
            event.stopPropagation()
        }
        this.root.setAttribute('aria-hidden', 'true')
        this._eventHandler('remove')
        unlock(this.content)
        if (this.trigger && this.trigger.focus) {
            this.trigger.focus()
        }
        const handler = () => {
            this.root.removeEventListener('animationend', handler, false)
            this.root.classList.remove(openClass)
            openModals.pop()
            this.config.onClose(this, event) // triggered after the modal is hidden
            GLSR.Event.trigger('site-reviews/modal/close', this.root, this.trigger, event)
            debounce(() => this._reset())()
        }
        this.root.addEventListener('animationend', handler, false)
    }

    _eventHandler (action) {
        this._eventListener(this.close, action, ['keydown'])
        this._eventListener(this.root, action, ['mouseup', 'touchstart'])
        this._eventListener(document, action, ['keydown'])
    }

    _eventListener (el, action, events) {
        if (el) {
            events.forEach(event => el[action+'EventListener'](event, this.events[event]))
        }
    }

    _focusableNodes () {
        return [].slice.call(this.root.querySelectorAll(FOCUSABLE_ELEMENTS))
    }

    _insertModal () {
        const close = dom('button', attr('close', { 'aria-label': GLSR.text.closemodal, 'data-glsr-close': '' }));
        const content = dom('div', attr('content', { tabindex: -1 }));
        const header = dom('div', attr('header'));
        const footer = dom('div', attr('footer'));
        const root = dom('div', { class: modalClass, id: this.id, 'aria-hidden': true },
            dom('div', attr('overlay', { tabindex: -1, 'data-glsr-close': '' }),
                dom('div', attr('dialog', { 'aria-modal': true, role: 'dialog' }),
                    close, header, content, footer
                )
            )
        )
        this.close = close;
        this.content = content;
        this.footer = footer;
        this.header = header;
        this.root = document.body.appendChild(root);
    }

    _onClick (event) {
        if (event.target.hasAttribute(closeTrigger)) {
            this._closeModal(event)
        }
    }

    _onKeydown (event) {
        if (~[13, 32].indexOf(event.keyCode) && event.target === this.close) { // enter/space
            this._closeModal(event)
        }
        if (event.keyCode === 27 && openModals.slice(-1)[0] === this.id) { // esc
            this._closeModal(event)
        }
        if (event.keyCode === 9) {
            this._retainFocus(event) // tab
        }
    }

    _openModal (event) {
        openModals.push(this.id)
        this.trigger = document.activeElement;
        if (event) {
            event.preventDefault()
            this.trigger = event.currentTarget;
        }
        this._insertModal()
        lock(this.content)
        GLSR.Event.on('site-reviews/modal/focus', this.events._opened)
        this.config.onOpen(this, event) // triggered before the modal is visible
        this.root.setAttribute('aria-hidden', 'false')
        this.root.classList.add(openClass)
        this._eventHandler('add')
        const handler = () => {
            this.root.removeEventListener('animationend', handler, false)
            GLSR.Event.trigger('site-reviews/modal/open', this.root, this.trigger, event)
            this._setFocusToFirstNode()
        }
        this.root.addEventListener('animationend', handler, false)
    }

    _registerTrigger (el) {
        this._removeTrigger(el)
        el.addEventListener('click', this.events._open)
        this.triggers.push(el)
    }

    _removeTrigger (el) {
        this.triggers.filter(trigger => trigger !== el)
        el.removeEventListener('click', this.events._open)
    }

    _removeTriggers () {
        this.triggers.forEach(el => this._removeTrigger(el))
        this.triggers = [];
    }

    _reset () {
        if (this.root) {
            this.root.remove()
        }
        this.close = null;
        this.content = null;
        this.footer = null;
        this.header = null;
        this.trigger = null;
        GLSR.Event.off('site-reviews/modal/focus', this.events._opened)
    }

    _retainFocus (event) {
        let focusableNodes = this._focusableNodes();
        if (focusableNodes.length === 0) return
        focusableNodes = focusableNodes.filter(node => (node.offsetParent !== null)); // removes hidden nodes
        if (!this.root.contains(document.activeElement)) {
            focusableNodes[0].focus()
        } else {
            const focusedItemIndex = focusableNodes.indexOf(document.activeElement);
            if (event.shiftKey && focusedItemIndex === 0) {
                focusableNodes[focusableNodes.length - 1].focus()
                event.preventDefault()
            } else if (!event.shiftKey && focusableNodes.length > 0 && focusedItemIndex === focusableNodes.length - 1) {
                focusableNodes[0].focus()
                event.preventDefault()
            }
        }
    }

    _setFocusToFirstNode (id) {
        if (id !== this.id) return;
        const focusableNodes = this._focusableNodes();
        if (focusableNodes.length === 0) return
        const focusableContentNodes = focusableNodes.filter(node => !node.hasAttribute(closeTrigger));
        if (focusableContentNodes.length > 0) {
            focusableContentNodes[0].focus()
        } else if (focusableContentNodes.length === 0) {
            focusableNodes[0].focus()
        }
    }
}

const activeModals = {};
const openModals = [];

const close = (id) => {
    if (!id) {
        for (let key in activeModals) {
            activeModals[key]._closeModal()
        }
    } else if (activeModals[id]) {
        activeModals[id]._closeModal()
    }
}

const init = (id, config) => {
    const modal = activeModals[id] || new Modal(id, config);
    modal._removeTriggers()
    document.querySelectorAll('[' + openTrigger + ']').forEach(el => {
        if (id === el.attributes[openTrigger].value) {
            modal._registerTrigger(el)
        }
    })
    activeModals[id] = modal;
    return activeModals
}

const open = (id, config) => {
    const modal = activeModals[id] || new Modal(id, config);
    if (modal.root) {
        modal._eventHandler('remove')
    }
    activeModals[id] = modal;
    modal._openModal()
}

window.xxx = { activeModals, openModals };

export default { close, init, open }
