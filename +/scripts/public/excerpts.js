/** global: GLSR */

const config = {
    hiddenClass: 'glsr-hidden',
    hiddenTextSelector: '.glsr-hidden-text',
    readMoreClass: 'glsr-read-more',
    visibleClass: 'glsr-visible',
}

class Excerpts {
    constructor (el) {
        const excerpts = (el || document).querySelectorAll(config.hiddenTextSelector);
        [].forEach.call(excerpts, el => this.init(el));
        GLSR.Event.trigger('site-reviews/excerpts/init', el);
    }

    init (el) {
        if (el.querySelector('.' + config.readMoreClass)) return; // only init once
        const trigger = el.dataset.trigger;
        const readMoreSpan = document.createElement('span');
        const readmoreLink = document.createElement('a');
        readmoreLink.setAttribute('href', '#');
        readmoreLink.innerHTML = el.dataset.showMore;
        if ('excerpt' === trigger) { // don't trigger for modals
            readmoreLink.addEventListener('click', this.onClick.bind(this));
            // we can't use dataset until the node has been inserted in the DOM
            readmoreLink.setAttribute('data-text', el.dataset.showLess);
        }
        if ('modal' === trigger) {
            // we can't use dataset until the node has been inserted in the DOM
            readmoreLink.setAttribute('data-excerpt-trigger', 'glsr-modal');
        }
        readMoreSpan.setAttribute('class', config.readMoreClass);
        readMoreSpan.appendChild(readmoreLink);
        el.parentNode.insertBefore(readMoreSpan, el.nextSibling);
    }

    onClick (ev) {
        ev.preventDefault();
        const el = ev.currentTarget;
        const hiddenNode = el.parentNode.previousSibling;
        const text = el.dataset.text;
        hiddenNode.classList.toggle(config.hiddenClass);
        hiddenNode.classList.toggle(config.visibleClass);
        el.dataset.text = el.innerText;
        el.innerText = text;
    }
}

export default Excerpts;
