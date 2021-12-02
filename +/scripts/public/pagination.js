/** global: GLSR */

import Excerpts from './excerpts.js';

const config = {
    buttonSelector: 'button.glsr-button-loadmore',
    hideClass: 'glsr-hide',
    linkSelector: 'a.page-numbers',
    paginationSelector: '.glsr-pagination',
    reviewsSelector: '.glsr-reviews, [data-reviews]',
    scrollOffset: 16,
    scrollTime: 468,
}

class Pagination {
    constructor (wrapperEl) {
        this.reviewsEl = wrapperEl.querySelector(config.reviewsSelector);
        this.wrapperEl = wrapperEl;
        this.init()
    }

    data (el) {
        const paginationEl = el.closest(config.paginationSelector);
        if (!paginationEl) {
            console.error('Pagination config not found.');
            return false;
        }
        try {
            const dataset = JSON.parse(JSON.stringify(paginationEl.dataset));
            const data = {};
            for (var key of Object.keys(dataset)) {
                data[`${GLSR.nameprefix}[atts][${key}]`] = dataset[key];
            }
            data[`${GLSR.nameprefix}[_action]`] = 'fetch-paged-reviews';
            data[`${GLSR.nameprefix}[page]`] = el.dataset.page || '';
            data[`${GLSR.nameprefix}[schema]`] = false;
            data[`${GLSR.nameprefix}[url]`] = el.href || '';
            return data;
        } catch(e) {
            console.error('Invalid pagination config.');
            return false;
        }
    }

    init () {
        this.initLoadMore()
        this.initPagination()
    }

    initLoadMore () {
        const buttons = this.wrapperEl.querySelectorAll(config.buttonSelector);
        if (buttons.length) {
            [].forEach.call(buttons, button => {
                if (button.dataset.ready) return; // @hack only init once
                button.addEventListener('click', this.onLoadMore.bind(this, button));
                button.dataset.ready = true;
            })
        }
    }

    initPagination () {
        const links = this.wrapperEl.querySelectorAll(`${config.paginationSelector} ${config.linkSelector}`);
        if (links.length) {
            [].forEach.call(links, link => {
                if (link.dataset.ready) return; // @hack only init once
                link.addEventListener('click', this.onPaginate.bind(this, link));
                link.dataset.ready = true;
            })
        }
    }

    onLoadMore (el, ev) {
        const data = this.data(el);
        if (data) {
            el.ariaBusy = 'true';
            el.setAttribute('disabled', '');
            ev.preventDefault();
            GLSR.ajax.post(data, this.handleLoadMore.bind(this, el));
        }
    }

    onPaginate (el, ev) {
        const data = this.data(el);
        if (data) {
            this.wrapperEl.classList.add(config.hideClass);
            ev.preventDefault();
            GLSR.ajax.post(data, this.handlePagination.bind(this, el));
        }
    }

    handleLoadMore (el, response, success) {
        el.ariaBusy = 'false';
        el.removeAttribute('disabled');
        if (!success) {
            window.location = location;
            return;
        }
        [].forEach.call(this.wrapperEl.querySelectorAll(config.paginationSelector), el => {
            el.innerHTML = response.pagination;
        })
        this.reviewsEl.innerHTML += response.reviews;
        this.init();
        new Excerpts(this.reviewsEl);
        // GLSR.Event.trigger('site-reviews/pagination/handle', response, this);
    }

    handlePagination (el, response, success) {
        if (!success) {
            window.location = el.href; // reload page
            return;
        }
        [].forEach.call(this.wrapperEl.querySelectorAll(config.paginationSelector), el => {
            el.innerHTML = response.pagination;
        })
        this.reviewsEl.innerHTML = response.reviews;
        this.scrollToTop();
        this.init();
        this.wrapperEl.classList.remove(config.hideClass);
        if (GLSR.urlparameter) {
            window.history.replaceState(null, '', el.href); // don't add a new entry to browser History
        }
        new Excerpts(this.reviewsEl);
        GLSR.Event.trigger('site-reviews/pagination/handle', response, this);
    }

    scrollToTop () {
        let offset = config.scrollOffset;
        [].forEach.call(GLSR.ajaxpagination, selector => {
            const fixedEl = document.querySelector(selector);
            if (fixedEl && 'fixed' === window.getComputedStyle(fixedEl).getPropertyValue('position')) {
                offset = offset + fixedEl.clientHeight;
            }
        })
        const clientBounds = this.reviewsEl.getBoundingClientRect();
        const offsetTop = clientBounds.top - offset;
        if (offsetTop > 0) return; // if top is in view, don't scroll!
        this.scrollStep({
            endY: offsetTop,
            offset: window.pageYOffset,
            startTime: window.performance.now(),
            startY: this.reviewsEl.scrollTop,
        });
    }

    scrollStep (context) {
        const elapsed = Math.min(1, (window.performance.now() - context.startTime) / config.scrollTime);
        const easedValue = 0.5 * (1 - Math.cos(Math.PI * elapsed));
        const currentY = context.startY + (context.endY - context.startY) * easedValue;
        window.scroll(0, context.offset + currentY); // set the starting scoll position
        if (currentY !== context.endY) {
            window.requestAnimationFrame(this.scrollStep.bind(this, context));
        }
    }
}

export default () => {
    [].forEach.call(document.querySelectorAll(config.paginationSelector), el => {
        const wrapperEl = el.closest('.glsr');
        if (wrapperEl && (el.classList.contains('glsr-ajax-loadmore') || el.classList.contains('glsr-ajax-pagination'))) {
            new Pagination(wrapperEl);
        }
    })
}
