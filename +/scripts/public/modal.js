// see: https://github.com/ghosh/Micromodal

import dom from '@/public/dom.js'
import { debounce } from '@/public/helpers.js'
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
    focus: false,
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
        this.events = {
            _open: this._openModal.bind(this),
            mouseup: this._onClick.bind(this),
            keydown: this._onKeydown.bind(this),
            touchstart: this._onClick.bind(this),
        };
        this.id = id;
        this.triggers = [];
        this._config(config)
        this._reset()
    }

    header (html, attributes) {
        return this._insertHtml(this.dom.header, html, attributes)
    }

    content (html, attributes) {
        return this._insertHtml(this.dom.content, html, attributes)
    }

    footer (html, attributes) {
        return this._insertHtml(this.dom.footer, html, attributes)
    }

    _closeModal (event = null) {
        if (!modals.open.includes(this.id)) return;
        if (event) {
            event.preventDefault()
            event.stopPropagation()
        }
        const handler = () => {
            this.root.removeEventListener('animationend', handler, false)
            this.root.classList.remove(openClass)
            modals.open.pop()
            this.config.onClose(this, event) // triggered after the modal is hidden
            GLSR.Event.trigger('site-reviews/modal/close', this, event)
            debounce(() => this._reset())()
        }
        this.root.addEventListener('animationend', handler, false)
        this.root.setAttribute('aria-hidden', 'true')
        this._eventHandler('remove')
        unlock(this.dom.content)
        if (this.trigger && this.trigger.focus) {
            this.trigger.focus()
        }
    }

    _config (config) {
        this.config = Object.assign({}, defaults, config);
        return this;
    }

    _eventHandler (action) {
        this._eventListener(this.dom.close, action, ['keydown'])
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

    _insertHtml (el, html = null, attributes = {}) {
        if (el && null !== html) {
            if ('' !== html) {
                const div = dom('div', attributes);
                div.innerHTML = html;
                html = div.outerHTML;
            }
            el.innerHTML = html;
        }
        return el;
    }

    _insertModal () {
        const close = dom('button', attr('close', { 'aria-label': GLSR.text.closemodal, 'data-glsr-close': '' }));
        const content = dom('div', attr('content', { tabindex: -1 }));
        const header = dom('div', attr('header'));
        const footer = dom('div', attr('footer'));
        const dialog = dom('div', attr('dialog', { 'aria-modal': true, role: 'dialog' }),
            close, header, content, footer
        );
        const root = dom('div', { class: modalClass, id: this.id, 'aria-hidden': true },
            dom('div', attr('overlay', { tabindex: -1, 'data-glsr-close': '' }), dialog)
        );
        this.dom = { ...this.dom, close, content, dialog, footer, header };
        const docBody = document.querySelector('body > #page.site') ?? document.body;
        this.root = docBody.appendChild(root);
    }

    _onClick (event) {
        if (event.target.hasAttribute(closeTrigger)) {
            this._closeModal(event)
        }
    }

    _onKeydown (event) {
        if (~[13, 32].indexOf(event.keyCode) && event.target === this.dom.close) { // enter/space
            this._closeModal(event)
        }
        if (event.keyCode === 27 && modals.open.slice(-1)[0] === this.id) { // esc
            this._closeModal(event)
        }
        if (event.keyCode === 9) {
            this._retainFocus(event) // tab
        }
    }

    _openModal (event) {
        modals.open.push(this.id)
        this.trigger = document.activeElement;
        if (event) {
            event.preventDefault()
            this.trigger = event.currentTarget;
        }
        this._insertModal()
        lock(this.dom.content)
        this.config.onOpen(this, event) // triggered before the modal is visible
        GLSR.Event.trigger('site-reviews/modal/open', this, event)
        this.root.setAttribute('aria-hidden', 'false')
        this.root.classList.add(openClass)
        this._eventHandler('add')
        const handler = () => {
            this.root.removeEventListener('animationend', handler, false)
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
        this.dom = {
            close: null,
            content: null,
            footer: null,
            header: null,
        }
        if (this.root) {
            this.root.remove()
        }
        this.root = null;
        this.trigger = null;
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

    _setFocusToFirstNode () {
        if (!this.config.focus) return;
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

const modals = {
    active: {},
    open: [],
};

const close = (id) => {
    if (!id) {
        for (let key in modals.active) {
            modals.active[key]._closeModal()
        }
    } else if (modals.active[id]) {
        modals.active[id]._closeModal()
    }
}

const init = (id, config) => {
    let modal;
    if (modals.active[id]) {
        modal = modals.active[id];
        modal._removeTriggers()
        if (config) {
            modal._config(config)
        }
    } else {
        modal = new Modal(id, config);
    }
    document.querySelectorAll('[' + openTrigger + ']').forEach(el => {
        if (id === el.attributes[openTrigger].value) {
            modal._registerTrigger(el)
        }
    })
    modals.active[id] = modal;
    return modals.active
}

const modify = (id, callback) => {
    if (id && modals.active[id]) {
        callback(modals.active[id])
    }
}

const open = (id, config) => {
    let modal;
    if (modals.active[id]) {
        modal = modals.active[id];
        if (modal.root) {
            modal._eventHandler('remove')
        }
        if (config) {
            modal._config(config)
        }
    } else {
        modal = new Modal(id, config);
    }
    modals.active[id] = modal;
    modal._openModal()
}

export default { close, init, modals, modify, open }
