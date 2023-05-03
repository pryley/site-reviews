import Ajax from './public/ajax.js';
import Event from './public/event.js';
import Excerpts from './public/excerpts.js';
import Form from './public/form.js';
import Modal from './public/modal.js';
import Pagination from './public/pagination.js';
import dom from './public/dom.js';
import { debounce } from './public/helpers.js';

const events = {
    excerpts: 'site-reviews/excerpts/init',
    forms: 'site-reviews/forms/init',
    init: 'site-reviews/init',
    loaded: 'site-reviews/loaded',
    modal: 'site-reviews/modal/init',
    pagination: 'site-reviews/pagination/init',
};

const initExcerpts = (el) => {
    new Excerpts(el)
    Event.trigger(events.modal)
}

const initForms = () => {
    // remove all forms that are no longer on the page
    GLSR.forms = GLSR.forms.filter(form => !!form.form.closest('body'));
    document.querySelectorAll('form.glsr-review-form').forEach(formEl => {
        const buttonEl = formEl.querySelector('[type=submit]');
        if (buttonEl) {
            let form;
            let index = GLSR.forms.findIndex(form => form.form === formEl);
            if (-1 !== index) {
                form = GLSR.forms[index];
                form.destroy()
            } else {
                form = new Form(formEl, buttonEl);
                GLSR.forms.push(form)
            }
            form.init()
        }
    })
}

const initModal = () => {
    GLSR.Modal.init('glsr-modal-review', {
        onOpen: (modal) => {
            const baseEl = modal.trigger.closest('.glsr').cloneNode(true);
            const reviewEl = modal.trigger.closest('.glsr-review').cloneNode(true);
            reviewEl.querySelectorAll('[data-expanded="false"]').forEach(el => {
                el.dataset.expanded = 'true';
            })
            baseEl.innerHTML = '';
            baseEl.appendChild(reviewEl);
            modal.content.appendChild(baseEl);
        },
    })
}

const initPagination = () => {
    GLSR.pagination.forEach(pagination => pagination.destroy())
    GLSR.pagination = [];
    document.querySelectorAll('.glsr').forEach(el => {
        const paginationEl = el.querySelector('.glsr-pagination');
        if (paginationEl && (paginationEl.classList.contains('glsr-ajax-loadmore') || paginationEl.classList.contains('glsr-ajax-pagination'))) {
            const pagination = new Pagination(el, paginationEl);
            pagination.init()
            GLSR.pagination.push(pagination)
        }
    })
}

const initPlugin = () => {
    // set text direction
    document.querySelectorAll('.glsr').forEach(el => {
        const direction = 'glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction');
        el.classList.add(direction)
    })
    Event.trigger(events.excerpts)
    Event.trigger(events.forms)
    Event.trigger(events.pagination) // Modal init event is triggered in excerpts
    Event.trigger(events.loaded) // this goes last!
}

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = {};
}
window.GLSR.ajax = Ajax;
window.GLSR.forms = [];
window.GLSR.pagination = [];
window.GLSR.Event = Event;
window.GLSR.Modal = Modal;
window.GLSR.Utils = { debounce, dom };

Event.on(events.excerpts, initExcerpts)
Event.on(events.forms, initForms)
Event.on(events.modal, initModal)
Event.on(events.pagination, initPagination)
Event.on(events.init, initPlugin)

Event.on('site-reviews/pagination/handle', (response, pagination) => {
    Event.trigger(events.excerpts, pagination.wrapperEl)
    // Modal init event is triggered in excerpts
})

document.addEventListener('DOMContentLoaded', () => {
    // for some reason, querySelectorAll return double the results in Firefox without this timeout...
    setTimeout(() => Event.trigger(events.init), 5)
})
