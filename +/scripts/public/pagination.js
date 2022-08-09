import Button from '@/public/button.js';

const classNames = {
    hide: 'glsr-hide',
}

const config = {
    scrollOffset: 16,
    scrollTime: 468,
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
    constructor (wrapperEl, paginationEl) {
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
            if (data && nextLink && 2 === +nextLink.dataset.page && GLSR.urlparameter) { // window loaded page 1
                window.history.replaceState(data, '', window.location)
            }
        }
    }

    _data (el) {
        try {
            const dataset = JSON.parse(JSON.stringify(this.paginationEl.dataset));
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
            data[`${GLSR.nameprefix}[url]`] = el.href || location.href;
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
        this.destroy()
        this.paginationEl.innerHTML = response.pagination;
        this.reviewsEl.insertAdjacentHTML('beforeend', response.reviews)
        this.init()
        GLSR.Event.trigger('site-reviews/pagination/handle', response, this)
    }

    _handlePagination (linkEl, request, response, success) {
        if (!success) {
            window.location = linkEl.href; // reload page
            return
        }
        this._paginate(response)
        if (GLSR.urlparameter) {
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
            GLSR.ajax.post(data, this._handleLoadMore.bind(this, button, data))
        }
    }

    _onPaginate (ev) {
        const el = ev.currentTarget;
        const data = this._data(el);
        if (data) {
            this._loading()
            ev.preventDefault()
            GLSR.ajax.post(data, this._handlePagination.bind(this, el, data))
        }
    }

    _onPopstate (ev) {
        GLSR.Event.trigger('site-reviews/pagination/popstate', ev, this)
        if (ev.state && ev.state[`${GLSR.nameprefix}[_action]`]) {
            this._loading()
            GLSR.ajax.post(ev.state, this._handlePopstate.bind(this, ev.state))
        }
    }

    _paginate (response) {
        this.destroy()
        this.paginationEl.innerHTML = response.pagination;
        this.reviewsEl.innerHTML = response.reviews;
        this.init()
        this._scrollToTop()
        this._loaded()
        GLSR.Event.trigger('site-reviews/pagination/handle', response, this)
    }

    _scrollStep (context) {
        const elapsed = Math.min(1, (window.performance.now() - context.startTime) / config.scrollTime);
        const easedValue = 0.5 * (1 - Math.cos(Math.PI * elapsed));
        const currentY = context.startY + (context.endY - context.startY) * easedValue;
        window.scroll(0, context.offset + currentY) // set the starting scoll position
        if (currentY !== context.endY) {
            window.requestAnimationFrame(this._scrollStep.bind(this, context))
        }
    }

    _scrollToTop () {
        let offset = config.scrollOffset;
        [].forEach.call(GLSR.ajaxpagination, selector => {
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
