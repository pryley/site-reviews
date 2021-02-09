import Ajax from './public/ajax.js';
import Event from './public/event.js';
import Excerpts from './public/excerpts.js';
import Forms from './public/forms.js';
import Modal from './public/modal.js';
import Pagination from './public/pagination.js';

const initModal = () => {
    const classNames = {
        content: 'glsr-modal__content',
        review: 'glsr-modal__review',
    }
    window.GLSR.Modal.init({
        onClose: (modal, triggerEl, ev) => {
            modal.querySelector('.' + classNames.content).innerHTML = '';
            modal.classList.remove(classNames.review);
            GLSR.Event.trigger('site-reviews/modal/close', modal, triggerEl, ev)
        },
        onOpen: (modal, triggerEl, ev) => {
            const reviewEl = triggerEl.closest('.glsr-review').cloneNode(true);
            modal.querySelector('.' + classNames.content).appendChild(reviewEl);
            modal.classList.add(classNames.review);
            GLSR.Event.trigger('site-reviews/modal/open', modal, triggerEl, ev)
        },
        openTrigger: 'data-excerpt-trigger',
    })
}

const initPlugin = () => {
    // set text direction
    [].forEach.call(document.querySelectorAll('.glsr'), el => {
        const direction = 'glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction');
        el.classList.add(direction);
    })
    new Excerpts();
    new Forms();
    Pagination(); // @todo only run once
}

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = {};
}
window.GLSR.ajax = new Ajax();
window.GLSR.forms = [];
window.GLSR.Event = Event;
window.GLSR.Forms = Forms;
window.GLSR.Modal = Modal;

Event.on('site-reviews/init', initPlugin)
Event.on('site-reviews/excerpts/init', initModal) // @todo verify that triggering this multiple times does not create multiple Modal listeners

document.addEventListener('DOMContentLoaded', () => {
    Event.trigger('site-reviews/init')
});
