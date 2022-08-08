import dom from './dom.js';

const classNames = {
    hidden: 'glsr-hidden',
    readmore: 'glsr-read-more',
    visible: 'glsr-visible',
}

const selectors = {
    hiddenText: '.glsr-hidden-text',
    wrap: '.glsr-tag-value',
}

class Excerpts {
    constructor (el) {
        this.events = {
            click: this._onClick.bind(this),
        };
        (el || document).querySelectorAll(selectors.hiddenText).forEach(el => this.init(el));
    }

    init (el) {
        const readMoreLink = this._insertLink(el)
        if (!readMoreLink) return;
        if ('expand' === el.dataset.trigger) {
            readMoreLink.dataset.text = el.dataset.showLess;
            readMoreLink.removeEventListener('click', this.events.click);
            readMoreLink.addEventListener('click', this.events.click);
        }
        if ('modal' === el.dataset.trigger) {
            readMoreLink.dataset.glsrTrigger = 'glsr-modal-review';
        }
    }

    _insertLink (el) { // p.glsr-hidden-text
        let readMoreEl = el.parentElement.querySelector('.' + classNames.readmore);
        if (readMoreEl) {
            readMoreEl.parentElement.removeChild(readMoreEl);
        }
        const readMoreLink = dom('a', { href: '#' }, el.dataset.showMore);
        const readMoreSpan = dom('span', { class: classNames.readmore }, readMoreLink);
        return el.appendChild(readMoreSpan).querySelector('a')
    }

    _onClick (ev) {
        ev.preventDefault();
        const el = ev.currentTarget; // a.glsr-read-more
        const readmoreEl = el.parentElement;
        const wrapEl = el.closest(selectors.wrap);
        const parentEl = wrapEl.querySelector(selectors.hiddenText);
        const newText = el.dataset.text;
        const oldText = el.innerText;
        el.dataset.text = oldText;
        el.innerText = newText;
        el.removeEventListener('click', this.events.click);
        if (wrapEl.dataset.expanded === 'false') {
            wrapEl.querySelector('p:last-of-type').appendChild(readmoreEl)
            wrapEl.dataset.expanded = true;
        } else {
            parentEl.appendChild(readmoreEl)
            wrapEl.dataset.expanded = false;
        }
        el.addEventListener('click', this.events.click);
        el.focus();
    }
}

export default Excerpts;
