/** global: GLSR */

const classNames = {
    hidden: 'glsr-hidden',
    readmore: 'glsr-read-more',
    visible: 'glsr-visible',
}

const selectors = {
    hiddenText: '.glsr-hidden-text',
}

class Excerpts {
    constructor (el) {
        const excerpts = (el || document).querySelectorAll(selectors.hiddenText);
        excerpts.forEach(el => this.init(el));
    }

    init (el) {
        if (el.parentNode.querySelector('.' + classNames.readmore)) return; // @hack only init once
        const trigger = el.dataset.trigger;
        const readMoreSpan = document.createElement('span');
        const readmoreLink = document.createElement('a');
        readmoreLink.setAttribute('href', '#');
        readmoreLink.innerHTML = el.dataset.showMore;
        if ('excerpt' === trigger) { // don't trigger for modals
            readmoreLink.addEventListener('click', this._onClick.bind(this));
            // we can't use dataset until the node has been inserted in the DOM
            readmoreLink.setAttribute('data-text', el.dataset.showLess);
        }
        if ('modal' === trigger) {
            // we can't use dataset until the node has been inserted in the DOM
            readmoreLink.setAttribute('data-excerpt-trigger', 'glsr-modal');
        }
        readMoreSpan.setAttribute('class', classNames.readmore);
        readMoreSpan.appendChild(readmoreLink);
        el.parentNode.insertBefore(readMoreSpan, el.nextSibling);
    }

    _onClick (ev) {
        ev.preventDefault();
        const el = ev.currentTarget;
        const hiddenNode = el.parentNode.previousSibling;
        const text = el.dataset.text;
        hiddenNode.classList.toggle(classNames.hidden);
        hiddenNode.classList.toggle(classNames.visible);
        el.dataset.text = el.innerText;
        el.innerText = text;
    }
}

export default Excerpts;
