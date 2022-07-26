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
    id: 'glsr-modal',
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
    constructor (options) {
        this.activeElement = null;
        this.config = Object.assign({}, defaults, options);
        this.id = this.config.id;
        this.events = {
            mouseup: this._onClick.bind(this),
            keydown: this._onKeydown.bind(this),
            touchstart: this._onClick.bind(this),
        };
        this.modal = null;
        this.modalTrigger = this._openModal.bind(this);
        this.triggers = [];
    }

    _closeModal (event = null) {
        if (event) {
            event.preventDefault()
            event.stopPropagation()
        }
        this.modal.setAttribute('aria-hidden', 'true')
        this._eventHandler('remove')
        unlock(this.modal.content)
        if (this.activeElement && this.activeElement.focus) {
            this.activeElement.focus()
        }
        const handler = () => {
            this.modal.removeEventListener('animationend', handler, false)
            openModals.pop()
            this.modal.classList.remove(openClass)
            this.config.onClose(this.modal, this.activeElement, event) // triggered after the modal is hidden
            GLSR.Event.trigger('site-reviews/modal/close', this.modal, this.activeElement, event)
            debounce(() => {
                this.modal.remove()
                this.activeElement = null;
                this.modal = null;
            })()
        }
        this.modal.addEventListener('animationend', handler, false)
    }

    _eventHandler (action) {
        this._eventListener(this.modal, action, ['mouseup', 'touchstart'])
        this._eventListener(document, action, ['keydown'])
    }

    _eventListener (el, action, events) {
        events.forEach(event => el[action+'EventListener'](event, this.events[event]))
    }

    _focusableNodes () {
        const nodes = this.modal.querySelectorAll(FOCUSABLE_ELEMENTS);
        return [].slice.call(nodes)
    }

    _modal () {
        const close = dom('button', attr('close', {'aria-label': GLSR.text.closemodal, 'data-glsr-close': '' }));
        const content = dom('div', attr('content'));
        const header = dom('div', attr('header'));
        const footer = dom('div', attr('footer'));
        const modal = dom('div', { class: modalClass, id: this.id, 'aria-hidden': true },
            dom('div', attr('overlay', { tabindex: -1, 'data-glsr-close': '' }),
                dom('div', attr('dialog', { 'aria-modal': true, role: 'dialog' }),
                    close, header, content, footer
                )
            )
        )
        modal.close = close;
        modal.content = content;
        modal.footer = footer;
        modal.header = header;
        return modal
    }

    _onClick (event) {
        if (event.target.hasAttribute(closeTrigger)) {
            this._closeModal(event)
        }
    }

    _onKeydown (event) {
        if (event.keyCode === 27 && openModals.slice(-1)[0] === this.id) { // esc
            this._closeModal(event)
        }
        if (event.keyCode === 9) this._retainFocus(event) // tab
    }

    _openModal (event) {
        openModals.push(this.id)
        this.activeElement = document.activeElement;
        if (event) {
            event.preventDefault()
            this.activeElement = event.currentTarget;
        }
        this.modal = document.body.appendChild(this._modal());
        lock(this.modal.content)
        this.config.onOpen(this.modal, this.activeElement) // triggered before the modal is visible
        this.modal.setAttribute('aria-hidden', 'false')
        this.modal.classList.add(openClass)
        this._eventHandler('add')
        const handler = () => {
            this.modal.removeEventListener('animationend', handler, false)
            this._setFocusToFirstNode()
        }
        this.modal.addEventListener('animationend', handler, false)
        GLSR.Event.trigger('site-reviews/modal/open', this.modal, this.activeElement)
    }

    _registerTrigger (el) {
        this._removeTrigger(el)
        el.addEventListener('click', this.modalTrigger)
        this.triggers.push(el)
    }

    _removeTrigger (el) {
        this.triggers.filter(trigger => trigger !== el)
        el.removeEventListener('click', this.modalTrigger)
    }

    _removeTriggers () {
        this.triggers.forEach(el => this._removeTrigger(el))
        this.triggers = [];
    }

    _retainFocus (event) {
        let focusableNodes = this._focusableNodes();
        if (focusableNodes.length === 0) return
        focusableNodes = focusableNodes.filter(node => (node.offsetParent !== null)); // removes hidden nodes
        if (!this.modal.contains(document.activeElement)) {
            focusableNodes[0].focus()
        } else {
            const focusedItemIndex = focusableNodes.indexOf(document.activeElement);
            if (event.shiftKey && focusedItemIndex === 0) {
                focusableNodes[focusableNodes.length - 1].focus()
                event.preventDefault()
            }
            if (!event.shiftKey && focusableNodes.length > 0 && focusedItemIndex === focusableNodes.length - 1) {
                focusableNodes[0].focus()
                event.preventDefault()
            }
        }
    }

    _setFocusToFirstNode () {
        const focusableNodes = this._focusableNodes();
        if (focusableNodes.length === 0) return
        const focusableContentNodes = focusableNodes.filter(node => !node.hasAttribute(closeTrigger));
        if (focusableContentNodes.length > 0) {
            focusableContentNodes[0].focus()
        }
        if (focusableContentNodes.length === 0) {
            focusableNodes[0].focus()
        }
    }
}

const activeModals = {};
const openModals = [];

const close = (modalId) => {
    if (!modalId) {
        for (let id in activeModals) {
            activeModals[id]._closeModal()
        }
    } else if (activeModals[modalId]) {
        activeModals[modalId]._closeModal()
    }
}

const init = (config) => {
    const options = Object.assign({}, defaults, config || {});
    const modal = activeModals[options.id] || new Modal(options);
    modal._removeTriggers()
    document.querySelectorAll('[' + openTrigger + ']').forEach(el => {
        if (options.id === el.attributes[openTrigger].value) {
            modal._registerTrigger(el)
        }
    })
    activeModals[options.id] = modal;
    return activeModals
}

const open = (id, config) => {
    const options = Object.assign({}, { id }, config || {});
    const modal = activeModals[options.id] || new Modal(options);
    if (modal.modal) {
        modal._eventHandler('remove')
    }
    modal._openModal()
}

export default { close, init, open }
