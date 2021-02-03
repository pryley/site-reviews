/** global: GLSR */

import Ajax from './public/ajax.js';
import Excerpts from './public/excerpts.js';
import Forms from './public/forms.js';
import Modal from './public/modal.js';
import Pagination from './public/pagination.js';

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = {};
}
window.GLSR.ajax = new Ajax();
window.GLSR.forms = [];

document.addEventListener('DOMContentLoaded', function () {
    // set text direction class
    const widgets = document.querySelectorAll('.glsr');
    for (let i = 0; i < widgets.length; i++) {
        let direction = window.getComputedStyle(widgets[i], null).getPropertyValue('direction');
        widgets[i].classList.add('glsr-' + direction);
    }
    window.GLSR.Forms = Forms;
    window.GLSR.Modal = Modal;
    new Forms();
    new Pagination();
    new Excerpts();
});

document.addEventListener('site-reviews/init/excerpts', () => {
    const classNames = {
        content: 'glsr-modal__content',
        review: 'glsr-modal__review',
    }
    window.GLSR.Modal.init({
        onClose: (modal, triggerEl, ev) => {
            modal.querySelector('.' + classNames.content).innerHTML = '';
            modal.classList.remove(classNames.review);
        },
        onOpen: (modal, triggerEl, ev) => {
            const reviewEl = triggerEl.closest('.glsr-review').cloneNode(true);
            modal.querySelector('.' + classNames.content).appendChild(reviewEl);
            modal.classList.add(classNames.review);
            document.dispatchEvent(new CustomEvent('site-reviews/after/modal', { detail: { modal, triggerEl, ev }}));
        },
        openTrigger: 'data-excerpt-trigger',
    })
});
