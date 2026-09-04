import dom from '@/public/dom.js'
import css from '../../styles/public/modal-shadow.css'

/**
 * A native <dialog> in a shadow root owns the modal chrome (frame, backdrop,
 * close button) so theme CSS cannot restyle it; deliberate styling goes
 * through the --glsr-modal-* custom properties and the dialog/close parts.
 * The header/body/footer regions are light-DOM children of the host, slotted
 * into the dialog, so page and theme CSS reaches everything injected into
 * them (reviews, forms, style-pack button classes).
 */

const FOCUSABLE_ELEMENTS = [
    '[contenteditable]',
    '[tabindex]:not([tabindex^="-"])',
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
];

const defaults = {
    focus: false,
    onClose: () => {},
    onOpen: () => {},
};

const closeTrigger = 'data-glsr-close';
const modalClass = 'glsr-modal';
const openTrigger = 'data-glsr-trigger';

const supported = 'undefined' !== typeof HTMLDialogElement
    && !!HTMLDialogElement.prototype.showModal
    && !!Element.prototype.attachShadow;

let openCount = 0;

const deprecatedNotices = [];
const deprecated = (surface, replacement) => {
    if (deprecatedNotices.includes(surface)) return;
    deprecatedNotices.push(surface);
    console.info(`[site-reviews] ${surface} is deprecated; use ${replacement} instead.`)
};

// A third party's throwing callback degrades to a logged error; it must not
// abort the open or close sequence.
const guard = (fn) => {
    try {
        fn()
    } catch (error) {
        console.error('[site-reviews] a modal callback failed:', error)
    }
};

// Constructable-stylesheet handshake; jsdom constructs a CSSStyleSheet but
// has no replaceSync, so the whole probe stays inside the try and the
// fallback is an inline <style>. Modelled on the premium lightbox/alerts.
const adoptCss = (root) => {
    try {
        const sheet = new CSSStyleSheet();
        sheet.replaceSync(css);
        root.adoptedStyleSheets = [sheet];
        return true;
    } catch (ignored) {
        return false;
    }
};

const attr = (className, attributes = {}) => {
    attributes.class = modalClass + '__' + className;
    return attributes
}

class Modal {
    constructor (id, config = {}) {
        this.id = id;
        this.events = {
            _cancel: this._onCancel.bind(this),
            _click: this._onClick.bind(this),
            _closed: this._onClosed.bind(this),
            _dialogClick: this._onDialogClick.bind(this),
            _open: this._openModal.bind(this),
            _transitionend: this._onTransitionEnd.bind(this),
        };
        this.triggers = [];
        this._config(config)
        this._reset()
    }

    get dom () { // @deprecated v8.3
        deprecated('Modal.dom', 'the modal instance API (style, hideClose, header/content/footer)')
        if (!this._dom) {
            this._dom = {
                body: this._body,
                close: this._close,
                content: this._regions ? this._regions.content : null,
                dialog: this._dialog,
                footer: this._regions ? this._regions.footer : null,
                header: this._regions ? this._regions.header : null,
            };
        }
        return this._dom
    }

    get isOpen () {
        return !!this.root
    }

    get trigger () {
        return this._trigger
    }

    close () {
        this._closeModal()
    }

    content (html, attributes) {
        return this._region('content', html, attributes)
    }

    footer (html, attributes) {
        return this._region('footer', html, attributes)
    }

    header (html, attributes) {
        const el = this._region('header', html, attributes);
        if (el && this._dialog) {
            // An IDREF cannot cross the shadow boundary, so the dialog's
            // accessible name is copied from the header text instead.
            const label = el.textContent.trim();
            if (label) {
                this._dialog.setAttribute('aria-label', label)
            } else {
                this._dialog.removeAttribute('aria-label')
            }
        }
        return el
    }

    hideClose () {
        if (this._close) {
            this._close.hidden = true;
        }
    }

    style (styles) {
        if (!this._dialog) return;
        if ('string' === typeof styles) {
            this._dialog.style.cssText = styles;
        } else if (styles && 'object' === typeof styles) {
            Object.assign(this._dialog.style, styles)
        }
    }

