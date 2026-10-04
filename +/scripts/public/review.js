import config from '@/public/config.js';
import dom from '@/public/dom.js';
import Event from '@/public/event.js';
import Excerpts from '@/public/excerpts.js';
import feature, { direction, wrappers } from '@/public/feature.js';
import Modal from '@/public/modal.js';
import Pagination from '@/public/pagination.js';
import Request from '@/public/request.js';

class Review {
    constructor (el) {
        this.el = el;
        this.excerpts = new Excerpts(el);
        this.pagination = null;
    }

    destroy () {
        if (this.pagination) {
            this.pagination.destroy()
            this.pagination = null;
        }
    }

    init () {
        direction(this.el)
        this.excerpts.init()
        this._initPagination()
    }

    _initPagination () {
        this.destroy()
        const paginationEl = this.el.querySelector('.glsr-pagination');
        if (paginationEl && ['glsr-ajax-loadmore', 'glsr-ajax-pagination'].some(name => paginationEl.classList.contains(name))) {
            this.pagination = new Pagination(this.el, paginationEl, this._onPaginated.bind(this));
            this.pagination.init()
        }
    }

    _onPaginated (response) {
        this.excerpts.init()
        initModal()
        Event.trigger('site-reviews/review/paginated', { response, review: this })
        Event.trigger('site-reviews/review/initialized', { instances: [this], root: this.el })
    }
}

const { module } = feature(root => wrappers(root, '.glsr-reviews-wrap, .glsr-review'), el => new Review(el));

const init = module.init;

const initModal = () => {
    Modal.init('glsr-modal-review', {
        onOpen: (modal) => {
            const triggerRoot = modal.trigger.closest('.glsr');
            const baseEl   = triggerRoot.cloneNode(true);
            const reviewEl = modal.trigger.closest('.glsr-review').cloneNode(true);
            // Ensure the entire review is visible
            reviewEl.querySelectorAll('[data-expanded="false"]').forEach(el => el.dataset.expanded = 'true');
            // Clean up cloned elements
            reviewEl.removeAttribute('id');
            baseEl.innerHTML = '';
            baseEl.removeAttribute('id');
            baseEl.appendChild(reviewEl);
            // Append directly or with parent-class wrapper
            const needsWrapper = config.modal.wrappedBy.includes(baseEl.dataset.from);
            const appendEl = needsWrapper
                ? dom('div', {
                    class: triggerRoot.parentElement.className,
                    id: triggerRoot.parentElement.parentElement.id,
                    style: triggerRoot.parentElement.style.cssText,
                }, baseEl)
                : baseEl;
            modal.content(appendEl);
        },
    })
}

const paginations = () => module.instances.map(instance => instance.pagination).filter(Boolean);

module.init = (root = document) => {
    const instances = init(root);
    initModal()
    Event.trigger('site-reviews/review/initialized', { instances, root })
    return instances;
}

module.excerpts = {
    init: (el) => new Excerpts(el).init(),
};

module.open = (id, values = {}) => Request.review(id, values).then(({ data: response, success }) => {
    if (!success) {
        return console.error({ values, response })
    }
    Modal.open(`glsr-modal-${values.verified ? 'verified' : 'approved'}`, {
        onOpen: (modal) => {
            const contentEl = modal.content(response.review, response.attributes);
            contentEl.querySelectorAll('[data-expanded="false"]').forEach(el => el.dataset.expanded = 'true')
            if (response.message) {
                modal.footer(`<p style="margin:0;padding:0;">${response.message}</p>`);
            }
        },
    })
});

module.pagination = {
    find: (el) => paginations().find(pagination => [pagination.wrapperEl, pagination.paginationEl].includes(el)) || null,
    get instances () {
        return Object.freeze(paginations());
    },
};

export { initModal }

export default module
