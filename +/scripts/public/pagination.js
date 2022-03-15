/** global: GLSR */

import Excerpts from './excerpts.js';

const config = {
    scrollOffset: 16,
    scrollTime: 468,
}

const selector = {
    button: 'button.glsr-button-loadmore',
    hide: 'glsr-hide',
    link: 'a.page-numbers',
    pagination: '.glsr-pagination',
    reviews: '.glsr-reviews, [data-reviews]',
}

class Pagination {
    constructor (wrapperEl) {
        this.reviewsEl = wrapperEl.querySelector(selector.reviews);
        this.wrapperEl = wrapperEl;
        this.init()
    }

    data (el, href) {
        const paginationEl = el.closest(selector.pagination);
        if (!paginationEl) {
            console.error('Pagination config not found.');
            return false;
        }
        try {
            const dataset = JSON.parse(JSON.stringify(paginationEl.dataset));
            const data = {};
            for (var key of Object.keys(dataset)) {
                let value;
                try {
                    value = JSON.parse(dataset[key]);
                } catch(e) {
                    value = dataset[key];
                }
                data[`${GLSR.nameprefix}[atts][${key}]`] = value;
            }
            data[`${GLSR.nameprefix}[_action]`] = 'fetch-paged-reviews';
            data[`${GLSR.nameprefix}[page]`] = el.dataset.page || 1;
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
        if (!GLSR.state.popstate) {
            window.addEventListener('popstate', this.onPopstate.bind(this));
            GLSR.state.popstate = true;
        }
    }

    initLoadMore () {
        const buttons = this.wrapperEl.querySelectorAll(selector.button);
        if (buttons.length) {
            [].forEach.call(buttons, button => {
                if (button.dataset.ready) return; // @hack only init once
                button.addEventListener('click', this.onLoadMore.bind(this, button));
                button.dataset.ready = true;
            })
        }
    }

    initPagination () {
        const links = this.wrapperEl.querySelectorAll(`${selector.pagination} ${selector.link}`);
        if (links.length) {
            [].forEach.call(links, link => {
                if (link.dataset.ready) return; // @hack only init once
                link.addEventListener('click', this.onPaginate.bind(this, link));
                link.dataset.ready = true;
            })
            const current = this.wrapperEl.querySelector(`${selector.pagination} .current`);
            if (current) {
                const data = this.data(current);
                const nextLink = current.nextElementSibling;
                if (data && nextLink && 2 === +nextLink.dataset.page && GLSR.urlparameter) { // window loaded page 1
                    window.history.replaceState(data, '', window.location);
                }
            }
        }
    }

    onLoadMore (el, ev) {
        const data = this.data(el);
        if (data) {
            el.ariaBusy = 'true';
            el.setAttribute('disabled', '');
            ev.preventDefault();
            GLSR.ajax.post(data, this.handleLoadMore.bind(this, el, data));
        }
    }

    onPaginate (el, ev) {
        const data = this.data(el);
        if (data) {
            this.wrapperEl.classList.add(selector.hide);
            ev.preventDefault();
            GLSR.ajax.post(data, this.handlePagination.bind(this, el, data));
        }
    }

    onPopstate (ev) {
        GLSR.Event.trigger('site-reviews/pagination/popstate', ev, this);
        if (ev.state && ev.state[`${GLSR.nameprefix}[_action]`]) {
            this.wrapperEl.classList.add(selector.hide);
            GLSR.ajax.post(ev.state, this.handlePopstate.bind(this, ev.state));
        }
    }

    handleLoadMore (buttonEl, request, response, success) {
        buttonEl.ariaBusy = 'false';
        buttonEl.removeAttribute('disabled');
        if (!success) {
            window.location = location;
            return;
        }
        [].forEach.call(this.wrapperEl.querySelectorAll(selector.pagination), el => {
            el.innerHTML = response.pagination;
        })
        this.reviewsEl.insertAdjacentHTML('beforeend', response.reviews);
        this.init();
        new Excerpts(this.reviewsEl);
    }

    handlePagination (linkEl, request, response, success) {
        // console.info(request);
        if (!success) {
            window.location = linkEl.href; // reload page
            return;
        }
        this.paginate(response)
        if (GLSR.urlparameter) {
            window.history.pushState(request, '', linkEl.href); // add a new entry to browser History
        }
    }

    handlePopstate (request, response, success) {
        if (success) {
            this.paginate(response)
            return;
        }
        console.error(response);
    }

    paginate (response) {
        [].forEach.call(this.wrapperEl.querySelectorAll(selector.pagination), el => {
            el.innerHTML = response.pagination;
        })
        this.reviewsEl.innerHTML = response.reviews;
        this.scrollToTop();
        this.init();
        this.wrapperEl.classList.remove(selector.hide);
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
    [].forEach.call(document.querySelectorAll(selector.pagination), el => {
        const wrapperEl = el.closest('.glsr');
        if (wrapperEl && (el.classList.contains('glsr-ajax-loadmore') || el.classList.contains('glsr-ajax-pagination'))) {
            new Pagination(wrapperEl);
        }
    })
}
