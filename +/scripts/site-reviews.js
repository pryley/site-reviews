import Ajax from '@/public/ajax.js';
import Event from '@/public/event.js';
import Excerpts from '@/public/excerpts.js';
import Form from '@/public/form.js';
import Modal from '@/public/modal.js';
import Pagination from '@/public/pagination.js';
import dom from '@/public/dom.js';
import { debounce, fadeIn, fadeOut, isEmpty, parseJson, throttle } from '@/public/helpers.js';

const events = {
    blockForm: 'block:site-reviews/form',
    blockReview: 'block:site-reviews/review',
    blockReviews: 'block:site-reviews/reviews',
    blockSummary: 'block:site-reviews/summary',
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
        onOpen: (Modal) => {
            const triggerRoot = Modal.trigger.closest('.glsr');
            const baseEl   = triggerRoot.cloneNode(true);
            const reviewEl = Modal.trigger.closest('.glsr-review').cloneNode(true);
            // Ensure the entire review is visible
            reviewEl.querySelectorAll('[data-expanded="false"]').forEach(el => el.dataset.expanded = 'true');
            // Clean up cloned elements
            reviewEl.removeAttribute('id');
            baseEl.innerHTML = '';
            baseEl.removeAttribute('id');
            baseEl.appendChild(reviewEl);
            // Append directly or with parent-class wrapper
            const needsWrapper = GLSR.modal_wrapped_by.includes(baseEl.dataset.from);
            const appendEl = needsWrapper
                ? dom('div', {
                    class: triggerRoot.parentElement.className,
                    id: triggerRoot.parentElement.parentElement.id,
                    style: triggerRoot.parentElement.style.cssText,
                }, baseEl)
                : baseEl;
            Modal.dom.content.appendChild(appendEl);
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

const initReview = () => {
    const url = new URL(location.href);
    const params = Object.fromEntries(url.searchParams);
    if (!params.review_id) return;
    const action = params.verified ? 'verified' : 'approved';
    const requestKeys = ['form', 'review_id', 'theme', 'verified'];
    const request = Object.fromEntries(requestKeys.filter(k => k in params).map(k => [k, params[k]]));
    requestKeys.forEach(k => url.searchParams.delete(k));
    history.replaceState({}, '', url);
    GLSR.ajax.post(GLSR.ajax.data(`${action}-review`, request), (response, success) => {
        if (!success) {
            return console.error({ request, response })
        }
        GLSR.Modal.open(`glsr-modal-${action}`, {
            onOpen: (Modal) => {
                Modal.content(response.review, response.attributes)
                Modal.dom.content.querySelectorAll('[data-expanded="false"]').forEach(el => el.dataset.expanded = 'true')
                if (response.message) {
                    Modal.footer(`<p style="margin:0;padding:0;">${response.message}</p>`);
                }
            },
        })
    })
}

const initEvents = () => {
    Event.on(events.blockReview, initExcerpts)
    Event.on(events.blockReviews, initExcerpts)
    Event.on(events.blockForm, initForms)
    Event.on(events.excerpts, initExcerpts)
    Event.on(events.forms, initForms)
    Event.on(events.modal, initModal)
    Event.on(events.pagination, initPagination)
    Event.on(events.init, initPlugin)
    Event.on('site-reviews/pagination/handle', (response, pagination) => {
        // Modal init event is triggered in excerpts
        Event.trigger(events.excerpts, pagination.wrapperEl)
    })
}

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = {};
}
window.GLSR.ajax = Ajax;
window.GLSR.forms = [];
window.GLSR.pagination = [];
window.GLSR.Event = Event;
window.GLSR.Modal = Modal;
window.GLSR.Utils = { debounce, dom, fadeIn, fadeOut, isEmpty, parseJson, throttle };

window.GLSR_init = (event, ...args) => {
    if (0 === Object.keys(Event.events).length) {
        initEvents()
    }
    Event.trigger((event || events.init), ...args)
};

document.addEventListener('DOMContentLoaded', () => {
    initEvents()
    // for some reason, querySelectorAll return double the results in Firefox without this timeout...
    setTimeout(() => Event.trigger(events.init), 5)
    setTimeout(() => initReview(), 10)
})
