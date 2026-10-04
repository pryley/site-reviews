import Button from '@/public/button.js';
import config from '@/public/config.js';
import report from '@/public/report.js';
import Request from '@/public/request.js';

const classNames = {
    hide: 'glsr-hide',
}

const scroll = {
    offset: 16,
    time: 468,
}

const loader = (el) => {
    const loadingText = el.dataset.loading;
    const text = el.innerText;
    const insert = () => {
        el.setAttribute('aria-busy', false);
        el.removeAttribute('disabled');
        el.innerHTML = text;
    }
    const remove = () => {
        el.setAttribute('aria-busy', true);
        el.setAttribute('disabled', '');
        el.innerHTML = '<span class="glsr-loading"></span>' + loadingText || text;
    }
    return { el, loading, loaded };
}

const selectors = {
    button: 'button.glsr-button-loadmore',
    link: '.glsr-pagination a[data-page]',
    pagination: '.glsr-pagination',
    reviews: '.glsr-reviews, [data-reviews]',
}

class Pagination {
    constructor (wrapperEl, paginationEl, onPaginated = () => {}) {
        this.events = {
            button: {
                click: this._onLoadMore.bind(this),
            },
            link: {
                click: this._onPaginate.bind(this),
            },
            window: {
                popstate: this._onPopstate.bind(this),
            },
        };
        this.onPaginated = onPaginated;
        this.paginationEl = paginationEl;
        this.reviewsEl = wrapperEl.querySelector(selectors.reviews);
        this.wrapperEl = wrapperEl;
    }

    destroy () {
        this._eventHandler('remove')
    }

    init () {
        this._eventHandler('add')
        const current = this.paginationEl.querySelector('.current');
        if (current) {
            const data = this._data(current);
            const nextLink = current.nextElementSibling;
            if (data && nextLink && 2 === +nextLink.dataset.page && config.pagination.urlParameter) { // window loaded page 1
                window.history.replaceState(data, '', window.location)
            }
        }
    }

    _data (el) {
        try {
            const dataset = JSON.parse(JSON.stringify(this.paginationEl.dataset));
            const data = {
                _action: 'fetch-paged-reviews', // identifies this plugin's history state
                atts: {},
                page: el.dataset.page || 1,
                schema: false,
                url: el.href || location.href,
            };
            for (var key of Object.keys(dataset)) {
                try {
                    data.atts[key] = JSON.parse(dataset[key]);
                } catch(e) {
                    data.atts[key] = dataset[key];
                }
            }
            return data;
        } catch(e) {
            console.error('Invalid pagination config.')
            return false;
        }
    }

    _eventHandler (action) {
        this._eventListener(window, action, this.events.window)
        this.wrapperEl.querySelectorAll(selectors.button).forEach(el => {
            this._eventListener(el, action, this.events.button)
        })
        this.wrapperEl.querySelectorAll(selectors.link).forEach(el => {
            this._eventListener(el, action, this.events.link)
        })
    }

    _eventListener (el, action, events) {
        Object.keys(events).forEach(event => el[action + 'EventListener'](event, events[event]))
    }

    _handleLoadMore (button, request, response, success) {
        if (!success) {
            window.location = location; // reload page
            return
        }
        button.loaded()
        const page = Number(button.el.dataset.page) + 1;
        button.el.dataset.page = page;
        // The dataset page is the NEXT page to fetch, so the button
        // has work left while that page still exists.
        if (response.max_num_pages < page) {
            this.paginationEl.innerHTML = '';
        }
        this.reviewsEl.insertAdjacentHTML('beforeend', response.reviews)
        report('pagination', { id: this.wrapperEl.id, page: request.page, url: request.url })
        this.onPaginated(response)
    }

    _handlePagination (linkEl, request, response, success) {
        if (!success) {
            window.location = linkEl.href; // reload page
            return
        }
        this._paginate(response)
        report('pagination', { id: this.wrapperEl.id, page: request.page, url: request.url })
        if (config.pagination.urlParameter) {
            window.history.pushState(request, '', linkEl.href) // add a new entry to browser History
        }
    }

    _handlePopstate (request, response, success) {
        if (success) {
            this._paginate(response)
        } else {
            console.error(response)
        }
    }

    _loaded () {
        const loaderEl = this.paginationEl.querySelector('.glsr-spinner');
        if (loaderEl) {
            this.paginationEl.removeChild(loaderEl)
        }
        this.wrapperEl.classList.remove(classNames.hide)
    }

    _loading () {
        this.wrapperEl.classList.add(classNames.hide)
        this.paginationEl.insertAdjacentHTML('beforeend', '<div class="glsr-spinner"></div>')
    }

    _onLoadMore (ev) {
        const el = ev.currentTarget;
        const data = this._data(el);
        if (data) {
            const button = Button(el);
            button.loading()
            ev.preventDefault()
            Request.pagedReviews(data).then(result => this._handleLoadMore(button, data, result.data, result.success))
        }
    }

    _onPaginate (ev) {
        const el = ev.currentTarget;
        const data = this._data(el);
        if (data) {
            this._loading()
            ev.preventDefault()
            Request.pagedReviews(data).then(result => this._handlePagination(el, data, result.data, result.success))
        }
    }

    _onPopstate (ev) {
        if (ev.state && 'fetch-paged-reviews' === ev.state._action) {
            this._loading()
            Request.pagedReviews(ev.state).then(result => this._handlePopstate(ev.state, result.data, result.success))
        }
    }

    _paginate (response) {
        this.destroy()
        this.paginationEl.innerHTML = response.pagination;
        this.reviewsEl.innerHTML = response.reviews;
        this.init()
        this._scrollToTop()
        this._loaded()
        this.onPaginated(response)
    }

    _scrollStep (context) {
        const elapsed = Math.min(1, (window.performance.now() - context.startTime) / scroll.time);
        const easedValue = 0.5 * (1 - Math.cos(Math.PI * elapsed));
        const currentY = context.startY + (context.endY - context.startY) * easedValue;
        window.scroll(0, context.offset + currentY) // set the starting scoll position
        if (currentY !== context.endY) {
            window.requestAnimationFrame(this._scrollStep.bind(this, context))
        }
    }

    _scrollToTop () {
        let offset = scroll.offset;
        [].forEach.call(config.pagination.fixed, selector => {
            const fixedEl = document.querySelector(selector);
            if (fixedEl && 'fixed' === window.getComputedStyle(fixedEl).getPropertyValue('position')) {
                offset = offset + fixedEl.clientHeight;
            }
        })
        const clientBounds = this.reviewsEl.getBoundingClientRect();
        const offsetTop = clientBounds.top - offset;
        if (offsetTop > 0) return; // if top is in view, don't scroll
        this._scrollStep({
            endY: offsetTop,
            offset: window.pageYOffset,
            startTime: window.performance.now(),
            startY: this.reviewsEl.scrollTop,
        })
    }
}

export default Pagination;
