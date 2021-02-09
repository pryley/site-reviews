/** global: GLSR */

import Excerpts from './excerpts.js';

const config = {
    hideClass: 'glsr-hide',
    linkSelector: 'a.page-numbers',
    paginationSelector: '.glsr-ajax-pagination',
    reviewsSelector: '.glsr-reviews',
    scrollOffset: 16,
    scrollTime: 468,
}

class Pagination {
    constructor (wrapperEl) {
        this.links = [];
        this.reviewsEl = wrapperEl.querySelector(config.reviewsSelector);
        this.wrapperEl = wrapperEl;
        this.init()
    }

    init () {
        this.links = this.wrapperEl.querySelectorAll(`${config.paginationSelector} ${config.linkSelector}`);
        if (this.links.length) {
            [].forEach.call(this.links, link => {
                if (link.dataset.ready) return; // @hack only init once
                link.addEventListener('click', this.onClick.bind(this, link));
                link.dataset.ready = true;
            })
        }
    }

    onClick (el, ev) {
        const paginationEl = el.closest(config.paginationSelector);
        if (!paginationEl) {
            console.log('pagination config not found.');
            return;
        }
        const data = {};
        for (var key of Object.keys(paginationEl.dataset)) {
            var value = paginationEl.dataset[key];
            try {
                var parsedValue = JSON.parse(value);
                value = parsedValue;
            } catch(e) {}
            data[`${GLSR.nameprefix}[atts][${key}]`] = value;
        }
        data[`${GLSR.nameprefix}[_action]`] = 'fetch-paged-reviews';
        data[`${GLSR.nameprefix}[page]`] = ev.currentTarget.dataset.page || '';
        data[`${GLSR.nameprefix}[url]`] = ev.currentTarget.href || '';
        this.wrapperEl.classList.add(config.hideClass);
        // this.reviewsEl.classList.add(config.hideClass);
        // [].forEach.call(this.wrapperEl.querySelectorAll(config.paginationSelector), el => {
        //     el.classList.add(config.hideClass);
        // })
        ev.preventDefault();
        GLSR.ajax.post(data, this.handleResponse.bind(this, ev.currentTarget.href));
    }

    handleResponse (location, response, success) {
        if (!success) {
            window.location = location;
            return;
        }
        [].forEach.call(this.wrapperEl.querySelectorAll(config.paginationSelector), el => {
            el.innerHTML = response.pagination;
            // el.classList.remove(config.hideClass);
        })
        this.reviewsEl.innerHTML = response.reviews;
        // this.reviewsEl.classList.remove(config.hideClass);
        this.scrollToTop();
        this.init();
        this.wrapperEl.classList.remove(config.hideClass);
        if (GLSR.urlparameter) {
            window.history.pushState(null, '', location);
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
        console.log(el)
        if (wrapperEl) {
            console.log(wrapperEl)
            new Pagination(wrapperEl);
        }
    })
}