    // The dialog's height is fit-content, and a content swap moves it in one
    // frame. FLIP smooths it: pin the height the dialog last painted at, let
    // layout resolve the height the new content wants, and let the shadow
    // sheet's height transition carry it across. The pre-swap height cannot
    // be measured here — by the time the mutation callback runs, layout
    // already answers for the new content — so it is the tracked height the
    // resize observer saw last. Pin and measurements land before paint, so
    // neither is ever visible; a retarget mid-transition starts from the
    // tracked height, which follows the transition frame by frame.
    _animateResize () {
        const dialog = this._dialog;
        const from = this._lastHeight;
        if (!dialog || !dialog.open || null == from) return;
        dialog.style.height = '';
        const to = dialog.offsetHeight;
        if (Math.abs(to - from) < 1) return;
        dialog.style.height = from + 'px';
        void dialog.offsetHeight; // commit the pinned height before retargeting
        dialog.style.height = to + 'px';
    }

    _closeModal (event = null) {
        if (!this._dialog || !this._dialog.open) return;
        this._dialog.close()
    }

    _config (config) {
        this.config = Object.assign({}, defaults, config);
        return this;
    }

    _insertModal () {
        const host = dom('div', { class: modalClass, id: this.id });
        const root = host.attachShadow({ mode: 'open' });
        const close = dom('button', {
            'aria-label': GLSR.text.close_modal,
            class: 'close',
            part: 'close',
            type: 'button',
        });
        const dialog = dom('dialog', { part: 'dialog' },
            close,
            dom('slot', { name: 'header' }),
            dom('slot', { name: 'body' }),
            dom('slot', { name: 'footer' }),
        );
        if (!adoptCss(root)) {
            root.appendChild(dom('style', {}, css))
        }
        root.appendChild(dialog)
        const content = dom('div', attr('content', { tabindex: -1 }));
        const header = dom('div', attr('header', { slot: 'header' }));
        const footer = dom('div', attr('footer', { slot: 'footer' }));
        const body = dom('div', attr('body', { slot: 'body' }), dom('div', attr('inner'), content));
        host.append(header, body, footer)
        this._body = body;
        this._close = close;
        this._dialog = dialog;
        this._regions = { content, footer, header };
        this.root = document.body.appendChild(host);
        close.addEventListener('click', this.events._click)
        dialog.addEventListener('cancel', this.events._cancel)
        dialog.addEventListener('click', this.events._dialogClick)
        dialog.addEventListener('close', this.events._closed)
        dialog.addEventListener('transitionend', this.events._transitionend)
        host.addEventListener('click', this.events._click)
        // Region mutations are the discrete moments to glide between — hidden
        // included, because content mounted hidden moves the dialog on the
        // reveal, not the mount. The ResizeObserver only keeps _lastHeight
        // current (offsetHeight rather than the bounding rect, which the
        // entrance scale() would distort); it must not retarget the dialog
        // itself, or it would chase every frame of a size change already in
        // transition.
        this._observer = new MutationObserver(() => this._animateResize());
        this._observer.observe(host, { attributeFilter: ['hidden'], childList: true, subtree: true })
        this._resizeObserver = new ResizeObserver(() => {
            if (this._dialog) {
                this._lastHeight = this._dialog.offsetHeight;
            }
        });
        this._resizeObserver.observe(dialog)
    }

    // A form with unsaved input blocks the first Esc or backdrop close and
    // cues the refusal; the second attempt (or the close button) closes.
    _isDirtyGuarded () {
        if (this._cancelWarned) return false;
        const dirty = Array.from(this.root.querySelectorAll('input, textarea, select')).some(el => {
            if ('hidden' === el.type) return false;
            if ('checkbox' === el.type || 'radio' === el.type) return el.checked !== el.defaultChecked;
            if ('SELECT' === el.tagName) {
                return Array.from(el.options).some(option => option.selected !== option.defaultSelected);
            }
            return el.value !== el.defaultValue;
        });
        if (!dirty) return false;
        this._cancelWarned = true;
        this._dialog.classList.add('is-blocked')
        setTimeout(() => this._dialog && this._dialog.classList.remove('is-blocked'), 400)
        return true;
    }

    _onCancel (event) {
        if (this._isDirtyGuarded()) {
            event.preventDefault()
        }
    }

    _onClick (event) {
        if (event.target.closest(`[${closeTrigger}]`) || event.target === this._close) {
            event.preventDefault()
            this._closeModal(event)
        }
    }

    _onClosed (event) {
        openCount = Math.max(0, openCount - 1);
        if (0 === openCount) {
            document.documentElement.classList.remove('glsr-modal-open')
        }
        guard(() => this.config.onClose(this, event))
        guard(() => GLSR.Event.trigger('site-reviews/modal/close', this, event))
        if (this._trigger && this._trigger.focus) {
            this._trigger.focus()
        }
        const host = this.root;
        const dialog = this._dialog;
        this._reset()
        // The host outlives the close so the exit transition can run; the
        // timer covers browsers that close discretely.
        const timer = setTimeout(() => host.remove(), 500);
        dialog.addEventListener('transitionend', () => {
            clearTimeout(timer)
            host.remove()
        }, { once: true })
    }

