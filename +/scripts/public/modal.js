import { clearAllBodyScrollLocks, disableBodyScroll } from 'body-scroll-lock';

const FOCUSABLE_ELEMENTS = [
    '[contenteditable]',
    '[tabindex]:not([tabindex^="-"])',
    'a[href]',
    'button:not([disabled]):not([aria-hidden])',
    'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',
    'select:not([disabled]):not([aria-hidden])',
    'textarea:not([disabled]):not([aria-hidden])',
]

class Modal {
    constructor ({
        closeTrigger = 'data-glsr-close',
        onClose = () => {},
        onOpen = () => {},
        openClass = 'is-open',
        openTrigger = 'data-glsr-trigger',
        targetModalId = 'glsr-modal',
        triggers = [],
    }) {
        this.modal = document.getElementById(targetModalId)
        this.config = { openTrigger, closeTrigger, openClass, onOpen, onClose }
        this.events = {
            mouseup: this.onClick.bind(this),
            keydown: this.onKeydown.bind(this),
            touchstart: this.onClick.bind(this),
        };
        if (triggers.length > 0) {
            this.registerTriggers(triggers)
        }
    }

    addEventListeners () {
        this.eventListener(this.modal, 'add', ['mouseup', 'touchstart'])
        this.eventListener(document, 'add', ['keydown'])
    }

    closeModal (event = null) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        this.modal.setAttribute('aria-hidden', 'true')
        this.removeEventListeners()
        clearAllBodyScrollLocks()
        if (this.activeElement && this.activeElement.focus) {
            this.activeElement.focus()
        }
        const handler = () => {
            this.modal.classList.remove(this.config.openClass)
            this.modal.removeEventListener('animationend', handler, false)
            this.config.onClose(this.modal, this.activeElement, event) // triggered after the modal is hidden
        }
        this.modal.addEventListener('animationend', handler, false)
    }

    closeModalById (targetModal) {
        this.modal = document.getElementById(targetModal)
        if (this.modal) this.closeModal()
    }

    eventListener(el, action, events) {
        events.forEach(event => el[action+'EventListener'](event, this.events[event]));
    }

    getFocusableNodes () {
        const nodes = this.modal.querySelectorAll(FOCUSABLE_ELEMENTS)
        return Array.prototype.slice.call(nodes)
    }

    onClick (event) {
        if (event.target.hasAttribute(this.config.closeTrigger)) {
            this.closeModal(event)
        }
    }

    onKeydown (event) {
        if (event.keyCode === 27) this.closeModal(event) // esc
        if (event.keyCode === 9) this.retainFocus(event) // tab
    }

    openModal (event = null) {
        this.activeElement = document.activeElement
        if (event) {
            event.preventDefault()
            this.activeElement = event.currentTarget
        }
        this.config.onOpen(this.modal, this.activeElement, event) // triggered before the modal is visible
        this.modal.setAttribute('aria-hidden', 'false')
        this.modal.classList.add(this.config.openClass)
        disableBodyScroll(this.modal.querySelector('[data-glsr-modal]'))
        this.addEventListeners()
        const handler = () => {
            this.modal.removeEventListener('animationend', handler, false)
            this.setFocusToFirstNode()
        }
        this.modal.addEventListener('animationend', handler, false)
    }

    registerTriggers (triggers) {
        triggers.filter(Boolean).forEach(triggerEl => {
            if (triggerEl.triggerModal) {
                triggerEl.removeEventListener('click', triggerEl.triggerModal)
            }
            triggerEl.triggerModal = this.openModal.bind(this) // store the handler directly on the trigger element
            triggerEl.addEventListener('click', triggerEl.triggerModal)
        })
    }

    removeEventListeners () {
        this.eventListener(this.modal, 'remove', ['mouseup', 'touchstart'])
        this.eventListener(document, 'remove', ['keydown'])
    }

    retainFocus (event) {
        let focusableNodes = this.getFocusableNodes()
        if (focusableNodes.length === 0) return
        focusableNodes = focusableNodes.filter(node => (node.offsetParent !== null)) // removes hidden nodes
        if (!this.modal.contains(document.activeElement)) {
            focusableNodes[0].focus()
        } else {
            const focusedItemIndex = focusableNodes.indexOf(document.activeElement)
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

    setFocusToFirstNode () {
        const focusableNodes = this.getFocusableNodes()
        if (focusableNodes.length === 0) return
        const focusableContentNodes = focusableNodes.filter(node => !node.hasAttribute(this.config.closeTrigger))
        if (focusableContentNodes.length > 0) focusableContentNodes[0].focus()
        if (focusableContentNodes.length === 0) focusableNodes[0].focus()
    }
}

const activeModals = {}

/**
 * @param {string} targetModalId [The id of the modal to close]
 * @return {void}
 */
const close = (targetModalId) => {
    if (targetModalId) {
       activeModals[targetModalId].closeModalById(targetModalId)
    } else {
        for (let id in activeModals) {
            activeModals[id].closeModal()
        }
    }
}

/**
 * Generates an Object containing modals and their respective triggers
 * @param {array} triggers [An array of all triggers]
 * @param {string} triggerAttr [The data-attribute which triggers the module]
 * @return {object}
 */
const generateTriggerMap = (triggers, triggerAttr) => {
    const triggerMap = {}
    triggers.forEach(trigger => {
        const targetModalId = trigger.attributes[triggerAttr].value
        if (triggerMap[targetModalId] === undefined) triggerMap[targetModalId] = []
        triggerMap[targetModalId].push(trigger)
    })
    return triggerMap
}

/**
 * @param {object} config
 * @return void
 */
const init = (config) => {
    const options = Object.assign({}, { openTrigger: 'data-glsr-trigger' }, config)
    const triggers = Array.prototype.slice.call(document.querySelectorAll(`[${ options.openTrigger }]`))
    const triggerMap = generateTriggerMap(triggers, options.openTrigger)
    Object.keys(triggerMap).forEach(key => {
        options.targetModalId = key
        options.triggers = triggerMap[key]
        activeModals[key] = new Modal(options)
    })
    return activeModals
}

/**
 * @param {string} targetModalId [The id of the modal to display]
 * @param {object} config [The configuration object to pass]
 * @return {void}
 */
const open = (targetModalId, config) => {
    const options = config || {}
    options.targetModalId = targetModalId
    if (activeModals[targetModalId]) {
        activeModals[targetModalId].removeEventListeners()
    }
    activeModals[targetModalId] = new Modal(options)
    activeModals[targetModalId].openModal()
}

export default { init, open, close }
