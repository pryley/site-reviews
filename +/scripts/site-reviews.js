import dom from '@/public/dom.js';
import Event, { adopt, listen, listeners } from '@/public/event.js';
import config from '@/public/config.js';
import Form, { Instance as FormInstance, retain as retainForms } from '@/public/form.js';
import lib from '@/public/lib.js';
import Modal, { Instance as ModalInstance, hooks as modalHooks } from '@/public/modal.js';
import registry, { defined, host } from '@/public/registry.js';
import report, { attach, expect } from '@/public/report.js';
import Request from '@/public/request.js';
import Review, { initModal as initReviewModal } from '@/public/review.js';
import Summary from '@/public/summary.js';
import { debounce, fadeIn, fadeOut, isEmpty, parseJson, selectText, throttle } from '@/public/helpers.js';

const initPlugin = (root = document) => {
    // every wrapper gets the class, including those of features that do not set it themselves
    const wrappers = [...root.querySelectorAll('.glsr')];
    if (root.matches?.('.glsr')) {
        wrappers.push(root)
    }
    wrappers.forEach(el => {
        const direction = 'glsr-' + window.getComputedStyle(el, null).getPropertyValue('direction');
        el.classList.add(direction)
    })
    Review.init(root)
    Form.init(root)
    Summary.init(root)
    Event.trigger('site-reviews/initialized', { root }) // this goes last!
}

// The review that a submission or a verification link redirects to.
const openReview = () => {
    const url = new URL(location.href);
    const params = Object.fromEntries(url.searchParams);
    if (!params.review_id) return;
    const requestKeys = ['form', 'review_id', 'theme', 'verified'];
    const values = Object.fromEntries(requestKeys.filter(k => k in params).map(k => [k, params[k]]));
    requestKeys.forEach(k => url.searchParams.delete(k));
    history.replaceState({}, '', url);
    Review.open(params.review_id, values)
}

let hasEvents = false;

// The listener is added late, so that those of other scripts run before the page is set up.
const initEvents = () => {
    if (hasEvents) return;
    hasEvents = true;
    listen('site-reviews/init', () => initPlugin())
}

window.GLSR.Event = adopt(window.GLSR.Event);
window.GLSR.Form = Form;
window.GLSR.Modal = Modal;
window.GLSR.Request = Request;
window.GLSR.Review = Review;
window.GLSR.Summary = Summary;
window.GLSR.lib = lib;
window.GLSR.registry = registry;
host('compat.public', { FormInstance, ModalInstance, initReviewModal, listen, modalHooks, report, retainForms })
host('debug.public', { attach, defined, listeners })
// debug mode for one page view
if (config.debug?.url && new URLSearchParams(location.search).has('glsr-debug')) {
    expect()
    registry.register({ 'debug.public': config.debug.url })
    registry.load('debug.public').catch(() => {})
}
window.GLSR.Util = { debounce, dom, fadeIn, fadeOut, isEmpty, parseJson, selectText, throttle };

window.GLSR_init = (first, ...args) => {
    initEvents()
    if (1 === first?.nodeType) {
        return initPlugin(first);
    }
    Event.trigger((first || 'site-reviews/init'), ...args)
};

document.addEventListener('DOMContentLoaded', () => {
    initEvents()
    // for some reason, querySelectorAll return double the results in Firefox without this timeout...
    setTimeout(() => Event.trigger('site-reviews/init'), 5)
    setTimeout(() => openReview(), 10)
})