    _onDialogClick (event) {
        if (event.target !== this._dialog) return; // a backdrop click targets the dialog itself
        const rect = this._dialog.getBoundingClientRect();
        const inDialog = rect.top <= event.clientY && event.clientY <= rect.bottom
            && rect.left <= event.clientX && event.clientX <= rect.right;
        if (!inDialog && !this._isDirtyGuarded()) {
            this._closeModal(event)
        }
    }

    // Once the glide lands, the pin comes off so the height is intrinsic
    // again; px to fit-content resolves to the same used value, so nothing
    // moves. A retargeted transition ends with transitioncancel, not here.
    _onTransitionEnd (event) {
        if ('height' === event.propertyName && event.target === this._dialog) {
            this._dialog.style.height = '';
        }
    }

    _openModal (event) {
        if (this.root) return;
        this._trigger = document.activeElement;
        if (event) {
            event.preventDefault()
            this._trigger = event.currentTarget;
        }
        this._insertModal()
        openCount++;
        document.documentElement.classList.add('glsr-modal-open')
        guard(() => this.config.onOpen(this, event)) // triggered before the modal is visible
        guard(() => GLSR.Event.trigger('site-reviews/modal/open', this, event))
        this._dialog.showModal()
        if (this.config.focus) {
            this._setFocusToFirstNode()
        }
    }

    _region (name, html, attributes = null) {
        if (!this._regions || !this._regions[name]) return null;
        const el = this._regions[name];
        if (undefined === html || null === html) return el; // getter: no arguments leaves the region untouched
        el.textContent = '';
        if (html instanceof Node) {
            el.appendChild(dom('div', attributes || {}, html))
        } else if ('' !== html) {
            const div = dom('div', attributes || {});
            div.innerHTML = html;
            el.appendChild(div)
        }
        return el;
    }

    _registerTrigger (el) {
        this._removeTrigger(el)
        el.addEventListener('click', this.events._open)
        this.triggers.push(el)
    }

    _removeTrigger (el) {
        this.triggers = this.triggers.filter(trigger => trigger !== el)
        el.removeEventListener('click', this.events._open)
    }

    _removeTriggers () {
        this.triggers.forEach(el => this._removeTrigger(el))
        this.triggers = [];
    }

    _reset () {
        if (this._observer) {
            this._observer.disconnect()
        }
        if (this._resizeObserver) {
            this._resizeObserver.disconnect()
        }
        this.root = null;
        this._body = null;
        this._cancelWarned = false;
        this._close = null;
        this._dialog = null;
        this._dom = null; // @deprecated v8.3
        this._lastHeight = null;
        this._observer = null;
        this._regions = null;
        this._resizeObserver = null;
        this._trigger = null;
    }

    _setFocusToFirstNode () {
        const nodes = Array.from(this.root.querySelectorAll(FOCUSABLE_ELEMENTS));
        const target = nodes.find(el => !el.hasAttribute(closeTrigger) && null !== el.offsetParent);
        if (target) {
            target.focus()
        }
    }
}

const modals = {};

const close = (id) => {
    if (!id) {
        for (let key in modals) {
            modals[key]._closeModal()
        }
    } else if (modals[id]) {
        modals[id]._closeModal()
    }
}

const get = (id) => modals[id] || null;

const modify = (id, callback) => { // @deprecated v8.3
    deprecated('GLSR.Modal.modify()', 'GLSR.Modal.get()')
    const modal = get(id);
    if (modal) {
        callback(modal)
    }
};

const init = (id, config) => {
    if (!supported) return;
    let modal;
    if (modals[id]) {
        modal = modals[id];
        modal._removeTriggers()
        if (config) {
            modal._config(config)
        }
    } else {
        modal = new Modal(id, config);
    }
    document.querySelectorAll(`[${openTrigger}]`).forEach(el => {
        if (id === el.getAttribute(openTrigger)) {
            modal._registerTrigger(el)
        }
    })
    modals[id] = modal;
}

const open = (id, config) => {
    if (!supported) return;
    let modal;
    if (modals[id]) {
        modal = modals[id];
        if (config) {
            modal._config(config)
        }
    } else {
        modal = new Modal(id, config);
    }
    modals[id] = modal;
    modal._openModal()
}

export default { close, get, init, modify, open }
